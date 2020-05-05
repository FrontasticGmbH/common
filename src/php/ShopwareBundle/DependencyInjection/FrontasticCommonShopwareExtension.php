<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

class FrontasticCommonShopwareExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new Loader\XmlFileLoader($container, $locator);
        $loader->load('services.xml');
    }
}
