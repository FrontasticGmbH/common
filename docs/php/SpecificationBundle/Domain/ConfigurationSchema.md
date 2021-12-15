#  ConfigurationSchema

**Fully Qualified**: [`\Frontastic\Common\SpecificationBundle\Domain\ConfigurationSchema`](../../../../src/php/SpecificationBundle/Domain/ConfigurationSchema.php)

## Methods

* [fromSchemaAndConfiguration()](#fromschemaandconfiguration)
* [hasField()](#hasfield)
* [getFieldValue()](#getfieldvalue)
* [getCompleteValues()](#getcompletevalues)
* [getFieldConfigurations()](#getfieldconfigurations)

### fromSchemaAndConfiguration()

```php
static public function fromSchemaAndConfiguration(
    array $schema,
    array $configuration
): self
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$schema`|`array`||
`$configuration`|`array`||

Return Value: `self`

### hasField()

```php
public function hasField(
    string $fieldName
): bool
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$fieldName`|`string`||

Return Value: `bool`

### getFieldValue()

```php
public function getFieldValue(
    string $fieldName,
    FieldVisitor $fieldVisitor = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$fieldName`|`string`||
`$fieldVisitor`|[`FieldVisitor`](Schema/FieldVisitor.md)|`null`|

Return Value: `mixed`

### getCompleteValues()

```php
public function getCompleteValues(
    FieldVisitor $fieldVisitor = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$fieldVisitor`|[`FieldVisitor`](Schema/FieldVisitor.md)|`null`|

Return Value: `mixed`

### getFieldConfigurations()

```php
public function getFieldConfigurations(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
