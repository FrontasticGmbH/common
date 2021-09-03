# `interface`  FieldVisitor

**Fully Qualified**: [`\Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor`](../../../../../src/php/SpecificationBundle/Domain/Schema/FieldVisitor.php)

## Methods

* [processField()](#processfield)

### processField()

```php
public function processField(
    FieldConfiguration $configuration,
    mixed $value,
    array $fieldPath
): mixed
```

*Note: You can, but you don't need to take care of nested "group" values,
those will be visited, too! Note that nested values are visited first,
then the group itself.*

Argument|Type|Default|Description
--------|----|-------|-----------
`$configuration`|[`FieldConfiguration`](FieldConfiguration.md)||
`$value`|`mixed`||
`$fieldPath`|`array`||Path of the field nesting e.g. ['groupField', 2] if this is the 3nd element in a group

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
