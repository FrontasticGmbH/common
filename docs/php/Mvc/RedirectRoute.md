#  RedirectRoute

**Fully Qualified**: [`\Frontastic\Common\Mvc\RedirectRoute`](../../../src/php/Mvc/RedirectRoute.php)

## Methods

* [__construct()](#__construct)
* [getRouteName()](#getroutename)
* [getParameters()](#getparameters)
* [getResponse()](#getresponse)
* [getStatusCode()](#getstatuscode)

### __construct()

```php
public function __construct(
    string $routeName,
    array $parameters = [],
    mixed $response = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$routeName`|`string`||
`$parameters`|`array`|`[]`|
`$response`|`mixed`|`null`|

Return Value: `mixed`

### getRouteName()

```php
public function getRouteName(): string
```

Return Value: `string`

### getParameters()

```php
public function getParameters(): array
```

Return Value: `array`

### getResponse()

```php
public function getResponse(): ?\Symfony\Component\HttpFoundation\Response
```

Return Value: `?\Symfony\Component\HttpFoundation\Response`

### getStatusCode()

```php
public function getStatusCode(): int
```

Return Value: `int`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
