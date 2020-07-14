<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Request;

interface CartItemRequestDataInterface
{
    /**
     * @return string
     */
    public function encode(): string;
}
