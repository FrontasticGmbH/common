#  GroupFieldConfiguration

**Fully Qualified**: [`\Frontastic\Common\SpecificationBundle\Domain\Schema\GroupFieldConfiguration`](../../../../../src/php/SpecificationBundle/Domain/Schema/GroupFieldConfiguration.php)

**Extends**: [`FieldConfiguration`](FieldConfiguration.md)

## Methods

* [doCreateFromSchema()](#docreatefromschema)
* [processValueIfRequired()](#processvalueifrequired)

### doCreateFromSchema()

```php
static public function doCreateFromSchema(
    string $type,
    array $fieldSchema
): FieldConfiguration
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$type`|`string`||
`$fieldSchema`|`array`||

Return Value: [`FieldConfiguration`](FieldConfiguration.md)

### processValueIfRequired()

```php
public function processValueIfRequired(
    mixed $value,
    FieldVisitor $fieldVisitor
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$value`|`mixed`||
`$fieldVisitor`|[`FieldVisitor`](FieldVisitor.md)||

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
