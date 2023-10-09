<?php

namespace Frontastic\Common\MvcBundle\Controller\ResultConverter;

use Frontastic\Common\Mvc\RedirectRoute;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RedirectConverterTest extends TestCase
{
    private $router;
    private $converter;

    public function setUp() : void
    {
        $this->router = \Phake::mock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->converter = new RedirectConverter($this->router);
    }

    /**
     * @test
     */
    public function it_redirects_when_goto_page_result() : void
    {
        \Phake::when($this->router)->generate('foo', ['id' => 10])->thenReturn('/foo?id=10');
        $request = Request::create('GET', '/');

        $response = $this->converter->convert(new RedirectRoute('foo', ['id' => 10]), $request);

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\Response',
            $response
        );
        $this->assertTrue($response->isRedirect());

        $this->assertEquals('/foo?id=10', $response->headers->get('Location'));
    }

    /**
     * @test
     */
    public function it_redirects_when_redirect_route_result() : void
    {
        \Phake::when($this->router)->generate('foo', ['id' => 10])->thenReturn('/foo?id=10');
        $request = Request::create('GET', '/');

        $response = $this->converter->convert(new RedirectRoute('foo', ['id' => 10]), $request);

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\Response',
            $response
        );
        $this->assertTrue($response->isRedirect());

        $this->assertEquals('/foo?id=10', $response->headers->get('Location'));
    }

    /**
     * @test
     */
    public function it_redirects_with_status_code() : void
    {
        \Phake::when($this->router)->generate('foo', ['id' => 10])->thenReturn('/foo?id=10');
        $request = Request::create('GET', '/');

        $response = $this->converter->convert(new RedirectRoute('foo', ['id' => 10], 303), $request);

        $this->assertSame(303, $response->getStatusCode());
    }
}
