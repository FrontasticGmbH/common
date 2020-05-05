#  CartApiFactory

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\CartApiFactory`](../../../../src/php/CartApiBundle/Domain/CartApiFactory.php)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    \Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator $factoryServiceLocator,
    OrderIdGenerator $orderIdGenerator,
    iterable $decorators
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$factoryServiceLocator`|`\Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator`||
`$orderIdGenerator`|[`OrderIdGenerator`](OrderIdGenerator.md)||
`$decorators`|`iterable`||

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

