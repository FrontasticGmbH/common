<?php

namespace Frontastic\Common\HttpClient;

use Domnikl\Statsd\Client;
use Frontastic\Common\HttpClient;
use GuzzleHttp\Promise\PromiseInterface;

class Logstash extends HttpClient
{
    /**
     * @var HttpClient
     */
    private $aggregate;

    public function __construct(HttpClient $aggregate)
    {
        $this->aggregate = $aggregate;
    }

    public function addDefaultHeaders(array $headers)
    {
        return $this->aggregate->addDefaultHeaders($headers);
    }

    public function requestAsync(
        string $method,
        string $url,
        string $body = '',
        array $headers = array(),
        Options $options = null
    ): PromiseInterface {
        $start = microtime(true);

        return $this->aggregate
            ->requestAsync($method, $url, $body, $headers, $options)
            ->then(function ($response) use ($start, $method, $url) {
                // [Fri, 16 Aug 2019 11:15:37 +0200] api.sphere.com POST /cart/update?sajkfdhaj 340ms 200
                file_put_contents(
                    '/var/log/frontastic/responses.log',
                    sprintf(
                        '[%s] %s %s %s %.3fs %d' . PHP_EOL,
                        date('r'),
                        parse_url($url, PHP_URL_HOST),
                        $method,
                        parse_url($url, PHP_URL_PATH),
                        microtime(true) - $start,
                        $response->status
                    ),
                    FILE_APPEND
                );

                return $response;
            });
    }
}
