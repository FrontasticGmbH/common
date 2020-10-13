#  SapClientFactory

**Fully Qualified**: [`\Frontastic\Common\SapCommerceCloudBundle\Domain\SapClientFactory`](../../../../src/php/SapCommerceCloudBundle/Domain/SapClientFactory.php)

## Methods

* [__construct()](#__construct)
* [factorForConfigs()](#factorforconfigs)
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

### factorForConfigs()

```php
public function factorForConfigs(
    object $typeSpecificConfiguration,
    ?object $sapConfig = null
): SapClient
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$typeSpecificConfiguration`|`object`||
`$sapConfig`|`?object`|`null`|

Return Value: [`SapClient`](SapClient.md)

### factorForProjectAndType()

```php
public function factorForProjectAndType(
    Project $project,
    string $typeName
): SapClient
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../ReplicatorBundle/Domain/Project.md)||
`$typeName`|`string`||

Return Value: [`SapClient`](SapClient.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
