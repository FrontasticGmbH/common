<?php

namespace Frontastic\Common\SprykerBundle\BaseApi;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\SprykerBundle\Domain\Product\Expander\ProductExpanderInterface;

trait ProductExpandingTrait
{
    /**
     * @var array|\Frontastic\Common\SprykerBundle\Domain\Product\Expander\ProductExpanderInterface[]
     */
    private $expanders = [];

    /**
     * @param \Frontastic\Common\SprykerBundle\Domain\Product\Expander\ProductExpanderInterface $expander
     *
     * @return \Frontastic\Common\SprykerBundle\BaseApi\ProductExpandingTrait|self
     */
    public function registerProductExpander(ProductExpanderInterface $expander): self
    {
        $this->expanders[] = $expander;

        return $this;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product $product
     * @param array|\WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject[] $includedResources
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\Product
     */
    protected function expandProduct(Product $product, array $includedResources): Product
    {
        foreach ($this->expanders as $expander) {
            $expander->expand($product, $includedResources);
        }

        return $product;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product[] $productList
     * @param array $includedResources
     *
     * @return void
     */
    protected function expandProductList(array $productList, array $includedResources): void
    {
        foreach ($productList as $product) {
            $this->expandProduct($product, $includedResources);
        }
    }
}
