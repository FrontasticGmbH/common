<?php

namespace Frontastic\Common;

use Frontastic\Common\HttpClient\Options;
use Frontastic\Common\HttpClient\Response;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * @method Response get(string $url, string $body = '', array $headers = [], Options $options = null)
 * @method Response post(string $url, string $body = '', array $headers = [], Options $options = null)
 * @method Response put(string $url, string $body = '', array $headers = [], Options $options = null)
 * @method Response delete(string $url, string $body = '', array $headers = [], Options $options = null)
 *
 * @method PromiseInterface getAsync(string $url, string $body = '', array $headers = [], Options $options = null)
 * @method PromiseInterface postAsync(string $url, string $body = '', array $headers = [], Options $options = null)
 * @method PromiseInterface putAsync(string $url, string $body = '', array $headers = [], Options $options = null)
 * @method PromiseInterface deleteAsync(string $url, string $body = '', array $headers = [], Options $options = null)
 */
abstract class HttpClient
{
    abstract public function addDefaultHeaders(array $headers);

    public function request(
        string $method,
        string $url,
        string $body = '',
        array $headers = array(),
        Options $options = null
    ): Response {
        return $this->requestAsync($method, $url, $body, $headers, $options)->wait();
    }

    abstract public function requestAsync(
        string $method,
        string $url,
        string $body = '',
        array $headers = array(),
        Options $options = null
    ): PromiseInterface;

    /**
     * @return Response|PromiseInterface
     */
    public function __call(string $functionName, array $arguments): object
    {
        // Check if the method name ends in Async. Forward to request for non async calls and forward to requestAsync
        //for async requests.
        $asyncSuffix = 'Async';
        if (substr_compare($functionName, $asyncSuffix, -strlen($asyncSuffix))) {
            $httpMethod = substr($functionName, 0, -strlen($asyncSuffix));
            $callMethod = 'requestAsync';
        } else {
            $httpMethod = $functionName;
            $callMethod = 'request';
        }

        $httpMethod = strtoupper($httpMethod);

        array_unshift($arguments, $httpMethod);
        return call_user_func_array([$this, $callMethod], $arguments);
    }
}
