<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\SingleProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiBase;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * This is an ProductApi class which throws exceptions if used. It's purpose is
 * to have a placeholder for the Frontastic Next.js projects. Because if entries
 * for the different APIs are missing in project.yml, the API Hub is not working
 * anymore.
 */
class DummyProductApi extends ProductApiBase
{
    public function __construct(
        ProductSearchApi $productSearchApi
    ) {
        parent::__construct($productSearchApi);
    }

    protected function queryCategoriesImplementation(CategoryQuery $query): Result
    {
        throw $this->exception();
    }

    protected function getProductTypesImplementation(ProductTypeQuery $query): array
    {
        throw $this->exception();
    }

    protected function getProductImplementation(SingleProductQuery $query): PromiseInterface
    {
        throw $this->exception();
    }

    public function getDangerousInnerClient(): Client
    {
        throw $this->exception();
    }

    private function exception(): \Throwable
    {
        return new \Exception("ProductApi is not available for Nextjs projects.");
    }
}
