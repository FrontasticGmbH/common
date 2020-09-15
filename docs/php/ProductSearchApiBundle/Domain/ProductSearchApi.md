# `interface`  ProductSearchApi

**Fully Qualified**: [`\Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi`](../../../../src/php/ProductSearchApiBundle/Domain/ProductSearchApi.php)

## Methods

* [query()](#query)
* [getSearchableAttributes()](#getsearchableattributes)
* [getDangerousInnerClient()](#getdangerousinnerclient)

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

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

*Get *dangerous* inner client*

This method exists to enable you to use features which are not yet part
of the abstraction layer.

Be aware that any usage of this method might seriously hurt backwards
compatibility and the future abstractions might differ a lot from the
vendor provided abstraction.

Use this with care for features necessary in your customer and talk with
Frontastic about provising an abstraction.

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
