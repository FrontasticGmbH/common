#  TemplateView

**Fully Qualified**: [`\Frontastic\Common\Mvc\TemplateView`](../../../src/php/Mvc/TemplateView.php)

## Methods

* [__construct()](#__construct)
* [getViewParams()](#getviewparams)
* [getActionTemplateName()](#getactiontemplatename)
* [getStatusCode()](#getstatuscode)
* [getHeaders()](#getheaders)

### __construct()

```php
public function __construct(
    mixed $viewParams,
    ?string $actionTemplateName = null,
    int $statusCode = 200,
    array $headers = []
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$viewParams`|`mixed`||
`$actionTemplateName`|`?string`|`null`|
`$statusCode`|`int`|`200`|
`$headers`|`array`|`[]`|

Return Value: `mixed`

### getViewParams()

```php
public function getViewParams(): array
```

Return Value: `array`

### getActionTemplateName()

```php
public function getActionTemplateName(): ?string
```

Return Value: `?string`

### getStatusCode()

```php
public function getStatusCode(): int
```

Return Value: `int`

### getHeaders()

```php
public function getHeaders(): array
```

Return Value: `array`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
