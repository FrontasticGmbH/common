#  ShopifyAccountMapper

**Fully Qualified**: [`\Frontastic\Common\ShopifyBundle\Domain\Mapper\ShopifyAccountMapper`](../../../../../src/php/ShopifyBundle/Domain/Mapper/ShopifyAccountMapper.php)

## Methods

* [mapDataToAccount()](#mapdatatoaccount)
* [mapDataToAddress()](#mapdatatoaddress)
* [mapAddressToData()](#mapaddresstodata)

### mapDataToAccount()

```php
public function mapDataToAccount(
    array $accountData
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountData`|`array`||

Return Value: [`Account`](../../../AccountApiBundle/Domain/Account.md)

### mapDataToAddress()

```php
public function mapDataToAddress(
    array $addressData
): ?Address
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$addressData`|`array`||

Return Value: ?[`Address`](../../../AccountApiBundle/Domain/Address.md)

### mapAddressToData()

```php
public function mapAddressToData(
    Address $address
): string
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$address`|[`Address`](../../../AccountApiBundle/Domain/Address.md)||

Return Value: `string`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
