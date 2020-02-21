#  Result

Fully Qualified: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result`](../../../../../src/php/ProductApiBundle/Domain/ProductApi/Result.php)



Property|Type|Default|Description
--------|----|-------|-----------
`offset`|`int`|``|
`total`|`int`|``|
`count`|`int`|``|
`items`|`array`|`[]`|
`facets`|`Facet[]`|`[]`|
`query`|`Query`|``|The query used to generate this result (cloned)

## Methods

* [getIterator()](#getIterator)
* [count()](#count)


### getIterator()


```php
public function getIterator(): \Traversable
```







Return Value: `\Traversable`

### count()


```php
public function count(): int
```







Return Value: `int`

