<?php

namespace Frontastic\Common\HttpClient;

use Frontastic\Common\HttpClient;

class Signing extends HttpClient
{
    /**
     * @var HttpClient
     */
    private $sharedSecret;

    public function __construct(HttpClient $aggregate, $sharedSecret)
    {
        $this->aggregate = $aggregate;
        $this->sharedSecret = $sharedSecret;
    }

    public function addDefaultHeaders(array $headers)
    {
        return $this->aggregate->addDefaultHeaders($headers);
    }

    public function request(
        string $method,
        string $url,
        string $body = '',
        array $headers = array(),
        Options $options = null
    ): Response {
        $nonce = random_int(0, mt_getrandmax());
        $hash = hash_hmac('sha256', $nonce . ':' . $body, $this->sharedSecret);
        $headers[] = 'X-Frontastic-Nonce: ' . $nonce;
        $headers[] = 'X-Frontastic-Hash: ' . $hash;

        return $this->aggregate->request($method, $url, $body, $headers, $options);
    }
}
