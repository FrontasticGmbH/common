#  ShopifyClientFactory

**Fully Qualified**: [`\Frontastic\Common\ShopifyBundle\Domain\ShopifyClientFactory`](../../../../src/php/ShopifyBundle/Domain/ShopifyClientFactory.php)

## Methods

* [__construct()](#__construct)
* [factorForConfigs()](#factorforconfigs)
* [factorForProjectAndType()](#factorforprojectandtype)

### __construct()

```php
public function __construct(
    HttpClient $httpClient,
    \Psr\SimpleCache\CacheInterface $cache,
    RequestProvider $requestProvider
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$httpClient`|[`HttpClient`](../../HttpClient.md)||
`$cache`|`\Psr\SimpleCache\CacheInterface`||
`$requestProvider`|[`RequestProvider`](../../CoreBundle/Domain/RequestProvider.md)||

Return Value: `mixed`

### factorForConfigs()

```php
public function factorForConfigs(
    object $typeSpecificConfiguration,
    ?object $shopifyConfig = null
): ShopifyClient
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$typeSpecificConfiguration`|`object`||
`$shopifyConfig`|`?object`|`null`|

Return Value: [`ShopifyClient`](ShopifyClient.md)

### factorForProjectAndType()

```php
public function factorForProjectAndType(
    Project $project,
    string $typeName
): ShopifyClient
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../ReplicatorBundle/Domain/Project.md)||
`$typeName`|`string`||

Return Value: [`ShopifyClient`](ShopifyClient.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
