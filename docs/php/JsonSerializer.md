#  JsonSerializer

Fully Qualified: [`\Frontastic\Common\JsonSerializer`](../../src/php/JsonSerializer.php)




## Methods

* [__construct()](#construct)
* [addEnhancer()](#addenhancer)
* [serialize()](#serialize)


### __construct()


```php
public function __construct(
    array $propertyExcludeList = [],
    iterable $objectEnhancers = []
): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$propertyExcludeList`|`array`|`[]`|
`$objectEnhancers`|`iterable`|`[]`|

Return Value: `mixed`

### addEnhancer()


```php
public function addEnhancer(
    JsonSerializer\ObjectEnhancer $enhancer
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$enhancer`|`JsonSerializer\ObjectEnhancer`||

Return Value: `void`

### serialize()


```php
public function serialize(
    mixed $item,
    mixed $visitedIds = array()
): mixed
```


*Is there a sensible refactoring to reduce this methods compleixty?
Otherwise we consider it fine, since its tested anyways:*



Argument|Type|Default|Description
--------|----|-------|-----------
`$item`|`mixed`||
`$visitedIds`|`mixed`|`array()`|

Return Value: `mixed`

