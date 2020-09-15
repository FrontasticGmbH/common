#  ShopifyClientFactory

**Fully Qualified**: [`\Frontastic\Common\ShopifyBundle\Domain\ShopifyClientFactory`](../../../../src/php/ShopifyBundle/Domain/ShopifyClientFactory.php)

## Methods

* [__construct()](#__construct)
* [factorForProjectAndType()](#factorforprojectandtype)

### __construct()

```php
public function __construct(
    HttpClient $httpClient,
    \Psr\SimpleCache\CacheInterface $cache
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$httpClient`|[`HttpClient`](../../HttpClient.md)||
`$cache`|`\Psr\SimpleCache\CacheInterface`||

Return Value: `mixed`

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