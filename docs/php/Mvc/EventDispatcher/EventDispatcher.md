#  EventDispatcher

**Fully Qualified**: [`\Frontastic\Common\Mvc\EventDispatcher\EventDispatcher`](../../../../src/php/Mvc/EventDispatcher/EventDispatcher.php)

## Methods

* [__construct()](#__construct)
* [dispatch()](#dispatch)

### __construct()

```php
public function __construct(
    \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$eventDispatcher`|`\Symfony\Component\EventDispatcher\EventDispatcherInterface`||

Return Value: `mixed`

### dispatch()

```php
public function dispatch(
    object $event,
    ?string $eventName = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$event`|`object`||
`$eventName`|`?string`|`null`|

Return Value: `void`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
