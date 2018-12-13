<?php

namespace Frontastic\Common\CoreBundle\DependencyInjection;

use Frontastic\Common\JsonSerializer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class JsonSerializerObjectEnhancerCompilerPass implements CompilerPassInterface
{
    const TAG = 'frontastic.json_serializer.enhancer';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(JsonSerializer::class)) {
            return;
        }

        $serviceDefinition = $container->findDefinition(JsonSerializer::class);

        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        foreach ($taggedServices as $serviceId => $tags) {
            $serviceDefinition->addMethodCall('addEnhancer', [new Reference($serviceId)]);
        }
    }
}
