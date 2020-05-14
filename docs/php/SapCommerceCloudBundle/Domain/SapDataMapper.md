#  SapDataMapper

**Fully Qualified**: [`\Frontastic\Common\SapCommerceCloudBundle\Domain\SapDataMapper`](../../../../src/php/SapCommerceCloudBundle/Domain/SapDataMapper.php)

## Methods

* [__construct()](#__construct)
* [mapDataToProduct()](#mapdatatoproduct)
* [mapDataToCategories()](#mapdatatocategories)
* [mapDataToCart()](#mapdatatocart)
* [mapDataToAccount()](#mapdatatoaccount)

### __construct()

```php
public function __construct(
    SapClient $client
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SapClient`](SapClient.md)||

Return Value: `mixed`

### mapDataToProduct()

```php
public function mapDataToProduct(
    array $data
): Product
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$data`|`array`||

Return Value: [`Product`](../../ProductApiBundle/Domain/Product.md)

### mapDataToCategories()

```php
public function mapDataToCategories(
    array $data,
    string $parentPath = '',
    int $depth
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$data`|`array`||
`$parentPath`|`string`|`''`|
`$depth`|`int`||

Return Value: `array`

### mapDataToCart()

```php
public function mapDataToCart(
    array $data,
    string $userId
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$data`|`array`||
`$userId`|`string`||

Return Value: [`Cart`](../../CartApiBundle/Domain/Cart.md)

### mapDataToAccount()

```php
public function mapDataToAccount(
    array $data
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$data`|`array`||

Return Value: [`Account`](../../AccountApiBundle/Domain/Account.md)

