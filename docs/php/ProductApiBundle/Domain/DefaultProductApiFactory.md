#  DefaultProductApiFactory

Fully Qualified: [`\Frontastic\Common\ProductApiBundle\Domain\DefaultProductApiFactory`](../../../../src/php/ProductApiBundle/Domain/DefaultProductApiFactory.php)




## Methods

* [__construct()](#construct)
* [factor()](#factor)


### __construct()


```php
public function __construct([ProductApi](ProductApi.md)\Commercetools\ClientFactory $commercetoolsClientFactory, [ProductApi](ProductApi.md)\Commercetools\Locale\CommercetoolsLocaleCreatorFactory $localeCreatorFactory, iterable $decorators = []): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$commercetoolsClientFactory`|`[ProductApi](ProductApi.md)\Commercetools\ClientFactory`|``|
`$localeCreatorFactory`|`[ProductApi](ProductApi.md)\Commercetools\Locale\CommercetoolsLocaleCreatorFactory`|``|
`$decorators`|`iterable`|`[]`|

### factor()


```php
public function factor([Project](../../ReplicatorBundle/Domain/Project.md) $project): [ProductApi](ProductApi.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|`[Project](../../ReplicatorBundle/Domain/Project.md)`|``|

