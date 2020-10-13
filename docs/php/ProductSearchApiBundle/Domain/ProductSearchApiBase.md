# `abstract`  ProductSearchApiBase

**Fully Qualified**: [`\Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase`](../../../../src/php/ProductSearchApiBundle/Domain/ProductSearchApiBase.php)

**Implements**: [`ProductSearchApi`](ProductSearchApi.md)

## Methods

* [query()](#query)
* [getSearchableAttributes()](#getsearchableattributes)

### query()

```php
public function query(
    Query\ProductQuery $query
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](../../ProductApiBundle/Domain/ProductApi/Query.md)\ProductQuery||

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

### getSearchableAttributes()

```php
public function getSearchableAttributes(): \GuzzleHttp\Promise\PromiseInterface
```

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
