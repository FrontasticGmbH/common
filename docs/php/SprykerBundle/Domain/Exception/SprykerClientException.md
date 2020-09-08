#  SprykerClientException

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\Exception\SprykerClientException`](../../../../../src/php/SprykerBundle/Domain/Exception/SprykerClientException.php)

**Extends**: `\RuntimeException`

## Methods

* [createFromGuzzleClientException()](#createfromguzzleclientexception)

### createFromGuzzleClientException()

```php
static public function createFromGuzzleClientException(
    \GuzzleHttp\Exception\ClientException $clientException,
    ?string $endpoint = null
): self
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$clientException`|`\GuzzleHttp\Exception\ClientException`||
`$endpoint`|`?string`|`null`|

Return Value: `self`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
