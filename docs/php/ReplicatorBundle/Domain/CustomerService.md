#  CustomerService

Fully Qualified: [`\Frontastic\Common\ReplicatorBundle\Domain\CustomerService`](../../../../src/php/ReplicatorBundle/Domain/CustomerService.php)




## Methods

### __construct

`function __construct(string customerDir, string deployedCustomers): mixed`






Argument|Type|Default|Description
--------|----|-------|-----------
`$customerDir`|`string`|``|
`$deployedCustomers`|`string`|``|

### getCustomers

`function getCustomers(): array`




**


### getCustomer

`function getCustomer(string customerName): \Frontastic\Common\ReplicatorBundle\Domain\Customer`






Argument|Type|Default|Description
--------|----|-------|-----------
`$customerName`|`string`|``|

### getProject

`function getProject(string customerName, string projectName): \Frontastic\Common\ReplicatorBundle\Domain\Project`






Argument|Type|Default|Description
--------|----|-------|-----------
`$customerName`|`string`|``|
`$projectName`|`string`|``|

### getForHost

`function getForHost(string host): \Frontastic\Common\ReplicatorBundle\Domain\Customer`






Argument|Type|Default|Description
--------|----|-------|-----------
`$host`|`string`|``|

