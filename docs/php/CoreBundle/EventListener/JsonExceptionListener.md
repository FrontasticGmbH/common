#  JsonExceptionListener

**Fully Qualified**: [`\Frontastic\Common\CoreBundle\EventListener\JsonExceptionListener`](../../../../src/php/CoreBundle/EventListener/JsonExceptionListener.php)

## Methods

* [__construct()](#__construct)
* [onKernelException()](#onkernelexception)

### __construct()

```php
public function __construct(
    mixed $debug = false
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$debug`|`mixed`|`false`|

Return Value: `mixed`

### onKernelException()

```php
public function onKernelException(
    \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$event`|`\Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent`||

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
