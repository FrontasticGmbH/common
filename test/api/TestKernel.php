<?php

namespace Frontastic\Common\ApiTests;

use Frontastic\Common\AccountApiBundle\FrontasticCommonAccountApiBundle;
use Frontastic\Common\CartApiBundle\FrontasticCommonCartApiBundle;
use Frontastic\Common\CoreBundle\FrontasticCommonCoreBundle;
use Frontastic\Common\Kernel;
use Frontastic\Common\ProductApiBundle\FrontasticCommonProductApiBundle;
use Frontastic\Common\ProjectApiBundle\FrontasticCommonProjectApiBundle;
use Frontastic\Common\SapCommerceCloudBundle\FrontasticCommonSapCommerceCloudBundle;
use Frontastic\Common\ShopifyBundle\FrontasticCommonShopifyBundle;
use Frontastic\Common\ShopwareBundle\FrontasticCommonShopwareBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;

class TestKernel extends Kernel
{
    public static function getBaseDir(): string
    {
        return __DIR__;
    }

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new SwiftmailerBundle(),

            new FrontasticCommonCoreBundle(),
            new FrontasticCommonAccountApiBundle(),
            new FrontasticCommonCartApiBundle(),
            new FrontasticCommonProductApiBundle(),
            new FrontasticCommonProjectApiBundle(),

            new FrontasticCommonSapCommerceCloudBundle(),
            new FrontasticCommonShopifyBundle(),
            new FrontasticCommonShopwareBundle(),
        ];
    }
}
