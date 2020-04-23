<?php

namespace Frontastic\Common\DevelopmentBundle\EventListener;

use Frontastic\Common\DevelopmentBundle\Debugger;
use Frontastic\Common\JsonSerializer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BrowserConsoleDebuggerListenerTest extends TestCase
{
    public static function setupBeforeClass()
    {
        Debugger::log('Something');
    }

    /**
     * @dataProvider provideHtmlExamples
     */
    public function testOnKernelResponseHtmlInjection(string $html)
    {
        $listener = new BrowserConsoleDebuggerListener(new JsonSerializer());

        $response = new  Response();
        $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
        $response->setContent($html);

        $event = new FilterResponseEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock(),
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );

        $listener->onKernelResponse($event);

        $dom = new \DOMDocument();
        $dom->loadHTML($response->getContent());
        $div = $dom->getElementById('appData');

        $this->assertTrue($div->hasAttribute('data-debug'), 'Missing data-debug attribute!');
        $this->assertEquals(json_encode([['Something']]), $div->getAttribute('data-debug'));
    }

    public static function provideHtmlExamples(): array
    {
        return [
            [
                '<html lang="de"><head></head><body><div id="appData" /></body></html>',
            ],
            [
                '<html lang="de"><head></head><body><div id="appData" data-props="{}"></div></body></html>',
            ],
            [
                '<html lang="de"><head></head><body><div         id="appData"        ></div></body></html>',
            ],
            [
                '<html lang="de"><head></head><body><div
id="appData"
data-props="{}"/></body></html>',
            ]
        ];
    }
}
