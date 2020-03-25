<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ShopwareBundle\Domain\Client;
use Frontastic\Common\ShopwareBundle\Domain\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\CategoryMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\ProductMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\ProductResultMapper;
use Frontastic\Common\ShopwareBundle\Domain\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\Search\Filter\Contains;
use Frontastic\Common\ShopwareBundle\Domain\Search\Filter\Equals;
use Frontastic\Common\ShopwareBundle\Domain\Search\Filter\EqualsAny;

class ShopwareProductApi implements ProductApi
{
    private const PRODUCT_SOURCE_FIELDS = [
        'id',
        'productNumber',
        'manufacturerNumber',
        'versionId',
        'name',
        'description',
        'categoryTree',
        'translations',
        'properties.id',
        'properties.groupId',
        'properties.name',
        'properties.translation',
        'tax',
        'stock',
        'children.id',
        'children.productNumber',
        'children.properties',
        'availableStock',
        'prices.quantityStart',
        'prices.price.gross',
        'manufacturer.id',
        'manufacturer.name',
        'manufacturer.translated',
        'cover.media.url',
    ];

    private const FIELDS_WITH_NAT_SORTING_ENABLED = [
        'productNumber',
    ];

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\Client
     */
    private $client;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator
     */
    private $localeCreator;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\DataMapperResolver
     */
    private $mapperResolver;

    public function __construct(Client $client, DataMapperResolver $mapperResolver, LocaleCreator $localeCreator)
    {
        $this->client = $client;
        $this->mapperResolver = $mapperResolver;
        $this->localeCreator = $localeCreator;
    }

    public function getCategories(CategoryQuery $query): array
    {
        $locale = $this->localeCreator->createLocaleFromString($query->locale);

        $parameters = [
            'offset' => $query->offset ?: 0,
            'limit' => 500, //$query->limit,
            'locale' => $locale->language,
        ];

        return $this->client->get('/category', $parameters)
            ->then(function ($response) use ($query) {
                return $this->mapResponse($response, $query, CategoryMapper::MAPPER_NAME);
            })
            ->wait();
    }

    public function getProductTypes(ProductTypeQuery $query): array
    {
        return [];
    }

    public function getProduct($query, string $mode = self::QUERY_SYNC): ?object
    {
        $identifier = $query->sku;
        $parameters = [];

        if ($identifier === null) {
            $identifier = $query->productId;
            $parameters = [
                'useNumberAsId' => true
            ];
        }

        $promise = $this->client
            ->get("/product/{$identifier}", $parameters)
            ->then(function ($response) use ($query) {
                return $this->mapResponse($response, $query, ProductMapper::MAPPER_NAME);
            });

        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }

        return $promise;
    }

    public function query(ProductQuery $query, string $mode = self::QUERY_SYNC): object
    {
        $body = [
            'offset' => $query->offset,
            'limit' => 10, //$query->limit,
            'filter' => [
                new Equals([
                    'field' => 'parentId',
                    'value' => null
                ])
            ],
            'associations' => [
                'children' => [
                    'associations' => [
                        'properties' => [],
                    ]
                ],
                'options' => [],
//                'categories' => [],
                'properties' => [],
                'manufacturer' => [],
            ],
            'source' => self::PRODUCT_SOURCE_FIELDS,
        ];

        $query->category = '44a73d002efb4ae1869959d862098b58';

        if (!empty($query->query)) {
            $body['term'] = $query->query;
        }

        if (!empty($query->productIds)) {
            $body['ids'] = $query->productIds;
        }

        if (!empty($query->skus)) {
            $body['filter'][] = new EqualsAny([
                'field' => 'categoryTree',
                'value' => $query->skus,
            ]);
        }

        if (!empty($query->category)) {
            $body['filter'][] = new Contains([
                'field' => 'categoryTree',
                'value' => $query->category,
            ]);
        }

        if ($query->sortAttributes) {
            $body['sort'] = array_map(
                static function (string $field, string $direction): array {
                    $result = [
                        'field' => $field,
                        'order' => ($direction === ProductQuery::SORT_ORDER_ASCENDING ? 'asc' : 'desc'),
                    ];

                    if (in_array($field, self::FIELDS_WITH_NAT_SORTING_ENABLED, true)) {
                        $result['naturalSorting'] = true;
                    }

                    return $result;
                },
                array_keys($query->sortAttributes),
                array_values($query->sortAttributes)
            );
        }

        $promise = $this->client
            ->post('/product', [], [], $body)
            ->then(function ($response) use ($query) {
                return $this->mapResponse($response, $query, ProductResultMapper::MAPPER_NAME);
            });

        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }

        return $promise;
    }

    public function getDangerousInnerClient(): Client
    {
        return $this->client;
    }

    private function mapResponse(array $response, ProductApi\Query $query, string $mapperName)
    {
        $mapper = $this->mapperResolver->getMapper($mapperName);

        if ($mapper instanceof QueryAwareDataMapperInterface) {
            $mapper->setQuery($query);
        }

        return $mapper->map($response);
    }
}
