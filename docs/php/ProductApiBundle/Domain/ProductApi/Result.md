#  Result

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result`](../../../../../src/php/ProductApiBundle/Domain/ProductApi/Result.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

**Implements**: `\Countable`, [`\IteratorAggregate`](https://www.php.net/manual/de/class.iteratoraggregate.php)

In general terms, REST APIs use offset pagination whereas GraphQL APIs use
cursor-based pagination.

Regardless the pagination implemented by your backend of choice, we highly
recommend you to use in both cases the property $nextCursor to store the
position where the pagination should continue.

Additionally, and only for GraphQL APIs, you can use $previousCursor to store
the position of the first element to allow backward pagination.

NOTE: the property $offset will be deprecated in a further commit.
Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`offset` | `int` |  | - | 
`total` | `int` |  | - | 
`previousCursor` | `string` |  | - | 
`nextCursor` | `string` |  | - | 
`count` | `int` |  | *Yes* | 
`items` | `array` | `[]` | *Yes* | 
`facets` | [`Result`](Result.md)\Facet[] | `[]` | - | 
`query` | [`Query`](Query.md) |  | *Yes* | The query used to generate this result (cloned)

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
