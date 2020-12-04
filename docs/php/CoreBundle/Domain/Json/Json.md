#  Json

**Fully Qualified**: [`\Frontastic\Common\CoreBundle\Domain\Json\Json`](../../../../../src/php/CoreBundle/Domain/Json/Json.php)

**Implements**: [`JsonInterface`](JsonInterface.md)

## Methods

* [encode()](#encode)
* [decode()](#decode)

### encode()

```php
static public function encode(
    mixed $data,
    int $flags,
    int $depth = 512
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$data`|`mixed`||
`$flags`|`int`||
`$depth`|`int`|`512`|

Return Value: `mixed`

### decode()

```php
static public function decode(
    mixed $data,
    mixed $associative = false,
    int $depth = 512,
    int $flags,
    mixed $useNativeDecoder = false
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$data`|`mixed`||
`$associative`|`mixed`|`false`|
`$depth`|`int`|`512`|
`$flags`|`int`||
`$useNativeDecoder`|`mixed`|`false`|

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
