# `interface`  JsonInterface

**Fully Qualified**: [`\Frontastic\Common\CoreBundle\Domain\Json\JsonInterface`](../../../../../src/php/CoreBundle/Domain/Json/JsonInterface.php)

## Methods

* [encode()](#encode)
* [decode()](#decode)

### encode()

```php
static public function encode(
    mixed $data,
    int $flags,
    int $depth
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$data`|`mixed`||
`$flags`|`int`||
`$depth`|`int`||

Return Value: `mixed`

### decode()

```php
static public function decode(
    mixed $data,
    mixed $associative,
    int $depth,
    int $flags,
    mixed $useNativeDecoder
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$data`|`mixed`||
`$associative`|`mixed`||
`$depth`|`int`||
`$flags`|`int`||
`$useNativeDecoder`|`mixed`||

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
