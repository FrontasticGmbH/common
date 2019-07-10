<?php

namespace Frontastic\Common\ProjectApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class DefaultProjectApiFactory implements ProjectApiFactory
{
    /**
     * @var ProductApiFactory
     */
    private $productApiFactory;

    public function __construct(ProductApiFactory $productApiFactory)
    {
        $this->productApiFactory = $productApiFactory;
    }

    public function factor(Project $project): ProjectApi
    {
        $productApi = $this->productApiFactory->factorFromConfiguration($project->configuration);

        // KN: Sorry :D
        while (method_exists($productApi, 'getAggregate')) {
            $productApi = $productApi->getAggregate();
        }

        if ($productApi instanceof ProductApi\Commercetools) {
            return new ProjectApi\Commercetools($productApi, $project->languages);
        }

        return new ProjectApi\Dummy();
    }
}
