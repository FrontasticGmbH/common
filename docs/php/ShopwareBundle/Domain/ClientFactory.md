#  ClientFactory

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\ClientFactory`](../../../../src/php/ShopwareBundle/Domain/ClientFactory.php)

## Methods

* [__construct()](#__construct)
* [factorForConfigs()](#factorforconfigs)
* [factorForProjectAndType()](#factorforprojectandtype)

### __construct()

```php
public function __construct(
    HttpClient $httpClient,
    \Doctrine\Common\Cache\Cache $cache
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$httpClient`|[`HttpClient`](../../HttpClient.md)||
`$cache`|`\Doctrine\Common\Cache\Cache`||

Return Value: `mixed`

### factorForConfigs()

```php
public function factorForConfigs(
    object $typeSpecificConfiguration,
    ?object $genericConfiguration = null
): Client
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$typeSpecificConfiguration`|`object`||
`$genericConfiguration`|`?object`|`null`|

Return Value: [`Client`](Client.md)

### factorForProjectAndType()

```php
public function factorForProjectAndType(
    Project $project,
    string $typeName
): Client
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../ReplicatorBundle/Domain/Project.md)||
`$typeName`|`string`||

Return Value: [`Client`](Client.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
