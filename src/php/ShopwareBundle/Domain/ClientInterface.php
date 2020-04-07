<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain;

use GuzzleHttp\Promise\PromiseInterface;

interface ClientInterface
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';

    public function forLanguage(string $languageId): ClientInterface;

    public function forCurrency(string $currencyId): ClientInterface;

    public function get(string $uri, array $parameters = [], array $headers = []): PromiseInterface;

    public function patch(string $uri, array $parameters = [], array $headers = [], $body = null): PromiseInterface;

    public function post(string $uri, array $parameters = [], array $headers = [], $body = null): PromiseInterface;

    public function put(string $uri, array $parameters = [], array $headers = [], $body = null): PromiseInterface;

    public function delete(string $uri, array $parameters = [], array $headers = []): PromiseInterface;
}
