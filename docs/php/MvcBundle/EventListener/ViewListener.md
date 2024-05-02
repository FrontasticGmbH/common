#  ViewListener

**Fully Qualified**: [`\Frontastic\Common\MvcBundle\EventListener\ViewListener`](../../../../src/php/MvcBundle/EventListener/ViewListener.php)

## Methods

* [addConverter()](#addconverter)
* [addYieldApplier()](#addyieldapplier)
* [onKernelView()](#onkernelview)

### addConverter()

```php
public function addConverter(
    ControllerResultConverter $converter
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$converter`|[`ControllerResultConverter`](../Controller/ResultConverter/ControllerResultConverter.md)||

Return Value: `void`

### addYieldApplier()

```php
public function addYieldApplier(
    ControllerYieldApplier $applier
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$applier`|[`ControllerYieldApplier`](../Controller/ResultConverter/ControllerYieldApplier.md)||

Return Value: `void`

### onKernelView()

```php
public function onKernelView(
    mixed $event
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$event`|`mixed`||

Return Value: `void`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
