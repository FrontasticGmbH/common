<?php

namespace Frontastic\Common\ProductSearchApiBundle\Domain;

use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi\DummyProductSearchApi;

/**
 * This is an ProductSearchApiFactory class which throws exceptions if used.
 * It's purpose is to have a placeholder for the Frontastic Next.js projects.
 * Because if entries for the different APIs are missing in project.yml, the API
 * Hub is not working anymore.
 */
class DummyProductSearchApiFactory implements ProductSearchApiFactory
{
    public function factor(Project $project): ProductSearchApi
    {
        return new DummyProductSearchApi();
    }
}
