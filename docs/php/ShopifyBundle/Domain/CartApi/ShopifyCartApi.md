#  ShopifyCartApi

**Fully Qualified**: [`\Frontastic\Common\ShopifyBundle\Domain\CartApi\ShopifyCartApi`](../../../../../src/php/ShopifyBundle/Domain/CartApi/ShopifyCartApi.php)

**Extends**: [`CartApiBase`](../../../CartApiBundle/Domain/CartApiBase.md)

## Methods

* [__construct()](#__construct)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    ShopifyClient $client,
    ShopifyProductMapper $productMapper,
    ShopifyAccountMapper $accountMapper
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`ShopifyClient`](../ShopifyClient.md)||
`$productMapper`|[`ShopifyProductMapper`](../Mapper/ShopifyProductMapper.md)||
`$accountMapper`|[`ShopifyAccountMapper`](../Mapper/ShopifyAccountMapper.md)||

Return Value: `mixed`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
