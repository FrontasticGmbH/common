#  RequestVerifier

Fully Qualified: [`\Frontastic\Common\ReplicatorBundle\Domain\RequestVerifier`](../../../../src/php/ReplicatorBundle/Domain/RequestVerifier.php)




## Methods

* [isValid()](#isValid)
* [ensure()](#ensure)


### isValid()


```php
public function isValid(\Symfony\Component\HttpFoundation\Request $request, string $secret): bool
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|`\Symfony\Component\HttpFoundation\Request`|``|
`$secret`|`string`|``|

Return Value: `bool`

### ensure()


```php
public function ensure(\Symfony\Component\HttpFoundation\Request $request, string $secret): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|`\Symfony\Component\HttpFoundation\Request`|``|
`$secret`|`string`|``|

Return Value: `mixed`

