#  OrderIdGeneratorV2Adapter

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\OrderIdGeneratorV2Adapter`](../../../../src/php/CartApiBundle/Domain/OrderIdGeneratorV2Adapter.php)

**Implements**: [`OrderIdGeneratorV2`](OrderIdGeneratorV2.md)

## Methods

* [__construct()](#__construct)
* [getOrderId()](#getorderid)

### __construct()

```php
public function __construct(
    OrderIdGenerator $legacyIdGenerator
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$legacyIdGenerator`|[`OrderIdGenerator`](OrderIdGenerator.md)||

Return Value: `mixed`

### getOrderId()

```php
public function getOrderId(
    CartApi $cartApi,
    Cart $cart
): string
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](CartApi.md)||
`$cart`|[`Cart`](Cart.md)||

Return Value: `string`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
