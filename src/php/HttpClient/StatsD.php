<?php

namespace Frontastic\Common\HttpClient;

use Domnikl\Statsd\Client;
use Frontastic\Common\HttpClient;
use GuzzleHttp\Promise\PromiseInterface;

class StatsD extends HttpClient
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Client
     */
    private $statsdClient;

    /**
     * @var HttpClient
     */
    private $sharedSecret;

    public function __construct(string $name, Client $statsdClient, HttpClient $aggregate)
    {
        $this->name = $name;
        $this->statsdClient = $statsdClient;
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
            ->then(function ($response) use ($start) {
                $duration = microtime(true) - $start;
                $this->statsdClient->timing($this->name . '.request.time', $duration);
                $this->statsdClient->increment($this->name . '.status.' . $response->status . '.count');
                return $response;
            });
    }
}
