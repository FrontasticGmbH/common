<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain;

interface DataMapperInterface
{
    public function getName(): string;

    public function map(array $resource);
}
