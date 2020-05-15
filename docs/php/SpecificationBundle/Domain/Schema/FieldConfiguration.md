#  FieldConfiguration

**Fully Qualified**: [`\Frontastic\Common\SpecificationBundle\Domain\Schema\FieldConfiguration`](../../../../../src/php/SpecificationBundle/Domain/Schema/FieldConfiguration.php)

## Methods

* [__construct()](#__construct)
* [fromSchema()](#fromschema)
* [getField()](#getfield)
* [getType()](#gettype)
* [getDefault()](#getdefault)

### __construct()

```php
public function __construct(
    string $field,
    string $type,
    mixed $default
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$field`|`string`||
`$type`|`string`||
`$default`|`mixed`||

Return Value: `mixed`

### fromSchema()

```php
static public function fromSchema(
    array $fieldSchema
): FieldConfiguration
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$fieldSchema`|`array`||

Return Value: [`FieldConfiguration`](FieldConfiguration.md)

### getField()

```php
public function getField(): string
```

Return Value: `string`

### getType()

```php
public function getType(): string
```

Return Value: `string`

### getDefault()

```php
public function getDefault(): mixed
```

Return Value: `mixed`

