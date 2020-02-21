#  WishlistApiFactory

Fully Qualified: [`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApiFactory`](../../../../src/php/WishlistApiBundle/Domain/WishlistApiFactory.php)




## Methods

* [__construct()](#construct)
* [factor()](#factor)


### __construct()


```php
public function __construct(\Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory productApiFactory, \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory commercetoolsClientFactory, iterable decorators): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$productApiFactory`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory`|``|
`$commercetoolsClientFactory`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory`|``|
`$decorators`|`iterable`|``|

### factor()


```php
public function factor(\Frontastic\Common\ReplicatorBundle\Domain\Project project): \Frontastic\Common\WishlistApiBundle\Domain\WishlistApi
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|`\Frontastic\Common\ReplicatorBundle\Domain\Project`|``|

