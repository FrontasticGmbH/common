#  CartFetcher

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Controller\CartFetcher`](../../../../src/php/CartApiBundle/Controller/CartFetcher.php)

## Methods

* [__construct()](#__construct)
* [fetchCart()](#fetchcart)

### __construct()

```php
public function __construct(
    CartApi $cartApi,
    \Psr\Log\LoggerInterface $logger
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../Domain/CartApi.md)||
`$logger`|`\Psr\Log\LoggerInterface`||

Return Value: `mixed`

### fetchCart()

```php
public function fetchCart(
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    \Symfony\Component\HttpFoundation\Request $request
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||

Return Value: [`Cart`](../Domain/Cart.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
