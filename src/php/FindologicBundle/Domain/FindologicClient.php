<?php

namespace Frontastic\Common\FindologicBundle\Domain;

use Frontastic\Common\FindologicBundle\Exception\ServiceNotAliveException;
use Frontastic\Common\HttpClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;

class FindologicClient
{
    private const ALIVE_TIMEOUT = 1;
    private const REQUEST_TIMEOUT = 3;

    /**
     * @var string
     */
    private $shopkey;

    /**
     * @var string
     */
    private $hostUrl;

    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct(
        HttpClient $httpClient,
        string $hostUrl,
        string $shopkey
    ) {
        $this->httpClient = $httpClient;
        $this->hostUrl = $hostUrl;
        $this->shopkey = $shopkey;
    }

    public function isAlive(): PromiseInterface
    {
        $url = $this->buildQueryUrl('alivetest.php');
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

    public function search(SearchRequest $request): PromiseInterface
    {
        return $this->isAlive()
            ->then(
                function () use ($request) {
                    $url = $this->buildQueryUrl('index.php', $request->toArray());
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

    private function buildQueryUrl(string $route, array $parameters = null)
    {
        return sprintf(
            '%s/%s?shopkey=%s&outputAdapter=JSON_1.0&outputAttrib[]=cat%s',
            $this->hostUrl,
            $route,
            $this->shopkey,
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
