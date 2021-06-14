#  JsonSerializer

**Fully Qualified**: [`\Frontastic\Common\JsonSerializer`](../../src/php/JsonSerializer.php)

## Methods

* [__construct()](#__construct)
* [addEnhancer()](#addenhancer)
* [clearEnhancers()](#clearenhancers)
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
    ObjectEnhancer $enhancer
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$enhancer`|[`ObjectEnhancer`](JsonSerializer/ObjectEnhancer.md)||

Return Value: `void`

### clearEnhancers()

```php
public function clearEnhancers(): void
```

Return Value: `void`

### serialize()

```php
public function serialize(
    mixed $item,
    mixed $visitedIds = array()
): mixed
```

*Prepares an object for json serialization. Does *not* actually encode it as JSON.*

Is there a sensible refactoring to reduce this methods complexity?
Otherwise we consider it fine, since its tested anyways:

Argument|Type|Default|Description
--------|----|-------|-----------
`$item`|`mixed`||
`$visitedIds`|`mixed`|`array()`|

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
