<?php

namespace Frontastic\Common\CoreBundle;

use Frontastic\Common\CoreBundle\DependencyInjection\CompilerPass\FactoryServiceLocatorServiceProviderPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FrontasticCommonCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FactoryServiceLocatorServiceProviderPass());
    }
}
