#  EndpointService

Fully Qualified: [`\Frontastic\Common\ReplicatorBundle\Domain\EndpointService`](../../../../src/php/ReplicatorBundle/Domain/EndpointService.php)

## Methods

* [addReplicationSource()](#addreplicationsource)
* [getReplicationSource()](#getreplicationsource)
* [addReplicationTarget()](#addreplicationtarget)
* [getReplicationTarget()](#getreplicationtarget)
* [dispatch()](#dispatch)

### addReplicationSource()

```php
public function addReplicationSource(
    string $channel,
    Source $source
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$channel`|`string`||
`$source`|[`Source`](Source.md)||

Return Value: `void`

### getReplicationSource()

```php
public function getReplicationSource(
    string $channel
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$channel`|`string`||

Return Value: `mixed`

### addReplicationTarget()

```php
public function addReplicationTarget(
    string $channel,
    Target $target
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$channel`|`string`||
`$target`|[`Target`](Target.md)||

Return Value: `void`

### getReplicationTarget()

```php
public function getReplicationTarget(
    string $channel
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$channel`|`string`||

Return Value: `mixed`

### dispatch()

```php
public function dispatch(
    Command $command
): Result
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$command`|[`Command`](Command.md)||

Return Value: [`Result`](Result.md)

