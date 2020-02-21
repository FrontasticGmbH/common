#  DefaultProductApiFactory

Fully Qualified: [`\Frontastic\Common\ProductApiBundle\Domain\DefaultProductApiFactory`](../../../../src/php/ProductApiBundle/Domain/DefaultProductApiFactory.php)




## Methods

* [__construct()](#construct)
* [factor()](#factor)


### __construct()


```php
public function __construct(\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory commercetoolsClientFactory, \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory localeCreatorFactory, iterable decorators = []): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$commercetoolsClientFactory`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory`|``|
`$localeCreatorFactory`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory`|``|
`$decorators`|`iterable`|`[]`|

### factor()


```php
public function factor(\Frontastic\Common\ReplicatorBundle\Domain\Project project): \Frontastic\Common\ProductApiBundle\Domain\ProductApi
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|`\Frontastic\Common\ReplicatorBundle\Domain\Project`|``|

