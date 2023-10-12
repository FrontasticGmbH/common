<?php

namespace Frontastic\Common\MvcBundle\View;

use PHPUnit\Framework\TestCase;

class SymfonyConventionsTemplateGuesserTest extends TestCase
{
    private $bundleLocation;
    private $parser;
    private $guesser;

    public function setUp() : void
    {
        $this->bundleLocation = \Phake::mock('Frontastic\Common\MvcBundle\View\BundleLocation');
        $this->parser = \Phake::mock('Frontastic\Common\MvcBundle\Controller\GyroControllerNameParser');

        $this->guesser = new SymfonyConventionsTemplateGuesser(
            $this->bundleLocation,
            $this->parser
        );
    }

    /**
     * @test
     */
    public function it_converts_convention_controller_to_template_reference() : void
    {
        \Phake::when($this->bundleLocation)->locationFor('Controller\\FooController')->thenReturn('CoreBundle');

        $this->assertEquals(
            '@Core/Foo/bar.html.twig',
            $this->guesser->guessControllerTemplateName('Controller\\FooController::barAction', null, 'html', 'twig')
        );
    }

    /**
     * @test
     */
    public function it_converts_any_suffixed_controller_to_template_reference() : void
    {
        \Phake::when($this->bundleLocation)->locationFor('Product\\ProductController')->thenReturn('CoreBundle');

        $this->assertEquals(
            '@Core/Product/list.html.twig',
            $this->guesser->guessControllerTemplateName('Product\\ProductController::listAction', null, 'html', 'twig')
        );
    }

    /**
     * @test
     */
    public function it_uses_parser_when_converting_non_callable_controller_to_template_reference() : void
    {
        \Phake::when($this->parser)->parse('foo:barAction')->thenReturn('Controller\\FooController::barAction');
        \Phake::when($this->bundleLocation)->locationFor('Controller\\FooController')->thenReturn('CoreBundle');

        $this->assertEquals(
            '@Core/Foo/bar.html.twig',
            $this->guesser->guessControllerTemplateName('Controller\\FooController::barAction', null, 'html', 'twig')
        );
    }

    /**
     * @test
     */
    public function it_uses_provided_action_name_as_overwrite() : void
    {
        \Phake::when($this->parser)->parse('foo:barAction')->thenReturn('Controller\\FooController::barAction');
        \Phake::when($this->bundleLocation)->locationFor('Controller\\FooController')->thenReturn('CoreBundle');

        $this->assertEquals(
            '@Core/Foo/baz.html.twig',
            $this->guesser->guessControllerTemplateName('Controller\\FooController::barAction', 'baz', 'html', 'twig')
        );
    }

    /**
     * @test
     */
    public function it_detects_symfony_flex_controllers() : void
    {
        \Phake::when($this->parser)->parse('foo:barAction')->thenReturn('App\\Controller\\FooController::barAction');
        \Phake::when($this->bundleLocation)->locationFor('App\\Controller\\FooController')->thenReturn('CoreBundle');

        $this->assertEquals(
            'Foo/baz.html.twig',
            $this->guesser->guessControllerTemplateName('App\\Controller\\FooController::barAction', 'baz', 'html', 'twig')
        );
    }
}
