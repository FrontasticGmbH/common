<?php

namespace Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * This is an ProductSearchApi class which throws exceptions if used. It's purpose is
 * to have a placeholder for the Frontastic Next.js projects. Because if entries
 * for the different APIs are missing in project.yml, the API Hub is not working
 * anymore.
 */
class DummyProductSearchApi extends ProductSearchApiBase
{
    protected function queryImplementation(ProductQuery $query): PromiseInterface
    {
        throw new \Exception("ProductSearchApi is not available for Nextjs projects.");
    }

    protected function getSearchableAttributesImplementation(): PromiseInterface
    {
        throw new \Exception("ProductSearchApi is not available for Nextjs projects.");
    }

    public function getDangerousInnerClient(): Client
    {
        throw new \Exception("ProductSearchApi is not available for Nextjs projects.");
    }
}
