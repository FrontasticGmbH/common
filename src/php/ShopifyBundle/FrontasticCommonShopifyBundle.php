<?php

namespace Frontastic\Common\ShopifyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FrontasticCommonShopifyBundle extends Bundle
{
    /**
     * Compatibility with QafooLabs/NoFrameworkBundle
     *
     * @return ?string
     */
    public function getParent()
    {
        return null;
    }
}
