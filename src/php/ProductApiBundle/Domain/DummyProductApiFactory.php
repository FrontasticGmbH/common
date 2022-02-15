<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\DummyProductApi;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

/**
 * This is an ProductApiFactory class which throws exceptions if used. It's purpose is
 * to have a placeholder for the Frontastic Next.js projects. Because if entries
 * for the different APIs are missing in project.yml, the API Hub is not working
 * anymore.
 */
class DummyProductApiFactory implements ProductApiFactory
{
    /**
     * @var ProductSearchApiFactory
     */
    private $productSearchApiFactory;

    public function __construct(
        ProductSearchApiFactory $productSearchApiFactory
    ) {
        $this->productSearchApiFactory = $productSearchApiFactory;
    }

    public function factor(Project $project): ProductApi
    {
        return new DummyProductApi($this->productSearchApiFactory->factor($project));
    }
}
