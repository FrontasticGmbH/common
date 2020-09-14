# `interface`  ExceptionFactoryInterface

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\Exception\ExceptionFactoryInterface`](../../../../../src/php/SprykerBundle/Domain/Exception/ExceptionFactoryInterface.php)

## Methods

* [createFromGuzzleClientException()](#createfromguzzleclientexception)
* [createFromGuzzleServerException()](#createfromguzzleserverexception)

### createFromGuzzleClientException()

```php
public function createFromGuzzleClientException(
    \GuzzleHttp\Exception\ClientException $clientException,
    ?string $endpoint = null
): SprykerClientException
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$clientException`|`\GuzzleHttp\Exception\ClientException`||
`$endpoint`|`?string`|`null`|

Return Value: [`SprykerClientException`](SprykerClientException.md)

### createFromGuzzleServerException()

```php
public function createFromGuzzleServerException(
    \GuzzleHttp\Exception\ServerException $serverException,
    ?string $endpoint = null
): SprykerServerException
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$serverException`|`\GuzzleHttp\Exception\ServerException`||
`$endpoint`|`?string`|`null`|

Return Value: [`SprykerServerException`](SprykerServerException.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
