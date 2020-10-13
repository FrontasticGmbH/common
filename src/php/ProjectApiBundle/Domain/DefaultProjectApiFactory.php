<?php

namespace Frontastic\Common\ProjectApiBundle\Domain;

use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class DefaultProjectApiFactory implements ProjectApiFactory
{
    /** @var ProductSearchApiFactory */
    private $productSearchApiFactory;

    public function __construct(ProductSearchApiFactory $productSearchApiFactory)
    {
        $this->productSearchApiFactory = $productSearchApiFactory;
    }

    public function factor(Project $project): ProjectApi
    {
        $productSearchApi = $this->productSearchApiFactory->factor($project);
        return new ProductSearchProjectApi($productSearchApi);
    }
}
