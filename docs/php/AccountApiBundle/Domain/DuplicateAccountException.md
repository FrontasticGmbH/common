#  DuplicateAccountException

**Fully Qualified**: [`\Frontastic\Common\AccountApiBundle\Domain\DuplicateAccountException`](../../../../src/php/AccountApiBundle/Domain/DuplicateAccountException.php)

**Extends**: `\RuntimeException`

## Methods

* [__construct()](#__construct)

### __construct()

```php
public function __construct(
    string $email,
    int $code,
    \Throwable $previous = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`||
`$code`|`int`||
`$previous`|[`\Throwable`](https://www.php.net/manual/de/class.throwable.php)|`null`|

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
