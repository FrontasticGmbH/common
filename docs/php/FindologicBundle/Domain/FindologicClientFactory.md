#  FindologicClientFactory

**Fully Qualified**: [`\Frontastic\Common\FindologicBundle\Domain\FindologicClientFactory`](../../../../src/php/FindologicBundle/Domain/FindologicClientFactory.php)

## Methods

* [__construct()](#__construct)
* [factorForConfigs()](#factorforconfigs)
* [factorForProjectAndType()](#factorforprojectandtype)

### __construct()

```php
public function __construct(
    HttpClient $httpClient
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$httpClient`|[`HttpClient`](../../HttpClient.md)||

Return Value: `mixed`

### factorForConfigs()

```php
public function factorForConfigs(
    object $typeSpecificConfig,
    ?object $defaultConfig = null
): FindologicClient
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$typeSpecificConfig`|`object`||
`$defaultConfig`|`?object`|`null`|

Return Value: [`FindologicClient`](FindologicClient.md)

### factorForProjectAndType()

```php
public function factorForProjectAndType(
    Project $project,
    string $typeName
): FindologicClient
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../ReplicatorBundle/Domain/Project.md)||
`$typeName`|`string`||

Return Value: [`FindologicClient`](FindologicClient.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
