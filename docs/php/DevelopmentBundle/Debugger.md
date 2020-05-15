#  Debugger

**Fully Qualified**: [`\Frontastic\Common\DevelopmentBundle\Debugger`](../../../src/php/DevelopmentBundle/Debugger.php)

This class deals as a var_dump() style gateway to logging debug-messages
during development. IT SHOULD NEVER BE USED IN PRODUCTION.

TODO: Use a CI scan to identify accidental commits of Debugger:: calls.

## Methods

* [log()](#log)
* [getMessages()](#getmessages)

### log()

```php
static public function log(
    mixed …$args
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`…$args`|`mixed`||

Return Value: `mixed`

### getMessages()

```php
static public function getMessages(): array
```

Return Value: `array`

