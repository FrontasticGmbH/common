<?php

namespace Frontastic\Common\CartApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FrontasticCommonCartApiExtension extends \Symfony\Component\DependencyInjection\Extension\Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new Loader\XmlFileLoader($container, $locator);
        $loader->load('services.xml');
    }
}
