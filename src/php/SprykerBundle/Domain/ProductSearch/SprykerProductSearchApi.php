<?php

namespace Frontastic\Common\SprykerBundle\Domain\ProductSearch;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\SprykerBundle\BaseApi\ProductExpandingTrait;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;
use Frontastic\Common\SprykerBundle\Domain\Product\CatalogSearchQuery;
use Frontastic\Common\SprykerBundle\Domain\Product\Mapper\ProductResultMapper;
use Frontastic\Common\SprykerBundle\Domain\Product\SprykerProductApiExtendedConstants;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use WoohooLabs\Yang\JsonApi\Response\JsonApiResponse;
use function GuzzleHttp\Promise\promise_for;

class SprykerProductSearchApi extends ProductSearchApiBase
{
    use ProductExpandingTrait;

    /** @var SprykerClientInterface */
    private $client;

    /** @var MapperResolver */
    private $mapperResolver;

    /** @var LocaleCreator */
    private $localeCreator;

    /** @var array */
    private $queryResources;

    public function __construct(
        SprykerClientInterface $client,
        MapperResolver $mapperResolver,
        LocaleCreator $localeCreator,
        array $queryResources = SprykerProductApiExtendedConstants::SPRYKER_PRODUCT_QUERY_RESOURCES
    ) {
        $this->client = $client;
        $this->mapperResolver = $mapperResolver;
        $this->localeCreator = $localeCreator;
        $this->queryResources = $queryResources;
    }

    protected function queryImplementation(ProductQuery $query): PromiseInterface
    {
        $searchQuery = CatalogSearchQuery::createFromProductQuery($query);

        $mapper = $this->mapperResolver->getMapper(ProductResultMapper::MAPPER_NAME);

        $response = $this->client
            ->get(
                $this->withIncludes("/catalog-search?{$searchQuery}", $this->queryResources),
                [],
                ProductApi::QUERY_ASYNC
            )
            ->then(function (JsonApiResponse $response) use ($mapper, $query): Result {
                $document = $response->document();
                $resources = $document->isSingleResourceDocument() ?
                    [$document->primaryResource()] :
                    $document->primaryResources();
                if ($document->hasAnyIncludedResources()) {
                    $resources = array_merge($resources, $document->includedResources());
                }

                $products = $mapper->mapResource($resources[0]);

                if (count($products->items) && count($resources)) {
                    $this->expandProductList($products->items, $resources);
                }

                $products->query = clone $query;

                return $products;
            })
            ->otherwise(function (\Throwable $exception) use ($query) {
                if ($exception instanceof RequestException && $exception->getCode() >= 500) {
                    return new Result([
                        'query' => clone $query,
                    ]);
                }
                throw $exception;
            });

        return $response;
    }

    protected function getSearchableAttributesImplementation(): PromiseInterface
    {
        $attributes = [];

        // @TODO: implement multi languages

        // @TODO: implement /product-searchable-attributes alternative from Spryker ocre

        // $response = $this->client->get('/product-searchable-attributes');

        // $attributes = $this->mapperResolver
        //    ->getExtendedMapper(ProductSearchableAttributesMapper::MAPPER_NAME)
        //    ->mapResourceArray($response->document()->primaryResources());

        // check if there are no attributes due to error or something, just return an empty result and don't add the
        // price attribute, as this will lead to disabling all other facets in backstage.
        // if (empty($attributes)) {
        //    return $attributes;
        // }

        return promise_for($this->addCustomAttributes($attributes));
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    private function withIncludes(string $url, array $includes = []): string
    {
        if (count($includes) === 0) {
            return $url;
        }

        $separator = (strpos($url, '?') === false) ? '?' : '&';
        $includesString = implode(',', $includes);

        return "{$url}{$separator}include={$includesString}";
    }

    private function addCustomAttributes(array $attributes): array
    {
        // Not included in attributes in Spryker
        $attributeId = 'price';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_MONEY,
            'label' => null, // Can we get the price label somehow?
        ]);

        $attributeId = 'listingPrices';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_MONEY,
        ]);

        $attributeId = 'categories.id';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_CATEGORY_ID,
        ]);

        return $attributes;
    }
}
