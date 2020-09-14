#  FindologicClient

**Fully Qualified**: [`\Frontastic\Common\FindologicBundle\Domain\FindologicClient`](../../../../src/php/FindologicBundle/Domain/FindologicClient.php)

## Methods

* [__construct()](#__construct)
* [isAlive()](#isalive)
* [search()](#search)

### __construct()

```php
public function __construct(
    HttpClient $httpClient,
    string $hostUrl,
    string $shopkey
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$httpClient`|[`HttpClient`](../../HttpClient.md)||
`$hostUrl`|`string`||
`$shopkey`|`string`||

Return Value: `mixed`

### isAlive()

```php
public function isAlive(): \GuzzleHttp\Promise\PromiseInterface
```

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

### search()

```php
public function search(
    SearchRequest $request
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|[`SearchRequest`](SearchRequest.md)||

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
