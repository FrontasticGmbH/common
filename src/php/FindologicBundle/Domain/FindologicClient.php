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
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var array<string, FindologicClientConfig> An array of client configs keyed by language
     */
    private $configs;

    /**
     * @param array<string, FindologicClientConfig> $configs
     */
    public function __construct(HttpClient $httpClient, array $configs)
    {
        $this->httpClient = $httpClient;
        $this->configs = $configs;
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
                    $url = $this->buildQueryUrl($language, 'index.php', $request->toArray());
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
        if (!isset($this->configs[$language])) {
            throw new \RuntimeException('No Findologic backend configured for requested language "' . $language . '".');
        }

        return sprintf(
            '%s/%s?shopkey=%s&outputAdapter=JSON_1.0&outputAttrib[]=cat%s',
            $this->configs[$language]->hostUrl,
            $route,
            $this->configs[$language]->shopkey,
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
