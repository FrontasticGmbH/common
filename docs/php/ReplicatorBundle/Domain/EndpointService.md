#  EndpointService

Fully Qualified: [`\Frontastic\Common\ReplicatorBundle\Domain\EndpointService`](../../../../src/php/ReplicatorBundle/Domain/EndpointService.php)




## Methods

### addReplicationSource

`function addReplicationSource(string channel, \Frontastic\Common\ReplicatorBundle\Domain\Source source): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$channel`|`string`|``|
`$source`|`\Frontastic\Common\ReplicatorBundle\Domain\Source`|``|

### getReplicationSource

`function getReplicationSource(string channel): mixed`






Argument|Type|Default|Description
--------|----|-------|-----------
`$channel`|`string`|``|

### addReplicationTarget

`function addReplicationTarget(string channel, \Frontastic\Common\ReplicatorBundle\Domain\Target target): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$channel`|`string`|``|
`$target`|`\Frontastic\Common\ReplicatorBundle\Domain\Target`|``|

### getReplicationTarget

`function getReplicationTarget(string channel): mixed`






Argument|Type|Default|Description
--------|----|-------|-----------
`$channel`|`string`|``|

### dispatch

`function dispatch(\Frontastic\Common\ReplicatorBundle\Domain\Command command): \Frontastic\Common\ReplicatorBundle\Domain\Result`






Argument|Type|Default|Description
--------|----|-------|-----------
`$command`|`\Frontastic\Common\ReplicatorBundle\Domain\Command`|``|

