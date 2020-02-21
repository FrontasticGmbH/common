#  WishlistApiFactory

Fully Qualified: [`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApiFactory`](../../../../src/php/WishlistApiBundle/Domain/WishlistApiFactory.php)




## Methods

* [__construct()](#construct)
* [factor()](#factor)


### __construct()


```php
public function __construct([ProductApiFactory](../../ProductApiBundle/Domain/ProductApiFactory.md) $productApiFactory, [ProductApi](../../ProductApiBundle/Domain/ProductApi.md)\Commercetools\ClientFactory $commercetoolsClientFactory, iterable $decorators): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$productApiFactory`|`[ProductApiFactory](../../ProductApiBundle/Domain/ProductApiFactory.md)`|``|
`$commercetoolsClientFactory`|`[ProductApi](../../ProductApiBundle/Domain/ProductApi.md)\Commercetools\ClientFactory`|``|
`$decorators`|`iterable`|``|

### factor()


```php
public function factor([Project](../../ReplicatorBundle/Domain/Project.md) $project): [WishlistApi](WishlistApi.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|`[Project](../../ReplicatorBundle/Domain/Project.md)`|``|

