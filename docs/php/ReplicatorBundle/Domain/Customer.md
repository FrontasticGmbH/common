#  Customer

Fully Qualified: [`\Frontastic\Common\ReplicatorBundle\Domain\Customer`](../../../../src/php/ReplicatorBundle/Domain/Customer.php)



Property|Type|Default|Description
--------|----|-------|-----------
`name`|`string`|``|
`secret`|`string`|``|
`edition`|`string`|`'micro'`|
`features`|`array`|`[]`|
`isTransient`|`bool`|`false`|Used to indicate this customer is only "half" configured or similar.
`configuration`|`array`|`[]`|
`environments`|`array`|`['production', 'staging', 'development']`|
`projects`|`Project[]`|`[]`|

## Methods

* [getLowestEnvironment()](#getlowestenvironment)


### getLowestEnvironment()


```php
public function getLowestEnvironment(
    
): string
```


*Get the environment with the lowest priority. This will return 'development' for the default environments.*




Return Value: `string`

