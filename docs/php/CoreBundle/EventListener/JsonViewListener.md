#  JsonViewListener

**Fully Qualified**: [`\Frontastic\Common\CoreBundle\EventListener\JsonViewListener`](../../../../src/php/CoreBundle/EventListener/JsonViewListener.php)

## Methods

* [__construct()](#__construct)
* [onKernelView()](#onkernelview)

### __construct()

```php
public function __construct(
    JsonSerializer $jsonSerializer
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$jsonSerializer`|[`JsonSerializer`](../../JsonSerializer.md)||

Return Value: `mixed`

### onKernelView()

```php
public function onKernelView(
    \Symfony\Component\HttpKernel\Event\ViewEvent $event
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$event`|`\Symfony\Component\HttpKernel\Event\ViewEvent`||

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
