<?php

namespace Frontastic\Common\FindologicBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\RequestProvider;
use Frontastic\Common\HttpClient;
use GuzzleHttp\Promise\FulfilledPromise;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class FindologicClientTest extends TestCase
{
    public function testSendsClientRequestParameters()
    {
        $language = 'en_GB@GBP';

        $httpClient = \Phake::mock(HttpClient::class);
        $requestProvider = \Phake::mock(RequestProvider::class);
        $endpointConfig = new FindologicEndpointConfig(['hostUrl' => 'foo', 'shopkey' => 'bar']);

        $request = $this->getRequest();

        \Phake::when($requestProvider)->getCurrentRequest()->thenReturn($request);
        \Phake::when($httpClient)
            ->getAsync(\Phake::anyParameters())
            ->thenReturn(new FulfilledPromise(new HttpClient\Response()));
        \Phake::when($httpClient)
            ->requestAsync(\Phake::anyParameters())
            ->thenReturn(new FulfilledPromise(new HttpClient\Response(['body' => ''])));

        $options = new HttpClient\Options(['timeout' => FindologicClient::REQUEST_TIMEOUT]);
        $searchRequest = new SearchRequest(['outputAttributes' => ['cat', 'price']]);
        $url = 'foo/index.php?shopkey=bar&outputAdapter=JSON_1.0' .
            '&outputAttrib%5B0%5D=cat&outputAttrib%5B1%5D=price' .
            '&userIp=1.2.3.4' .
            '&referer=https%3A%2F%2Fmy.shop.com%2Fsome%2Fcategory%2Fsearch%2Furl' .
            '&shopUrl=https%3A%2F%2Fmy.shop.com';

        $client = new FindologicClient($httpClient, $requestProvider, [$language => $endpointConfig]);
        $client->search($language, $searchRequest)->wait();

        \Phake::verify($httpClient)->requestAsync('GET', $url, '', [], $options);
    }

    private function getRequest(): Request
    {
        return new Request(
            [],
            [],
            [],
            [],
            [],
            [
                'REMOTE_ADDR' => '1.2.3.4',
                'HTTP_Referer' => 'https://my.shop.com/some/category/search/url'
            ]
        );
    }
}
