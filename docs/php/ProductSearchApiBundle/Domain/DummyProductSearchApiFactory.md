#  DummyProductSearchApiFactory

**Fully Qualified**: [`\Frontastic\Common\ProductSearchApiBundle\Domain\DummyProductSearchApiFactory`](../../../../src/php/ProductSearchApiBundle/Domain/DummyProductSearchApiFactory.php)

**Implements**: [`ProductSearchApiFactory`](ProductSearchApiFactory.md)

It's purpose is to have a placeholder for the Frontastic Next.js projects.
Because if entries for the different APIs are missing in project.yml, the API
Hub is not working anymore.

## Methods

* [factor()](#factor)

### factor()

```php
public function factor(
    Project $project
): ProductSearchApi
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../ReplicatorBundle/Domain/Project.md)||

Return Value: [`ProductSearchApi`](ProductSearchApi.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
