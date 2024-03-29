<?php

namespace Frontastic\Common\ApiTests;

use Frontastic\Common\AccountApiBundle\FrontasticCommonAccountApiBundle;
use Frontastic\Common\AlgoliaBundle\FrontasticCommonAlgoliaBundle;
use Frontastic\Common\CartApiBundle\FrontasticCommonCartApiBundle;
use Frontastic\Common\ContentApiBundle\FrontasticCommonContentApiBundle;
use Frontastic\Common\CoreBundle\FrontasticCommonCoreBundle;
use Frontastic\Common\Kernel;
use Frontastic\Common\ProductApiBundle\FrontasticCommonProductApiBundle;
use Frontastic\Common\ProductSearchApiBundle\FrontasticCommonProductSearchApiBundle;
use Frontastic\Common\ProjectApiBundle\FrontasticCommonProjectApiBundle;
use Frontastic\Common\SapCommerceCloudBundle\FrontasticCommonSapCommerceCloudBundle;
use Frontastic\Common\FindologicBundle\FrontasticCommonFindologicBundle;
use Frontastic\Common\ShopifyBundle\FrontasticCommonShopifyBundle;
use Frontastic\Common\ShopwareBundle\FrontasticCommonShopwareBundle;
use Frontastic\Common\SprykerBundle\FrontasticCommonSprykerBundle;
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
            new FrontasticCommonContentApiBundle(),
            new FrontasticCommonProductApiBundle(),
            new FrontasticCommonProductSearchApiBundle(),
            new FrontasticCommonProjectApiBundle(),

            new FrontasticCommonSapCommerceCloudBundle(),
            new FrontasticCommonShopifyBundle(),
            new FrontasticCommonShopwareBundle(),
            new FrontasticCommonSprykerBundle(),
            new FrontasticCommonAlgoliaBundle(),
            new FrontasticCommonFindologicBundle(),
        ];
    }
}
