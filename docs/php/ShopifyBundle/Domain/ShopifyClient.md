#  ShopifyClient

**Fully Qualified**: [`\Frontastic\Common\ShopifyBundle\Domain\ShopifyClient`](../../../../src/php/ShopifyBundle/Domain/ShopifyClient.php)

## Methods

* [__construct()](#__construct)
* [request()](#request)

### __construct()

```php
public function __construct(
    HttpClient $httpClient,
    \Psr\SimpleCache\CacheInterface $cache,
    string $hostUrl,
    string $accessToken
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$httpClient`|[`HttpClient`](../../HttpClient.md)||
`$cache`|`\Psr\SimpleCache\CacheInterface`||
`$hostUrl`|`string`||
`$accessToken`|`string`||

Return Value: `mixed`

### request()

```php
public function request(
    string $query,
    string $locale = null
): \GuzzleHttp\Promise\PromiseInterface
```

*takes GraphQL query, returns JSON result as string*

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|`string`||
`$locale`|`string`|`null`|

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
