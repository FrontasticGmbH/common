#  SprykerClient

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\SprykerClient`](../../../../src/php/SprykerBundle/Domain/SprykerClient.php)

**Implements**: [`SprykerClientInterface`](SprykerClientInterface.md)

## Methods

* [__construct()](#__construct)
* [get()](#get)
* [head()](#head)
* [post()](#post)
* [patch()](#patch)
* [delete()](#delete)

### __construct()

```php
public function __construct(
    HttpClient $client,
    string $url,
    ExceptionFactoryInterface $exceptionFactory
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`HttpClient`](../../HttpClient.md)||
`$url`|`string`||
`$exceptionFactory`|[`ExceptionFactoryInterface`](Exception/ExceptionFactoryInterface.md)||

Return Value: `mixed`

### get()

```php
public function get(
    string $endpoint,
    array $headers = [],
    string $mode = self::MODE_SYNC
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$endpoint`|`string`||
`$headers`|`array`|`[]`|
`$mode`|`string`|`self::MODE_SYNC`|

Return Value: `mixed`

### head()

```php
public function head(
    string $endpoint,
    array $headers = []
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$endpoint`|`string`||
`$headers`|`array`|`[]`|

Return Value: `mixed`

### post()

```php
public function post(
    string $endpoint,
    array $headers = [],
    string $body = '',
    string $mode = self::MODE_SYNC
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$endpoint`|`string`||
`$headers`|`array`|`[]`|
`$body`|`string`|`''`|
`$mode`|`string`|`self::MODE_SYNC`|

Return Value: `mixed`

### patch()

```php
public function patch(
    string $endpoint,
    array $headers = [],
    string $body = ''
): \WoohooLabs\Yang\JsonApi\Response\JsonApiResponse
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$endpoint`|`string`||
`$headers`|`array`|`[]`|
`$body`|`string`|`''`|

Return Value: `\WoohooLabs\Yang\JsonApi\Response\JsonApiResponse`

### delete()

```php
public function delete(
    string $endpoint,
    array $headers = []
): \WoohooLabs\Yang\JsonApi\Response\JsonApiResponse
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$endpoint`|`string`||
`$headers`|`array`|`[]`|

Return Value: `\WoohooLabs\Yang\JsonApi\Response\JsonApiResponse`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
