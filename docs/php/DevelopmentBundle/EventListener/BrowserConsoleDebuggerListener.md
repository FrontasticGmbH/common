#  BrowserConsoleDebuggerListener

**Fully Qualified**: [`\Frontastic\Common\DevelopmentBundle\EventListener\BrowserConsoleDebuggerListener`](../../../../src/php/DevelopmentBundle/EventListener/BrowserConsoleDebuggerListener.php)

## Methods

* [__construct()](#__construct)
* [onKernelResponse()](#onkernelresponse)

### __construct()

```php
public function __construct(
    JsonSerializer $serializer
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$serializer`|[`JsonSerializer`](../../JsonSerializer.md)||

Return Value: `mixed`

### onKernelResponse()

```php
public function onKernelResponse(
    \Symfony\Component\HttpKernel\Event\ResponseEvent $event
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$event`|`\Symfony\Component\HttpKernel\Event\ResponseEvent`||

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
