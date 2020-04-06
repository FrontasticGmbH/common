<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

trait QueryAwareDataMapperTrait
{
    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query
     */
    private $query;

    public function setQuery(Query $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getQuery(): Query
    {
        return $this->query;
    }
}
