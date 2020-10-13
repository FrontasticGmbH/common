<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Mapper;

class GuestCartMapper extends AbstractCartMapper
{
    public const MAPPER_NAME = 'guest-cart';

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
        return 'guest-cart-items';
    }
}
