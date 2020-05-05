#  DefaultProductApiFactory

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\DefaultProductApiFactory`](../../../../src/php/ProductApiBundle/Domain/DefaultProductApiFactory.php)

**Implements**: [`ProductApiFactory`](ProductApiFactory.md)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    \Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator $serviceLocator,
    ProductApi\EnabledFacetService $enabledFacetService,
    iterable $decorators = []
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$serviceLocator`|`\Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator`||
`$enabledFacetService`|[`ProductApi`](ProductApi.md)\EnabledFacetService||
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

