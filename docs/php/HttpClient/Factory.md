#  Factory

**Fully Qualified**: [`\Frontastic\Common\HttpClient\Factory`](../../../src/php/HttpClient/Factory.php)

## Methods

* [__construct()](#__construct)
* [create()](#create)

### __construct()

```php
public function __construct(
    \Psr\Log\LoggerInterface $httpClientLogger,
    \Redis $redis = null,
    Options $defaultOptions = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$httpClientLogger`|`\Psr\Log\LoggerInterface`||
`$redis`|`\Redis`|`null`|
`$defaultOptions`|[`Options`](Options.md)|`null`|

Return Value: `mixed`

### create()

```php
public function create(
    mixed $clientIdentifier,
    Configuration $configuration = null
): HttpClient
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$clientIdentifier`|`mixed`||
`$configuration`|[`Configuration`](Configuration.md)|`null`|

Return Value: [`HttpClient`](../HttpClient.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
