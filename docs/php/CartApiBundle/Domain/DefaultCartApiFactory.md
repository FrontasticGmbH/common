#  DefaultCartApiFactory

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\DefaultCartApiFactory`](../../../../src/php/CartApiBundle/Domain/DefaultCartApiFactory.php)

**Implements**: [`CartApiFactory`](CartApiFactory.md)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    \Psr\Container\ContainerInterface $container,
    AccountApiFactory $accountApiFactory,
    object $orderIdGenerator,
    iterable $decorators,
    CartCheckoutService $cartCheckoutService,
    \Psr\Log\LoggerInterface $logger
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$container`|`\Psr\Container\ContainerInterface`||
`$accountApiFactory`|[`AccountApiFactory`](../../AccountApiBundle/Domain/AccountApiFactory.md)||
`$orderIdGenerator`|`object`||
`$decorators`|`iterable`||
`$cartCheckoutService`|[`CartCheckoutService`](CartCheckoutService.md)||
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
