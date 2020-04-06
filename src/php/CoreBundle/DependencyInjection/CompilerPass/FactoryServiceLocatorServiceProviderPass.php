<?php declare(strict_types = 1);

namespace Frontastic\Common\CoreBundle\DependencyInjection\CompilerPass;

use Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FactoryServiceLocatorServiceProviderPass implements CompilerPassInterface
{
    private const FRONTASTIC_NAMESPACE = 'Frontastic\\';
    private const FACTORY_SERVICE_TAG = 'frontastic.common.api_integration';
    private const SERVICE_SUBSCRIBER_TAG = 'container.service_subscriber';

    public function process(ContainerBuilder $container): void
    {
        $factoryServiceLocatorDef = $container->getDefinition(FactoryServiceLocator::class);

        $taggedServiceIds = $container->findTaggedServiceIds(self::FACTORY_SERVICE_TAG);
        $aliasedServices = $this->getFrontasticAliases($container);

        foreach ($taggedServiceIds as $serviceId => $tags) {
            foreach ($aliasedServices as $aliasId => $alias) {
                if ((string)$alias === $serviceId) {
                    FactoryServiceLocator::addSubscribedService($aliasId);
                    $factoryServiceLocatorDef->addTag(self::SERVICE_SUBSCRIBER_TAG, ['id' => $aliasId]);
                }
            }
        }
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Alias[]
     */
    private function getFrontasticAliases(ContainerBuilder $container): array
    {
        return array_filter(
            $container->getAliases(),
            static function (string $key) {
                return strpos($key, self::FRONTASTIC_NAMESPACE) !== false;
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
