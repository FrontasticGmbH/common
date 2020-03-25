<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper;

use DateTimeImmutable;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ShopwareBundle\Domain\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\QueryAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\Slugger;

class ProductResultMapper extends AbstractDataMapper implements QueryAwareDataMapperInterface
{
    use QueryAwareDataMapperTrait;

    public const MAPPER_NAME = 'product-result';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\ProductMapper
     */
    private $productMapper;

    public function __construct(ProductMapper $productMapper)
    {
        $this->productMapper = $productMapper;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $resource)
    {
        $result = new Result();

        $result->total = $resource['total'];
        $result->count = 0;
        $result->offset = 0;

        $result->items = $this->mapProducts($this->extractData($resource));
//        $result->facets = $this->mapFacets();

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

    private function mapDataToProduct(array $productData)
    {
        $lastModified = $productData['updatedAt'] ?? null;

        return new Product([
            'productId' => (string)$productData['id'],
            'changed' => ($lastModified !== null) ? $this->parseDate($lastModified) : null,
            'version' => (string)$productData['versionId'],
            'name' => $productData['translated']['name'] ?? $productData['name'],
            'slug' => Slugger::slugify($productData['name']),
            'description' => $productData['translated']['description'] ?? $productData['description'],
            'categories' => $productData['categoryTree'],
            'variants' => [$this->mapDataToVariants($productData)],
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
        $variants = [];
        foreach ($productData['children'] as $variantData) {
            $variants[] = $this->mapDataToVariant($variantData);
        }
        return $variants;
    }

    private function mapDataToVariant(array $variantData): Variant
    {
//        list($price, $currency, $discountedPrice) = $this->dataToPrice($variantData);

//        $attributes = $this->dataToAttributes($variantData);
//        $groupId = $attributes['baseId'];

        $isOnStock = null;
        if (isset($variantData['availability'])) {
            $availability = $variantData['availability'];
            if (isset($availability['channels'])) {
                // Use first channel for now
                $availability = reset($availability['channels']);
            }
            $isOnStock = $availability['isOnStock'];
        }

        return new Variant([
            'id' => (string)$variantData['id'],
            'sku' => $variantData['productNumber'],
//            'groupId' => $groupId,
//            'price' => $price,
//            'discountedPrice' => $discountedPrice,
//            'discounts' => $variantData['discountedPrice']['includedDiscounts'] ?? [],
//            'attributes' => $attributes,
            'images' => [],
//            array_merge(
//                array_map(
//                    function (array $asset): string {
//                        return $asset['sources'][0]['uri'];
//                    },
//                    $variantData['assets']
//                ),
//                array_map(
//                    function (array $image): string {
//                        return $image['url'];
//                    },
//                    $variantData['images']
//                )
//            ),
            'isOnStock' => $isOnStock,
            'dangerousInnerVariant' => $this->mapDangerousInnerData($variantData),
        ]);
    }

    private function mapDangerousInnerData(array $productData): ?array
    {
        if ($this->getQuery()->loadDangerousInnerData) {
            return null;
        }

        return $productData;
    }

    private function parseDate(string $string): DateTimeImmutable
    {
        $formats = [
            'Y-m-d\TH:i:s.uP',
            \DateTimeInterface::RFC3339,
            \DateTimeInterface::RFC3339_EXTENDED,
        ];

        foreach ($formats as $format) {
            $date = DateTimeImmutable::createFromFormat($format, $string);
            if ($date !== false) {
                return $date;
            }
        }

        throw new \RuntimeException('Invalid date: ' . $string);
    }
}
