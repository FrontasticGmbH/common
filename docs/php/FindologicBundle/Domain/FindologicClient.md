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
    RequestProvider $requestProvider,
    array $endpoints,
    array $outputAttributes = []
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$httpClient`|[`HttpClient`](../../HttpClient.md)||
`$requestProvider`|[`RequestProvider`](../../CoreBundle/Domain/RequestProvider.md)||
`$endpoints`|`array`||
`$outputAttributes`|`array`|`[]`|

Return Value: `mixed`

### isAlive()

```php
public function isAlive(
    string $language
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$language`|`string`||

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

### search()

```php
public function search(
    string $language,
    SearchRequest $request
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$language`|`string`||
`$request`|[`SearchRequest`](SearchRequest.md)||

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
