<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperTrait;

class ProductResultMapper extends AbstractDataMapper implements
    LocaleAwareDataMapperInterface,
    ProjectConfigApiAwareDataMapperInterface,
    QueryAwareDataMapperInterface
{
    use LocaleAwareDataMapperTrait,
        ProjectConfigApiAwareDataMapperTrait,
        QueryAwareDataMapperTrait;

    public const MAPPER_NAME = 'product-result';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\ProductMapper
     */
    private $productMapper;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\AggregationMapper
     */
    private $aggregationMapper;

    public function __construct(
        ProductMapper $productMapper,
        AggregationMapper $aggregationMapper
    ) {
        $this->productMapper = $productMapper;
        $this->aggregationMapper = $aggregationMapper;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $result = new Result();

        $productData = $this->extractElements($resource, $resource);

        $result->total = $resource['total'];
        $result->count = count($productData);
        $result->offset = $this->getQuery()->offset;
        $result->items = $this->mapProducts($productData);
        $result->facets = $this->mapAggregationsToFacets($this->extractAggregations($resource));

        $result->query = clone $this->getQuery();

        return $result;
    }

    private function mapProducts(array $productData): array
    {
        $products = [];
        foreach ($productData as $data) {
            $products[] = $this->mapDataToProduct($data);
        }

        return $products;
    }

    private function mapDataToProduct(array $productData): Product
    {
        return $this->productMapper
            ->setLocale($this->getLocale())
            ->setProjectConfigApi($this->getProjectConfigApi())
            ->setQuery($this->getQuery())
            ->map($productData);
    }

    private function mapAggregationsToFacets(array $aggregationData): array
    {
        return $this->aggregationMapper
            ->setQuery($this->getQuery())
            ->map($aggregationData);
    }
}
