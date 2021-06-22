# `interface`  Target

**Fully Qualified**: [`\Frontastic\Common\ReplicatorBundle\Domain\Target`](../../../../src/php/ReplicatorBundle/Domain/Target.php)

## Methods

* [lastUpdate()](#lastupdate)
* [replicate()](#replicate)

### lastUpdate()

```php
public function lastUpdate(): string
```

*Returns the latest sequence revision securely stored in the Target.*

Return Value: `string`

### replicate()

```php
public function replicate(
    array $updates
): void
```

*Store all changes in $updates in the corresponding order. Throw
exception if a change cannot be stored.*

Argument|Type|Default|Description
--------|----|-------|-----------
`$updates`|`array`||

Return Value: `void`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
