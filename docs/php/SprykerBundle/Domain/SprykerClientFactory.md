#  SprykerClientFactory

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\SprykerClientFactory`](../../../../src/php/SprykerBundle/Domain/SprykerClientFactory.php)

## Methods

* [__construct()](#__construct)
* [factorForConfigs()](#factorforconfigs)
* [factorForProjectAndType()](#factorforprojectandtype)

### __construct()

```php
public function __construct(
    HttpClient $httpClient,
    ExceptionFactoryInterface $exceptionFactory
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$httpClient`|[`HttpClient`](../../HttpClient.md)||
`$exceptionFactory`|[`ExceptionFactoryInterface`](Exception/ExceptionFactoryInterface.md)||

Return Value: `mixed`

### factorForConfigs()

```php
public function factorForConfigs(
    object $typeSpecificConfiguration,
    ?object $genericConfiguration = null
): SprykerClient
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$typeSpecificConfiguration`|`object`||
`$genericConfiguration`|`?object`|`null`|

Return Value: [`SprykerClient`](SprykerClient.md)

### factorForProjectAndType()

```php
public function factorForProjectAndType(
    Project $project,
    string $typeName
): SprykerClient
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../ReplicatorBundle/Domain/Project.md)||
`$typeName`|`string`||

Return Value: [`SprykerClient`](SprykerClient.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
