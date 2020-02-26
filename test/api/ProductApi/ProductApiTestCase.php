<?php

namespace Frontastic\Common\ApiTests\ProductApi;

use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class ProductApiTestCase extends FrontasticApiTestCase
{
    /**
     * @var array<string, ProductApi>
     */
    private $productApis = [];

    protected function productApiForProject(Project $project): ProductApi
    {
        $key = sprintf('%s_%s', $project->customer, $project->projectId);
        if (!array_key_exists($key, $this->productApis)) {
            $this->productApis[$key] = self::$container->get(ProductApiFactory::class)->factor($project);
        }

        return $this->productApis[$key];
    }
}
