#  Response

**Fully Qualified**: [`\Frontastic\Common\HttpClient\Response`](../../../src/php/HttpClient/Response.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`status` | `int` |  | *Yes* | Response HTTP status code
`headers` | `string[]` | `[]` | *Yes* | The HTTP headers from the response as a plain array
`body` | `string` |  | - | Response body
`rawApiOutput` | `\Psr\Http\Message\ResponseInterface` |  | - | Raw HTTP output response

## Methods

* [getHeaderValue()](#getheadervalue)
* [__toString()](#__tostring)

### getHeaderValue()

```php
public function getHeaderValue(
    string $header
): ?string
```

*Get the header value for the given header name ignoring the case of the header. If the header does not exist,
null is returned.*

Argument|Type|Default|Description
--------|----|-------|-----------
`$header`|`string`||

Return Value: `?string`

### __toString()

```php
public function __toString(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
