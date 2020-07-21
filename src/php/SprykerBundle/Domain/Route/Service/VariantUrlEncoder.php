<?php

namespace Frontastic\Common\SprykerBundle\Domain\Route\Service;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\SprykerBundle\Domain\Product\SprykerSlugger;
use Frontastic\Common\SprykerBundle\Domain\Route\RouteConstants;
use Frontastic\Common\CartApiBundle\Domain\LineItem;

class VariantUrlEncoder
{
    /**
     * @var \Frontastic\Common\SprykerBundle\Domain\Product\SprykerSlugger
     */
    private $slugger;

    /**
     * @param \Frontastic\Common\SprykerBundle\Domain\Product\SprykerSlugger $slugger
     */
    public function __construct(SprykerSlugger $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product $product
     * @param \Frontastic\Common\ProductApiBundle\Domain\Variant $variant
     *
     * @return string
     */
    public function encode(Product $product, Variant $variant): string
    {
        return $this->mapToSlug([
            RouteConstants::PLACEHOLDER_ID => $this->getId($variant),
            RouteConstants::PLACEHOLDER_NAME_SLUG => $this->getSlug($product, $variant),
        ]);
    }

    /**
     * @param array $map
     *
     * @return string
     */
    private function mapToSlug(array $map): string
    {
        $encoded = str_replace(
            array_keys($map),
            array_values($map),
            RouteConstants::URL_STRUCTURE_VARIANT
        );

        return $this->slugger::cleanSlug($encoded);
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Variant $variant
     *
     * @return string
     */
    private function getId(Variant $variant): string
    {
        return (string)$variant->sku;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product $product
     * @param \Frontastic\Common\ProductApiBundle\Domain\Variant $variant
     *
     * @return string
     */
    private function getSlug(Product $product, Variant $variant): string
    {
        $name = $variant->attributes['article_single_page_title']['value'] ?? $product->name;

        return $this->slugger::slugify($name);
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem\Variant $lineItem
     *
     * @return string
     */
    public function encodeLineItem(LineItem\Variant $lineItem): string
    {
        $name = $lineItem->variant->attributes['article_single_page_title']['value'] ?? $lineItem->name;
        $slug = $this->slugger::slugify($name);

        return $this->mapToSlug([
            RouteConstants::PLACEHOLDER_ID => $this->getId($lineItem->variant),
            RouteConstants::PLACEHOLDER_NAME_SLUG => $slug,
        ]);
    }

    /**
     * @param array $bundleData
     *
     * @return string
     */
    public function encodeBundleMainProduct(array $bundleData): string
    {
        return $this->mapToSlug([
            RouteConstants::PLACEHOLDER_ID => $bundleData['sku'],
            RouteConstants::PLACEHOLDER_NAME_SLUG => $this->slugger::slugify($bundleData['name']),
        ]);
    }
}
