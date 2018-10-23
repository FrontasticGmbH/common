<?php

namespace Frontastic\Common;

use Frontastic\Common\HttpClient\Response;
use Frontastic\Common\HttpClient\Options;

abstract class HttpClient
{
    abstract public function addDefaultHeaders(array $headers);

    abstract public function request(
        string $method,
        string $url,
        string $body = '',
        array $headers = array(),
        Options $options = null
    ): Response;

    public function __call(string $method, array $arguments): Response
    {
        array_unshift($arguments, strtoupper($method));
        return call_user_func_array([$this, 'request'], $arguments);
    }
}
