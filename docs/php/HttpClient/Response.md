#  Response

**Fully Qualified**: [`\Frontastic\Common\HttpClient\Response`](../../../src/php/HttpClient/Response.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`status` | `int` |  | - | Response HTTP status code
`headers` | `string[]` |  | - | The HTTP headers from the response as a plain array
`body` | `string` |  | - | Response body
`rawApiOutput` | `\Psr\Http\Message\ResponseInterface` |  | - | Raw HTTP output response

## Methods

* [__toString()](#__tostring)

### __toString()

```php
public function __toString(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
