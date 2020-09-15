<?php

namespace Frontastic\Common\ProjectApiBundle\Domain;

use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;

class ProductSearchProjectApi implements ProjectApi
{
    /** @var ProductSearchApi */
    private $productSearchApi;

    public function __construct(ProductSearchApi $productSearchApi)
    {
        $this->productSearchApi = $productSearchApi;
    }

    public function getSearchableAttributes(): array
    {
        return $this->productSearchApi->getSearchableAttributes()->wait();
    }
}
