#  MapperResolver

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\MapperResolver`](../../../../src/php/SprykerBundle/Domain/MapperResolver.php)

## Methods

* [__construct()](#__construct)
* [getMapper()](#getmapper)
* [getExtendedMapper()](#getextendedmapper)

### __construct()

```php
public function __construct(
    iterable $mappers
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$mappers`|`iterable`||

Return Value: `mixed`

### getMapper()

```php
public function getMapper(
    string $name
): MapperInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$name`|`string`||

Return Value: [`MapperInterface`](MapperInterface.md)

### getExtendedMapper()

```php
public function getExtendedMapper(
    string $name
): ExtendedMapperInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$name`|`string`||

Return Value: [`ExtendedMapperInterface`](ExtendedMapperInterface.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
