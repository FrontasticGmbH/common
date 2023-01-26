#  Customer

**Fully Qualified**: [`\Frontastic\Common\ReplicatorBundle\Domain\Customer`](../../../../src/php/ReplicatorBundle/Domain/Customer.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`name` | `string` |  | *Yes* | 
`secret` | `string` |  | *Yes* | 
`edition` | `string` | `'micro'` | *Yes* | 
`hasPaasModifications` | `bool` | `false` | *Yes* | 
`machineLimit` | `int` | `10` | *Yes* | Number of available Frontastic Machines
`machineRegionToProviderMap` | `array` | `[]` | *Yes* | Frontastic Machines Map to define providers use in each region
`features` | `array` | `[]` | *Yes* | 
`isTransient` | `bool` | `false` | *Yes* | Used to indicate this customer is only "half" configured or similar.
`configuration` | `array` | `[]` | *Yes* | 
`environments` | `array` | `['production', 'staging', 'development']` | *Yes* | 
`projects` | [`Project`](Project.md)[] | `[]` | *Yes* | 
`netlifyUrl` | `?string` | `null` | - | 

## Methods

* [getLowestEnvironment()](#getlowestenvironment)

### getLowestEnvironment()

```php
public function getLowestEnvironment(): string
```

*Get the environment with the lowest priority. This will return 'development' for the default environments.*

Return Value: `string`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
