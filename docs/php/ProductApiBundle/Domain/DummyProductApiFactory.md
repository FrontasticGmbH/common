#  DummyProductApiFactory

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\DummyProductApiFactory`](../../../../src/php/ProductApiBundle/Domain/DummyProductApiFactory.php)

**Implements**: [`ProductApiFactory`](ProductApiFactory.md)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    ProductSearchApiFactory $productSearchApiFactory
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$productSearchApiFactory`|[`ProductSearchApiFactory`](../../ProductSearchApiBundle/Domain/ProductSearchApiFactory.md)||

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
