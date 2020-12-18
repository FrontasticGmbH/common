#  DefaultAccountApiFactory

**Fully Qualified**: [`\Frontastic\Common\AccountApiBundle\Domain\DefaultAccountApiFactory`](../../../../src/php/AccountApiBundle/Domain/DefaultAccountApiFactory.php)

**Implements**: [`AccountApiFactory`](AccountApiFactory.md)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    \Psr\Container\ContainerInterface $container,
    iterable $decorators,
    \Psr\Log\LoggerInterface $logger
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$container`|`\Psr\Container\ContainerInterface`||
`$decorators`|`iterable`||
`$logger`|`\Psr\Log\LoggerInterface`||

Return Value: `mixed`

### factor()

```php
public function factor(
    Project $project
): AccountApi
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../ReplicatorBundle/Domain/Project.md)||

Return Value: [`AccountApi`](AccountApi.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
