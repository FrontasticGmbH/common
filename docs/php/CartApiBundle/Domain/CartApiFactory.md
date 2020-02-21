#  CartApiFactory

Fully Qualified: [`\Frontastic\Common\CartApiBundle\Domain\CartApiFactory`](../../../../src/php/CartApiBundle/Domain/CartApiFactory.php)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    ProductApi\Commercetools\ClientFactory $commercetoolsClientFactory,
    ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory $localeCreatorFactory,
    OrderIdGenerator $orderIdGenerator,
    iterable $decorators
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$commercetoolsClientFactory`|`ProductApi\Commercetools\ClientFactory`||
`$localeCreatorFactory`|`ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory`||
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

