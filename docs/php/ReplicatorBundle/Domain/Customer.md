#  Customer

**Fully Qualified**: [`\Frontastic\Common\ReplicatorBundle\Domain\Customer`](../../../../src/php/ReplicatorBundle/Domain/Customer.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`name` | `string` |  | - | 
`secret` | `string` |  | - | 
`edition` | `string` | `'micro'` | - | 
`hasPaasModifications` | `bool` | `false` | - | 
`features` | `array` | `[]` | - | 
`isTransient` | `bool` | `false` | - | Used to indicate this customer is only "half" configured or similar.
`configuration` | `array` | `[]` | - | 
`environments` | `array` | `['production', 'staging', 'development']` | - | 
`projects` | [`Project`](Project.md)[] | `[]` | - | 

## Methods

* [getLowestEnvironment()](#getlowestenvironment)

### getLowestEnvironment()

```php
public function getLowestEnvironment(): string
```

*Get the environment with the lowest priority. This will return 'development' for the default environments.*

Return Value: `string`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
