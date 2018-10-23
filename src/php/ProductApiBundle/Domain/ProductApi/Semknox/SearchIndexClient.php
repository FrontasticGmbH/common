<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox;

use Frontastic\Common\HttpClient;

class SearchIndexClient
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $customerId;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var \Frontastic\Common\HttpClient
     */
    private $httpClient;

    public function __construct(string $host, string $customerId, string $apiKey, HttpClient $httpClient)
    {
        $this->host = $host;
        $this->customerId = $customerId;
        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient;
    }

    public function get(string $uri, array $parameters = [], array $headers = []): array
    {
        return $this->request(
            'GET',
            $uri,
            array_merge($parameters, [
                'customerId' => $this->customerId,
                'apiKey' => $this->apiKey
            ]),
            $headers
        );
    }

    public function post(string $uri, string $body = '', array $parameters = [], array $headers = []): array
    {
        return $this->request(
            'POST',
            $uri,
            $parameters,
            $headers,
            sprintf(
                '%s&customerId=%d&apiKey=%s',
                $body,
                $this->customerId,
                $this->apiKey
            )
        );
    }

    public function put(string $uri, string $body = '', array $parameters = [], array $headers = []): array
    {
        return $this->request(
            'PUT',
            $uri,
            $parameters,
            $headers,
            sprintf(
                '%s&customerId=%d&apiKey=%s',
                $body,
                $this->customerId,
                $this->apiKey
            )
        );
    }

    public function delete(string $uri, array $parameters = [], array $headers = [], string $body = ''): array
    {
        return $this->request(
            'DELETE',
            $uri,
            array_merge($parameters, [
                'customerId' => $this->customerId,
                'apiKey' => $this->apiKey
            ]),
            $headers,
            $body
        );
    }

    public function request(string $method, string $uri, array $parameters = [], array $headers = [], $body = ''): array
    {
        $response = $this->httpClient->request(
            $method,
            sprintf('https://%s/%s?%s', $this->host, ltrim($uri, '/'), http_build_query($parameters)),
            $body,
            $headers
        );

        // @todo Proper error handling or silent ignore
        if ($response->status >= 400) {
            return ["Error" => $response->status, 'Message' => $response->body];
        }

        $data = json_decode($response->body, true);
        if (JSON_ERROR_NONE === json_last_error()) {
            return $data;
        }
        // @todo Proper error handling or silent ignore
        return ['Error' => json_last_error_msg()];
    }
}
