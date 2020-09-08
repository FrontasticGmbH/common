#  SprykerServerException

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\Exception\SprykerServerException`](../../../../../src/php/SprykerBundle/Domain/Exception/SprykerServerException.php)

**Extends**: `\RuntimeException`

## Methods

* [createFromGuzzleClientException()](#createfromguzzleclientexception)

### createFromGuzzleClientException()

```php
static public function createFromGuzzleClientException(
    \GuzzleHttp\Exception\ServerException $clientException,
    ?string $endpoint = null
): self
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$clientException`|`\GuzzleHttp\Exception\ServerException`||
`$endpoint`|`?string`|`null`|

Return Value: `self`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
