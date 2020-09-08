<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Mapper;

class CustomerCartMapper extends AbstractCartMapper
{
    public const MAPPER_NAME = 'customer-cart';

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::MAPPER_NAME;
    }

    /**
     * @return string
     */
    protected function getRelationship(): string
    {
        return 'items';
    }
}
