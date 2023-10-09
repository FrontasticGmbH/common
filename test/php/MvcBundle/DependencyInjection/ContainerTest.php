<?php

namespace Frontastic\Common\MvcBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ContainerTest extends TestCase
{
    /**
     * @test
     */
    public function it_compiles_with_container() : void
    {
        $container = $this->createContainer([]);

        $this->assertInstanceOf(
            'Frontastic\Common\MvcBundle\EventListener\ViewListener',
            $container->get('frontastic_common_mvc.view_listener')
        );

        $this->assertInstanceOf(
            'Frontastic\Common\MvcBundle\EventListener\ParamConverterListener',
            $container->get('frontastic_common_mvc.param_converter_listener')
        );

        $this->assertFalse($container->has('frontastic_common_mvc.turbolinks_listener'));
    }

    /**
     * @test
     */
    public function it_allows_configuring_convert_exceptions() : void
    {
        $container = $this->createContainer([
            'convert_exceptions' => ['foo' => 'bar'],
        ]);

        $this->assertEquals(['foo' => 'bar'], $container->getParameter('frontastic_common_mvc.convert_exceptions_map'));

        $this->assertInstanceOf(
            'Frontastic\Common\MvcBundle\EventListener\ConvertExceptionListener',
            $container->get('frontastic_common_mvc.convert_exception_listener')
        );
    }

    public function createContainer(array $config)
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.debug'       => false,
            'kernel.bundles'     => [],
            'kernel.cache_dir'   => sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir'    => __DIR__ . '/../../../../', // src dir
        ]));

        $loader = new FrontasticCommonMvcExtension();
        $container->set('twig', \Phake::mock('Twig\Environment'));
        $container->set('kernel', \Phake::mock('Symfony\Component\HttpKernel\KernelInterface'));
        $container->set('logger', \Phake::mock('Psr\Log\LoggerInterface'));
        $container->set('router', \Phake::mock('Symfony\Component\Routing\Generator\UrlGeneratorInterface'));
        $container->set('event_dispatcher', \Phake::mock(EventDispatcherInterface::class));
        $container->registerExtension($loader);
        $loader->load([$config], $container);

        $container->getCompilerPassConfig()->setRemovingPasses([]);

        foreach ($container->getDefinitions() as $definition) {
            $definition->setPublic(true); // symfony 4 support
        }

        $container->compile();

        return $container;
    }
}
