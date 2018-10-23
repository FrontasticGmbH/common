<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox;

use Frontastic\Common\HttpClient;

class DataStudioClient
{
    /**
     * @var string
     */
    private $projectId;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $host = 'api-data-studio.semknox.com';

    /**
     * @var \Frontastic\Common\HttpClient
     */
    private $httpClient;

    /**
     * DataStudioClient constructor.
     *
     * @param string $projectId
     * @param string $accessToken
     * @param \Frontastic\Common\HttpClient $httpClient
     */
    public function __construct(string $projectId, string $accessToken, HttpClient $httpClient)
    {
        $this->projectId = $projectId;
        $this->accessToken = $accessToken;
        $this->httpClient = $httpClient;
    }

    public function get(string $uri, array $parameters = [], array $headers = [], string $body = ''): array
    {
        return $this->request(
            'GET',
            "{$uri}/{$this->projectId}",
            $parameters,
            $headers,
            $body
        );
    }

    public function post(string $uri, string $body = '', array $parameters = [], array $headers = []): array
    {
        return $this->request(
            'POST',
            "{$uri}/{$this->projectId}",
            $parameters,
            $headers,
            $body
        );
    }

    public function delete(string $uri, array $parameters = [], array $headers = [], string $body = ''): array
    {
        return $this->request(
            'DELETE',
            "{$uri}/{$this->projectId}",
            $parameters,
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
            array_merge(
                $headers,
                ["Authorization: Bearer {$this->accessToken}"]
            )
        );

        // @todo Proper error handling or silent ignore
        if ($response->status >= 400) {
            return ["Error" => '400'];
        }

        $data = json_decode($response->body, true);
        if (JSON_ERROR_NONE === json_last_error()) {
            return $data;
        }
        // @todo Proper error handling or silent ignore
        return ['Error' => json_last_error_msg()];
    }
}
