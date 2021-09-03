#  FieldConfiguration

**Fully Qualified**: [`\Frontastic\Common\SpecificationBundle\Domain\Schema\FieldConfiguration`](../../../../../src/php/SpecificationBundle/Domain/Schema/FieldConfiguration.php)

## Methods

* [fromSchema()](#fromschema)
* [getField()](#getfield)
* [getType()](#gettype)
* [getDefault()](#getdefault)
* [processValueIfRequired()](#processvalueifrequired)
* [isTranslatable()](#istranslatable)

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

### processValueIfRequired()

```php
public function processValueIfRequired(
    mixed $value,
    FieldVisitor $fieldVisitor,
    array $fieldPath
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$value`|`mixed`||
`$fieldVisitor`|[`FieldVisitor`](FieldVisitor.md)||
`$fieldPath`|`array`||

Return Value: `mixed`

### isTranslatable()

```php
public function isTranslatable(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
