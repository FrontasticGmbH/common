<?php

namespace Frontastic\Common\ProductSearchApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use GuzzleHttp\Promise\PromiseInterface;

use Frontastic\Common\ProjectApiBundle\Domain\Attribute;

interface ProductSearchApi
{
    /**
     * @return PromiseInterface A promise for Result of Products.
     */
    public function query(ProductQuery $query): PromiseInterface;

    /**
     * @return PromiseInterface A promise for an array of Attributes mapped by ID
     */
    public function getSearchableAttributes(): PromiseInterface;

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
