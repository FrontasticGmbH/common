#  ParamConverterListener

**Fully Qualified**: [`\Frontastic\Common\MvcBundle\EventListener\ParamConverterListener`](../../../../src/php/MvcBundle/EventListener/ParamConverterListener.php)

This replicates the SensioFrameworkExtraBundle behavior but keeps it simple
and without a dependency to allow usage outside Symfony Framework apps (Silex,
..).

## Methods

* [__construct()](#__construct)
* [onKernelController()](#onkernelcontroller)

### __construct()

```php
public function __construct(
    ServiceProvider $container
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$container`|[`ServiceProvider`](../ParamConverter/ServiceProvider.md)||

Return Value: `mixed`

### onKernelController()

```php
public function onKernelController(
    \Symfony\Component\HttpKernel\Event\ControllerEvent $event
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$event`|`\Symfony\Component\HttpKernel\Event\ControllerEvent`||

Return Value: `void`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
