#  DefaultProjectApiFactory

**Fully Qualified**: [`\Frontastic\Common\ProjectApiBundle\Domain\DefaultProjectApiFactory`](../../../../src/php/ProjectApiBundle/Domain/DefaultProjectApiFactory.php)

**Implements**: [`ProjectApiFactory`](ProjectApiFactory.md)

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
): ProjectApi
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../ReplicatorBundle/Domain/Project.md)||

Return Value: [`ProjectApi`](ProjectApi.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
