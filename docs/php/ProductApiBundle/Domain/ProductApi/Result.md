#  Result

Fully Qualified: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result`](../../../../../src/php/ProductApiBundle/Domain/ProductApi/Result.php)



Property|Type|Default|Description
--------|----|-------|-----------
`offset`|`int`|``|
`total`|`int`|``|
`count`|`int`|``|
`items`|`array`|`[]`|
`facets`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Facet[]`|`[]`|
`query`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query`|``|The query used to generate this result (cloned)

## Methods

* [getIterator()](#getIterator)
* [count()](#count)


### getIterator()


```php
public function getIterator(): \Traversable
```







### count()


```php
public function count(): int
```







