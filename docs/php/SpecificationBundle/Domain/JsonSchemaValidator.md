#  JsonSchemaValidator

**Fully Qualified**: [`\Frontastic\Common\SpecificationBundle\Domain\JsonSchemaValidator`](../../../../src/php/SpecificationBundle/Domain/JsonSchemaValidator.php)

## Methods

* [validate()](#validate)
* [parse()](#parse)

### validate()

```php
public function validate(
    mixed $toValidate,
    string $schemaFile,
    array $schemaLibraryFiles = []
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$toValidate`|`mixed`||
`$schemaFile`|`string`||
`$schemaLibraryFiles`|`array`|`[]`|

Return Value: `array`

### parse()

```php
public function parse(
    string $toParse,
    string $schemaFile,
    array $schemaLibraryFiles = []
): object
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$toParse`|`string`||
`$schemaFile`|`string`||
`$schemaLibraryFiles`|`array`|`[]`|

Return Value: `object`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
