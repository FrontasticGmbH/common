<?php

namespace Frontastic\Common\FindologicBundle\Domain;

use Frontastic\Common\FindologicBundle\Exception\ServiceNotAliveException;
use Frontastic\Common\HttpClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;

class FindologicClient
{
    public const ALIVE_TIMEOUT = 1;
    public const REQUEST_TIMEOUT = 3;

    private const DEFAULT_OUTPUT_ATTRIBUTES = [
        'cat',
        'price',
    ];

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var RequestProvider
     */
    private $requestProvider;

    /**
     * @var array<string, FindologicEndpointConfig> An array of endpoint configs keyed by language
     */
    private $endpoints;

    /**
     * @var string[]
     */
    private $outputAttributes;

    /**
     * @param array<string, FindologicEndpointConfig> $endpoints
     * @param string[] $outputAttributes
     */
    public function __construct(
        HttpClient $httpClient,
        RequestProvider $requestProvider,
        array $endpoints,
        array $outputAttributes = []
    ) {
        $this->httpClient = $httpClient;
        $this->requestProvider = $requestProvider;
        $this->endpoints = $endpoints;
        $this->outputAttributes = $outputAttributes;
    }

    public function isAlive(string $language): PromiseInterface
    {
        $url = $this->buildQueryUrl($language, 'alivetest.php');
        $options = new HttpClient\Options(['timeout' => self::ALIVE_TIMEOUT]);

        return $this->httpClient
            ->getAsync($url, '', [], $options)
            ->then(
                function (HttpClient\Response $response) {
                    if ($response->status >= 400) {
                        throw new ServiceNotAliveException();
                    }

                    return true;
                }
            )
            ->otherwise(
                function () {
                    throw new ServiceNotAliveException();
                }
            );
    }

    public function search(string $language, SearchRequest $request): PromiseInterface
    {
        return $this->isAlive($language)
            ->then(
                function () use ($language, $request) {
                    $parameters = $request->toArray();

                    $parameters['outputAttrib'] = array_unique(
                        array_merge(self::DEFAULT_OUTPUT_ATTRIBUTES, $this->outputAttributes)
                    );

                    $request = $this->requestProvider->getCurrentRequest();

                    if ($request !== null) {
                        $parameters['userIp'] = $request->getClientIp();

                        if ($request->headers->has('Referer')) {
                            $referer = $request->headers->get('Referer');
                            $urlParts = parse_url($referer);

                            $parameters['referer'] = $referer;
                            $parameters['shopUrl'] = sprintf('%s://%s', $urlParts['scheme'], $urlParts['host']);
                        }
                    }

                    $url = $this->buildQueryUrl($language, 'index.php', $parameters);
                    $options = new HttpClient\Options(['timeout' => self::REQUEST_TIMEOUT]);

                    return $this->httpClient
                        ->requestAsync('GET', $url, '', [], $options)
                        ->then(
                            function (HttpClient\Response $response) {
                                if ($response->status >= 400) {
                                    throw $this->prepareException($response);
                                }

                                return $this->parseResponse($response);
                            }
                        );
                }
            );
    }

    private function buildQueryUrl(string $language, string $route, array $parameters = null)
    {
        if (!isset($this->endpoints[$language])) {
            throw new \RuntimeException('No Findologic backend configured for requested language "' . $language . '".');
        }

        return sprintf(
            '%s/%s?shopkey=%s&outputAdapter=JSON_1.0%s',
            $this->endpoints[$language]->hostUrl,
            $route,
            $this->endpoints[$language]->shopkey,
            empty($parameters) ? '' : '&' . http_build_query($parameters)
        );
    }

    private function parseResponse(HttpClient\Response $response): ?array
    {
        if ($response->body === '') {
            return null;
        }

        $body = json_decode($response->body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RequestException(
                'JSON error: ' . json_last_error_msg(),
                $response->status
            );
        }

        return [
            'response' => $response,
            'status' => $response->status,
            'body' => $body,
        ];
    }

    protected function prepareException(HttpClient\Response $response): \Exception
    {
        $errorData = json_decode($response->body);
        $exception = new RequestException(
            ($errorData->message ?? $response->body) ?: 'Internal Server Error',
            $response->status ?? 503
        );

        return $exception;
    }
}
