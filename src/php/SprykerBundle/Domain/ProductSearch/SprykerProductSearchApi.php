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
use Frontastic\Common\SprykerBundle\Domain\Product\Expander\ProductVariantSkuExpander;
use Frontastic\Common\SprykerBundle\Domain\Product\Mapper\ProductResultMapper;
use Frontastic\Common\SprykerBundle\Domain\Product\SprykerProductApiExtendedConstants;
use Frontastic\Common\SprykerBundle\Domain\Project\Mapper\ProductSearchableAttributesMapper;
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

    /** @var string[] */
    private $projectLanguages;

    public function __construct(
        SprykerClientInterface $client,
        MapperResolver $mapperResolver,
        LocaleCreator $localeCreator,
        array $projectLanguages,
        array $queryResources = SprykerProductApiExtendedConstants::SPRYKER_PRODUCT_QUERY_RESOURCES
    ) {
        $this->client = $client;
        $this->mapperResolver = $mapperResolver;
        $this->localeCreator = $localeCreator;
        $this->projectLanguages = $projectLanguages;
        $this->queryResources = $queryResources;

        $this->extendNestedAttributes();
    }

    protected function extendNestedAttributes(): void
    {
        $this->registerProductExpander(new ProductVariantSkuExpander());
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
        $resources = [];
        try {
            $response = $this->client->get('/product-management-attributes');
            $resources  = $response->document()->primaryResources();
        } catch (\Exception $e) {
            // Endpoint not implemented
            if ($e->getCode() === 404) {
                // TODO: Log error
            }
        }

        $attributes = $this->mapperResolver
            ->getExtendedMapper(ProductSearchableAttributesMapper::MAPPER_NAME)
            ->mapResourceArray($resources);

        foreach ($attributes as &$attribute) {
            $attribute->label = $this->mapLocales($attribute->label);
            foreach ($attribute->values as &$value) {
                if (is_array($value)) {
                    $value = $this->mapLocales($value);
                }
            }
        }

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

    private function mapLocales(array $localizedStrings): array
    {
        $localizedResult = [];
        foreach ($this->projectLanguages as $language) {
            $locale = $this->localeCreator->createLocaleFromString($language);
            $localizedResult[$language] =
                $localizedStrings[$locale->language . '_' . $locale->country] ??
                (reset($localizedStrings) ?: '');
        }

        return $localizedResult;
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
