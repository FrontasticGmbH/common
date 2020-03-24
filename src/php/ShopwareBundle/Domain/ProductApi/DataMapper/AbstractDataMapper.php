<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ShopwareBundle\Domain\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\QueryAwareDataMapperTrait;

abstract class AbstractDataMapper implements DataMapperInterface
{
    private const DATA_KEY = 'data';

    protected function extractData(array $resource): array
    {
        return $resource[self::DATA_KEY] ?? [];
    }
}
