<?php

namespace Frontastic\Common\CoreBundle\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestUtilitiesTest extends TestCase
{
    /**
     * @param string $header
     * @param bool $prefersHtml
     * @dataProvider provideAcceptHeaderExamples
     */
    public function testPrefersHtml(string $header, bool $prefersHtml)
    {
        $request = new Request();
        $request->headers->add([
            'Accept' => $header
        ]);

        $this->assertSame($prefersHtml, RequestUtilities::prefersHtml($request));
    }

    public static function provideAcceptHeaderExamples()
    {
        return [
            'Firefox 66' => [
                'header' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'prefersHtml' => true,
            ],
            'Google Bot' => [
                'header' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'prefersHtml' => true,
            ],
            'Frontastic' => [
                'header' => 'application/json',
                'prefersHtml' => false,
            ],
        ];
    }
}
