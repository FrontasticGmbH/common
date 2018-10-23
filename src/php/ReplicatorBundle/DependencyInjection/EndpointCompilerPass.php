<?php

namespace Frontastic\Common\ReplicatorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class EndpointCompilerPass implements CompilerPassInterface
{
    protected $serviceId = 'Frontastic\Common\ReplicatorBundle\Domain\EndpointService';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->serviceId)) {
            return;
        }

        $commandRegistry = $container->getDefinition($this->serviceId);

        $taggedServices = $container->findTaggedServiceIds('endpoint.target');
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $commandRegistry->addMethodCall(
                    'addReplicationTarget',
                    array($attributes['channel'], new Reference($id))
                );
            }
        }

        $taggedServices = $container->findTaggedServiceIds('endpoint.source');
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $commandRegistry->addMethodCall(
                    'addReplicationSource',
                    array($attributes['channel'], new Reference($id))
                );
            }
        }
    }
}
