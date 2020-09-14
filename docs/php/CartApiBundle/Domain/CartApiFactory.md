#  CartApiFactory

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\CartApiFactory`](../../../../src/php/CartApiBundle/Domain/CartApiFactory.php)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    \Psr\Container\ContainerInterface $container,
    OrderIdGenerator $orderIdGenerator,
    iterable $decorators,
    \Psr\Log\LoggerInterface $logger
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$container`|`\Psr\Container\ContainerInterface`||
`$orderIdGenerator`|[`OrderIdGenerator`](OrderIdGenerator.md)||
`$decorators`|`iterable`||
`$logger`|`\Psr\Log\LoggerInterface`||

Return Value: `mixed`

### factor()

```php
public function factor(
    Project $project
): CartApi
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../ReplicatorBundle/Domain/Project.md)||

Return Value: [`CartApi`](CartApi.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
