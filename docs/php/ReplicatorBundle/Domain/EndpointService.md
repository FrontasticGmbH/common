#  EndpointService

Fully Qualified: [`\Frontastic\Common\ReplicatorBundle\Domain\EndpointService`](../../../../src/php/ReplicatorBundle/Domain/EndpointService.php)




## Methods

* [addReplicationSource()](#addReplicationSource)
* [getReplicationSource()](#getReplicationSource)
* [addReplicationTarget()](#addReplicationTarget)
* [getReplicationTarget()](#getReplicationTarget)
* [dispatch()](#dispatch)


### addReplicationSource()


```php
public function addReplicationSource(string $channel, [Source](Source.md) $source): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$channel`|`string`|``|
`$source`|`[Source](Source.md)`|``|

### getReplicationSource()


```php
public function getReplicationSource(string $channel): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$channel`|`string`|``|

### addReplicationTarget()


```php
public function addReplicationTarget(string $channel, [Target](Target.md) $target): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$channel`|`string`|``|
`$target`|`[Target](Target.md)`|``|

### getReplicationTarget()


```php
public function getReplicationTarget(string $channel): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$channel`|`string`|``|

### dispatch()


```php
public function dispatch([Command](Command.md) $command): [Result](Result.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$command`|`[Command](Command.md)`|``|

