#  Response

**Fully Qualified**: [`\Frontastic\Common\HttpClient\Response`](../../../src/php/HttpClient/Response.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Description
--------|----|-------|-----------
`status`|`int`||Response HTTP status code
`headers`|`string[]`||The HTTP headers from the response as a plain array
`body`|`string`||Response body

## Methods

* [__toString()](#__tostring)

### __toString()

```php
public function __toString(): mixed
```

Return Value: `mixed`
