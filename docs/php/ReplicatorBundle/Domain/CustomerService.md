#  CustomerService

**Fully Qualified**: [`\Frontastic\Common\ReplicatorBundle\Domain\CustomerService`](../../../../src/php/ReplicatorBundle/Domain/CustomerService.php)

## Methods

* [__construct()](#__construct)
* [getCustomers()](#getcustomers)
* [getCustomer()](#getcustomer)
* [getProject()](#getproject)
* [getForHost()](#getforhost)

### __construct()

```php
public function __construct(
    string $customerDir,
    string $deployedCustomers
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$customerDir`|`string`||
`$deployedCustomers`|`string`||

Return Value: `mixed`

### getCustomers()

```php
public function getCustomers(): array
```

Return Value: `array`

### getCustomer()

```php
public function getCustomer(
    string $customerName
): Customer
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$customerName`|`string`||

Return Value: [`Customer`](Customer.md)

### getProject()

```php
public function getProject(
    string $customerName,
    string $projectName
): Project
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$customerName`|`string`||
`$projectName`|`string`||

Return Value: [`Project`](Project.md)

### getForHost()

```php
public function getForHost(
    string $host
): Customer
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$host`|`string`||

Return Value: [`Customer`](Customer.md)

