#  CartApiFactory

Fully Qualified: [`\Frontastic\Common\CartApiBundle\Domain\CartApiFactory`](../../../../src/php/CartApiBundle/Domain/CartApiFactory.php)




## Methods

* [__construct()](#construct)
* [factor()](#factor)


### __construct()


```php
public function __construct([ProductApi](../../ProductApiBundle/Domain/ProductApi.md)\Commercetools\ClientFactory $commercetoolsClientFactory, [ProductApi](../../ProductApiBundle/Domain/ProductApi.md)\Commercetools\Locale\CommercetoolsLocaleCreatorFactory $localeCreatorFactory, [OrderIdGenerator](OrderIdGenerator.md) $orderIdGenerator, iterable $decorators): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$commercetoolsClientFactory`|`[ProductApi](../../ProductApiBundle/Domain/ProductApi.md)\Commercetools\ClientFactory`|``|
`$localeCreatorFactory`|`[ProductApi](../../ProductApiBundle/Domain/ProductApi.md)\Commercetools\Locale\CommercetoolsLocaleCreatorFactory`|``|
`$orderIdGenerator`|`[OrderIdGenerator](OrderIdGenerator.md)`|``|
`$decorators`|`iterable`|``|

### factor()


```php
public function factor([Project](../../ReplicatorBundle/Domain/Project.md) $project): [CartApi](CartApi.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|`[Project](../../ReplicatorBundle/Domain/Project.md)`|``|

