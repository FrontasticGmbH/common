#  InvalidCustomDataSourceTypeException

**Fully Qualified**: [`\Frontastic\Common\SpecificationBundle\Domain\InvalidCustomDataSourceTypeException`](../../../../src/php/SpecificationBundle/Domain/InvalidCustomDataSourceTypeException.php)

**Extends**: `\InvalidArgumentException`

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`message` | `` |  | - | 
`error` | `` |  | - | 

## Methods

* [__construct()](#__construct)
* [getError()](#geterror)

### __construct()

```php
public function __construct(
    string $message,
    string $error
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$message`|`string`||
`$error`|`string`||

Return Value: `mixed`

### getError()

```php
public function getError(): string
```

Return Value: `string`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
