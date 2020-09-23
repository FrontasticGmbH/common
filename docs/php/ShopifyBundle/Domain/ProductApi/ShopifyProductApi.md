#  ShopifyProductApi

**Fully Qualified**: [`\Frontastic\Common\ShopifyBundle\Domain\ProductApi\ShopifyProductApi`](../../../../../src/php/ShopifyBundle/Domain/ProductApi/ShopifyProductApi.php)

**Extends**: [`ProductApiBase`](../../../ProductApiBundle/Domain/ProductApiBase.md)

## Methods

* [__construct()](#__construct)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    ShopifyClient $client,
    ProductSearchApi $productSearchApi
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`ShopifyClient`](../ShopifyClient.md)||
`$productSearchApi`|[`ProductSearchApi`](../../../ProductSearchApiBundle/Domain/ProductSearchApi.md)||

Return Value: `mixed`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): ShopifyClient
```

Return Value: [`ShopifyClient`](../ShopifyClient.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
