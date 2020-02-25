#  Result

**Fully Qualified**: [`\Frontastic\Common\ReplicatorBundle\Domain\Result`](../../../../src/php/ReplicatorBundle/Domain/Result.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Description
--------|----|-------|-----------
`ok`|`bool`|`true`|
`payload`|`array`|`[]`|
`message`|`string`|`null`|
`file`|`string`||
`line`|`int`||
`stack`|`array`||

## Methods

* [fromThrowable()](#fromthrowable)

### fromThrowable()

```php
static public function fromThrowable(
    \Throwable $e
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$e`|[`\Throwable`](https://www.php.net/manual/de/class.throwable.php)||

Return Value: `mixed`
