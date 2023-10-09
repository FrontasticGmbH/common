<?php

namespace Frontastic\Common\MvcBundle\Controller\ResultConverter;

use Frontastic\Common\MvcBundle\View\TemplateGuesser;
use Frontastic\Common\Mvc\TemplateView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class ArrayToTemplateResponseConverterTest extends TestCase
{
    private $twig;
    private $guesser;
    private $converter;

    public function setUp() : void
    {
        $this->converter = new ArrayToTemplateResponseConverter(
            $this->twig = \Phake::mock(Environment::class),
            $this->guesser = \Phake::mock(TemplateGuesser::class),
            'twig'
        );
    }

    public function testSupports() : void
    {
        $this->assertTrue($this->converter->supports(new TemplateView(['foo' => 'bar'])));
        $this->assertTrue($this->converter->supports([]));
    }

    public function testRenderArrayToTemplateStringFromController() : void
    {
        $request = new Request();
        $request->attributes->set('_controller', 'ctrl');

        \Phake::when($this->guesser)->guessControllerTemplateName('ctrl', null, 'html', 'twig')->thenReturn('ctrl.html.twig');

        $response = $this->converter->convert(['foo' => 'bar'], $request);

        \Phake::verify($this->twig)->render('ctrl.html.twig', ['foo' => 'bar', 'view' => ['foo' => 'bar']]);
    }
}
