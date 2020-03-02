<?php

namespace Frontastic\Common\ApiTests;

use Frontastic\Common\CartApiBundle\FrontasticCommonCartApiBundle;
use Frontastic\Common\CoreBundle\FrontasticCommonCoreBundle;
use Frontastic\Common\Kernel;
use Frontastic\Common\ProductApiBundle\FrontasticCommonProductApiBundle;
use Frontastic\Common\ProjectApiBundle\FrontasticCommonProjectApiBundle;
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
            new FrontasticCommonProductApiBundle(),
            new FrontasticCommonProjectApiBundle(),
            new FrontasticCommonCartApiBundle(),
        ];
    }
}
