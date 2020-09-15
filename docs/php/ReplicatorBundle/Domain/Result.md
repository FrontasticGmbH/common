#  Result

**Fully Qualified**: [`\Frontastic\Common\ReplicatorBundle\Domain\Result`](../../../../src/php/ReplicatorBundle/Domain/Result.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`ok` | `bool` | `true` | *Yes* | 
`payload` | `array` | `[]` | *Yes* | 
`message` | `string` | `null` | - | 
`file` | `string` |  | - | 
`line` | `int` |  | - | 
`stack` | `array` |  | - | 

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

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
