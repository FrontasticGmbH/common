#  DefaultProductApiFactory

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\DefaultProductApiFactory`](../../../../src/php/ProductApiBundle/Domain/DefaultProductApiFactory.php)

**Implements**: [`ProductApiFactory`](ProductApiFactory.md)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    ProductApi\Commercetools\ClientFactory $commercetoolsClientFactory,
    ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory $localeCreatorFactory,
    iterable $decorators = []
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$commercetoolsClientFactory`|[`ProductApi`](ProductApi.md)\Commercetools\ClientFactory||
`$localeCreatorFactory`|[`ProductApi`](ProductApi.md)\Commercetools\Locale\CommercetoolsLocaleCreatorFactory||
`$decorators`|`iterable`|`[]`|

Return Value: `mixed`

### factor()

```php
public function factor(
    Project $project
): ProductApi
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../ReplicatorBundle/Domain/Project.md)||

Return Value: [`ProductApi`](ProductApi.md)
