#  DefaultProductApiFactory

Fully Qualified: [`\Frontastic\Common\ProductApiBundle\Domain\DefaultProductApiFactory`](../../../../src/php/ProductApiBundle/Domain/DefaultProductApiFactory.php)




## Methods

* [__construct()](#construct)
* [factor()](#factor)


### __construct()


```php
public function __construct(ProductApi\Commercetools\ClientFactory $commercetoolsClientFactory, ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory $localeCreatorFactory, iterable $decorators = []): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$commercetoolsClientFactory`|`ProductApi\Commercetools\ClientFactory`|``|
`$localeCreatorFactory`|`ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory`|``|
`$decorators`|`iterable`|`[]`|

Return Value: `mixed`

### factor()


```php
public function factor(Project $project): ProductApi
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../ReplicatorBundle/Domain/Project.md)|``|

Return Value: [`ProductApi`](ProductApi.md)

