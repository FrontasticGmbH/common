# `interface`  SprykerClientInterface

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface`](../../../../src/php/SprykerBundle/Domain/SprykerClientInterface.php)

## Methods

* [get()](#get)
* [head()](#head)
* [post()](#post)
* [patch()](#patch)
* [delete()](#delete)

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
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$endpoint`|`string`||
`$headers`|`array`|`[]`|
`$body`|`string`|`''`|

Return Value: `mixed`

### delete()

```php
public function delete(
    string $endpoint,
    array $headers = []
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$endpoint`|`string`||
`$headers`|`array`|`[]`|

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
