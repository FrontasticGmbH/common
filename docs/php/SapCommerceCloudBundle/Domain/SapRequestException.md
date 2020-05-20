#  SapRequestException

**Fully Qualified**: [`\Frontastic\Common\SapCommerceCloudBundle\Domain\SapRequestException`](../../../../src/php/SapCommerceCloudBundle/Domain/SapRequestException.php)

**Extends**: `\RuntimeException`

## Methods

* [__construct()](#__construct)
* [hasErrorType()](#haserrortype)
* [fromResponse()](#fromresponse)

### __construct()

```php
public function __construct(
    string $message = '',
    array $errorTypes = [],
    int $code,
    \Throwable $previous = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$message`|`string`|`''`|
`$errorTypes`|`array`|`[]`|
`$code`|`int`||
`$previous`|[`\Throwable`](https://www.php.net/manual/de/class.throwable.php)|`null`|

Return Value: `mixed`

### hasErrorType()

```php
public function hasErrorType(
    string $type
): bool
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$type`|`string`||

Return Value: `bool`

### fromResponse()

```php
static public function fromResponse(
    Response $response
): SapRequestException
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$response`|[`Response`](../../HttpClient/Response.md)||

Return Value: [`SapRequestException`](SapRequestException.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
