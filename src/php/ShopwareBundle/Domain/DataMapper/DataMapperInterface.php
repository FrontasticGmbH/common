<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\DataMapper;

interface DataMapperInterface
{
    public function getName(): string;

    /**
     * @param array $resource
     *
     * @return mixed
     */
    public function map($resource);
}
