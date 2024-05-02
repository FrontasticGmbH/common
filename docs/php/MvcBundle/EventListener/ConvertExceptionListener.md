#  ConvertExceptionListener

**Fully Qualified**: [`\Frontastic\Common\MvcBundle\EventListener\ConvertExceptionListener`](../../../../src/php/MvcBundle/EventListener/ConvertExceptionListener.php)

## Methods

* [__construct()](#__construct)
* [onKernelException()](#onkernelexception)

### __construct()

```php
public function __construct(
    ?\Psr\Log\LoggerInterface $logger = null,
    array $exceptionClassMap = []
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$logger`|`?\Psr\Log\LoggerInterface`|`null`|
`$exceptionClassMap`|`array`|`[]`|

Return Value: `mixed`

### onKernelException()

```php
public function onKernelException(
    mixed $event
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$event`|`mixed`||

Return Value: `void`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
