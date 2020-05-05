<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Exception;

use RuntimeException;

class MapperNotFoundException extends RuntimeException
{
    public function __construct(string $mapperName)
    {
        parent::__construct(sprintf('Mapper not found by name: `%s`', $mapperName));
    }
}
