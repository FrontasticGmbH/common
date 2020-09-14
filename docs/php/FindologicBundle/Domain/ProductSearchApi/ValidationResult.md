#  ValidationResult

**Fully Qualified**: [`\Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\ValidationResult`](../../../../../src/php/FindologicBundle/Domain/ProductSearchApi/ValidationResult.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`isSupported` | `bool` |  | - | 
`validationError` | `?string` | `null` | - | 

## Methods

* [createValid()](#createvalid)
* [createUnsupported()](#createunsupported)

### createValid()

```php
static public function createValid(): ValidationResult
```

Return Value: [`ValidationResult`](ValidationResult.md)

### createUnsupported()

```php
static public function createUnsupported(
    string $message
): ValidationResult
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$message`|`string`||

Return Value: [`ValidationResult`](ValidationResult.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
