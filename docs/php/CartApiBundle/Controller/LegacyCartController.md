#  LegacyCartController

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Controller\LegacyCartController`](../../../../src/php/CartApiBundle/Controller/LegacyCartController.php)

**Extends**: [`CrudController`](../../CoreBundle/Controller/CrudController.md)

## Methods

* [getAction()](#getaction)
* [getOrderAction()](#getorderaction)
* [addAction()](#addaction)
* [addMultipleAction()](#addmultipleaction)
* [updateLineItemAction()](#updatelineitemaction)
* [removeLineItemAction()](#removelineitemaction)
* [updateAction()](#updateaction)
* [checkoutAction()](#checkoutaction)
* [redeemDiscountAction()](#redeemdiscountaction)
* [removeDiscountAction()](#removediscountaction)
* [getShippingMethodsAction()](#getshippingmethodsaction)

### getAction()

```php
public function getAction(
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    \Symfony\Component\HttpFoundation\Request $request
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||

Return Value: `array`

### getOrderAction()

```php
public function getOrderAction(
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    \Symfony\Component\HttpFoundation\Request $request,
    string $order
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||
`$order`|`string`||

Return Value: `array`

### addAction()

```php
public function addAction(
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    \Symfony\Component\HttpFoundation\Request $request
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||

Return Value: `array`

### addMultipleAction()

```php
public function addMultipleAction(
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    \Symfony\Component\HttpFoundation\Request $request
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||

Return Value: `array`

### updateLineItemAction()

```php
public function updateLineItemAction(
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    \Symfony\Component\HttpFoundation\Request $request
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||

Return Value: `array`

### removeLineItemAction()

```php
public function removeLineItemAction(
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    \Symfony\Component\HttpFoundation\Request $request
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||

Return Value: `array`

### updateAction()

```php
public function updateAction(
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    \Symfony\Component\HttpFoundation\Request $request
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||

Return Value: `array`

### checkoutAction()

```php
public function checkoutAction(
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    \Symfony\Component\HttpFoundation\Request $request
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||

Return Value: `array`

### redeemDiscountAction()

```php
public function redeemDiscountAction(
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    \Symfony\Component\HttpFoundation\Request $request,
    string $code
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||
`$code`|`string`||

Return Value: `array`

### removeDiscountAction()

```php
public function removeDiscountAction(
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    \Symfony\Component\HttpFoundation\Request $request
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||

Return Value: `array`

### getShippingMethodsAction()

```php
public function getShippingMethodsAction(
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    \Symfony\Component\HttpFoundation\Request $request
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||

Return Value: `array`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
