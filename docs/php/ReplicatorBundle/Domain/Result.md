#  Result

Fully Qualified: [`\Frontastic\Common\ReplicatorBundle\Domain\Result`](../../../../src/php/ReplicatorBundle/Domain/Result.php)



Property|Type|Default|Description
--------|----|-------|-----------
`ok`|`bool`|`true`|
`payload`|`array`|`[]`|
`message`|`string`|`null`|
`file`|`string`|``|
`line`|`int`|``|
`stack`|`array`|``|

## Methods

* [fromThrowable()](#fromThrowable)


### fromThrowable()


```php
static public function fromThrowable(\Throwable $e): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$e`|`\Throwable`|``|

Return Value: `mixed`

