#  CustomerService

Fully Qualified: [`\Frontastic\Common\ReplicatorBundle\Domain\CustomerService`](../../../../src/php/ReplicatorBundle/Domain/CustomerService.php)




## Methods

* [__construct()](#construct)
* [getCustomers()](#getCustomers)
* [getCustomer()](#getCustomer)
* [getProject()](#getProject)
* [getForHost()](#getForHost)


### __construct()


```php
public function __construct(string customerDir, string deployedCustomers): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$customerDir`|`string`|``|
`$deployedCustomers`|`string`|``|

### getCustomers()


```php
public function getCustomers(): array
```







### getCustomer()


```php
public function getCustomer(string customerName): \Frontastic\Common\ReplicatorBundle\Domain\Customer
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$customerName`|`string`|``|

### getProject()


```php
public function getProject(string customerName, string projectName): \Frontastic\Common\ReplicatorBundle\Domain\Project
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$customerName`|`string`|``|
`$projectName`|`string`|``|

### getForHost()


```php
public function getForHost(string host): \Frontastic\Common\ReplicatorBundle\Domain\Customer
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$host`|`string`|``|

