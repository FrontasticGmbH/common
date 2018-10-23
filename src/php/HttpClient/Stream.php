<?php

namespace Frontastic\Common\HttpClient;

use Frontastic\Common\HttpClient;

/**
 * HTTP client implementation
 */
class Stream extends HttpClient
{
    /**
     * Optional default headers for each request.
     *
     * @var array
     */
    private $headers = array();

    public function addDefaultHeaders(array $headers)
    {
        $this->headers = array_merge(
            $this->headers,
            $headers
        );
    }

    public function getDefaultHeaders(): array
    {
        return $this->headers;
    }

    public function setDefaultHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function request(
        string $method,
        string $url,
        string $body = '',
        array $headers = array(),
        Options $options = null
    ): Response {
        $options = $options ?: new Options();
        $httpFilePointer = @fopen(
            $url,
            'r',
            false,
            stream_context_create(
                array(
                    'http' => array(
                        'method' => $method,
                        'content' => $body ?: null,
                        'ignore_errors' => true,
                        'timeout' => $options->timeout,
                        'header' => implode(
                            "\r\n",
                            array_merge(
                                $this->headers,
                                $headers
                            )
                        ),
                    ),
                )
            )
        );

        if ($httpFilePointer === false) {
            $error = error_get_last();
            return new HttpClient\Response([
                'status' => 503,
                'body' => "Could not connect to server {$url}: " . $error['message'],
            ]);
        }

        $response = new HttpClient\Response();
        while (!feof($httpFilePointer)) {
            $response->body .= fgets($httpFilePointer);
        }

        $metaData = stream_get_meta_data($httpFilePointer);
        $rawHeaders = $metaData['wrapper_data']['headers'] ??
            $metaData['wrapper_data'];

        foreach ($rawHeaders as $lineContent) {
            if (preg_match('(^HTTP/(?P<version>\d+\.\d+)\s+(?P<status>\d+))S', $lineContent, $match)) {
                $response->status = (int) $match['status'];
            } else {
                list($key, $value) = explode(':', $lineContent, 2);
                $key = strtolower($key);
                $value = trim($value);

                if (isset($response->headers[$key])) {
                    $response->headers[$key] .= ',' . $value;
                } else {
                    $response->headers[$key] = $value;
                }
            }
        }

        return $response;
    }
}
