<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;

interface ProductApi
{
    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery $query
     * @return \Frontastic\Common\ProductApiBundle\Domain\Category[]
     */
    public function getCategories(CategoryQuery $query): array;

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery $query
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductType[]
     */
    public function getProductTypes(ProductTypeQuery $query): array;

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery $query
     * @return \Frontastic\Common\ProductApiBundle\Domain\Product
     */
    public function getProduct(ProductQuery $query): ?Product;

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery $query
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result|\Frontastic\Common\ProductApiBundle\Domain\Product[]
     */
    public function query(ProductQuery $query): Result;

    /**
     * Get *dangerous* inner client
     *
     * This method exists to enable you to use features which are not yet part
     * of the abstraction layer.
     *
     * Be aware that any usage of this method might seriously hurt backwards
     * compatibility and the future abstractions might differ a lot from the
     * vendor provided abstraction.
     *
     * Use this with care for features necessary in your customer and talk with
     * Frontastic about provising an abstraction.
     *
     * @return mixed
     */
    public function getDangerousInnerClient();
}
