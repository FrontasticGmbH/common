#  Result

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result`](../../../../../src/php/ProductApiBundle/Domain/ProductApi/Result.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

**Implements**: `\Countable`, [`\IteratorAggregate`](https://www.php.net/manual/de/class.iteratoraggregate.php)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`offset` | `int` |  | - | 
`total` | `int` |  | - | 
`count` | `int` |  | - | 
`items` | `array` | `[]` | - | 
`facets` | [`Result`](Result.md)\Facet[] | `[]` | - | 
`query` | [`Query`](Query.md) |  | - | The query used to generate this result (cloned)

## Methods

* [getIterator()](#getiterator)
* [count()](#count)

### getIterator()

```php
public function getIterator(): \Traversable
```

Return Value: [`\Traversable`](https://www.php.net/manual/de/class.traversable.php)

### count()

```php
public function count(): int
```

Return Value: `int`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
