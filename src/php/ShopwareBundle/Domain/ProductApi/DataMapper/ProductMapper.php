<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper;

use DateTimeImmutable;
use DateTimeInterface;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\ProductNotFoundException;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\Slugger;
use RuntimeException;

class ProductMapper extends AbstractDataMapper implements
    LocaleAwareDataMapperInterface,
    ProjectConfigApiAwareDataMapperInterface,
    QueryAwareDataMapperInterface
{
    use LocaleAwareDataMapperTrait,
        ProjectConfigApiAwareDataMapperTrait,
        QueryAwareDataMapperTrait;

    public const MAPPER_NAME = 'product';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\ProductVariantMapper
     */
    private $variantMapper;

    public function __construct(ProductVariantMapper $variantMapper)
    {
        $this->variantMapper = $variantMapper;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        // Support for list with single resources as well as direct single resource
        $productData = $this->extractData($resource, $resource);
        $productData = $productData[0] ?? $productData;
        if (empty($productData)) {
            return null;
        }

        $lastModified = $productData['updatedAt'] ?? null;

        $name = $this->resolveTranslatedValue($productData, 'name');

        return new Product([
            'productId' => $productData['parentId'] ?? (string)$productData['id'],
            'changed' => ($lastModified !== null) ? $this->parseDate($lastModified) : null,
            'version' => $productData['versionId'] ? (string)$productData['versionId'] : null,
            'name' => $name,
            'slug' => Slugger::slugify($name),
            'description' => strip_tags($this->resolveTranslatedValue($productData, 'description')),
            'categories' => $productData['categoryTree'] ?? null,
            'variants' => $this->mapDataToVariants($productData),
            'dangerousInnerProduct' => $this->mapDangerousInnerData($productData),
        ]);
    }

    /**
     * @param array $productData
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\Variant[]
     */
    public function mapDataToVariants(array $productData): array
    {
        if (empty($productData['children'])) {
            return [$this->mapDataToVariant($productData)];
        }

        $variants = [];
        foreach ($productData['children'] as $variantData) {
            $variants[] = $this->mapDataToVariant($variantData);
        }
        return $variants;
    }

    private function mapDataToVariant(array $variantData): Variant
    {
        return $this->variantMapper
            ->setLocale($this->getLocale())
            ->setProjectConfigApi($this->getProjectConfigApi())
            ->setQuery($this->getQuery())
            ->map($variantData);
    }

    private function parseDate(string $string): DateTimeImmutable
    {
        $formats = [
            'Y-m-d\TH:i:s.uP',
            DateTimeInterface::RFC3339,
            DateTimeInterface::RFC3339_EXTENDED,
        ];

        foreach ($formats as $format) {
            $date = DateTimeImmutable::createFromFormat($format, $string);
            if ($date !== false) {
                return $date;
            }
        }

        throw new RuntimeException('Invalid date: ' . $string);
    }
}
