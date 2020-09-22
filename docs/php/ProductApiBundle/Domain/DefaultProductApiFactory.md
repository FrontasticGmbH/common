#  DefaultProductApiFactory

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\DefaultProductApiFactory`](../../../../src/php/ProductApiBundle/Domain/DefaultProductApiFactory.php)

**Implements**: [`ProductApiFactory`](ProductApiFactory.md)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    \Psr\Container\ContainerInterface $container,
    EnabledFacetService $enabledFacetService,
    ProductSearchApiFactory $productSearchApiFactory,
    iterable $decorators = []
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$container`|`\Psr\Container\ContainerInterface`||
`$enabledFacetService`|[`EnabledFacetService`](ProductApi/EnabledFacetService.md)||
`$productSearchApiFactory`|[`ProductSearchApiFactory`](../../ProductSearchApiBundle/Domain/ProductSearchApiFactory.md)||
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

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
