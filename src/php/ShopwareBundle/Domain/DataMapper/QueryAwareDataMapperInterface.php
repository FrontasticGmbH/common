<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\DataMapper;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

interface QueryAwareDataMapperInterface
{
    public function setQuery(Query $query);

    public function getQuery(): Query;
}
