#  LegacyWishlistController

**Fully Qualified**: [`\Frontastic\Common\WishlistApiBundle\Controller\LegacyWishlistController`](../../../../src/php/WishlistApiBundle/Controller/LegacyWishlistController.php)

**Extends**: [`CrudController`](../../CoreBundle/Controller/CrudController.md)

## Methods

* [getAction()](#getaction)
* [addAction()](#addaction)
* [addMultipleAction()](#addmultipleaction)
* [createAction()](#createaction)
* [updateLineItemAction()](#updatelineitemaction)
* [removeLineItemAction()](#removelineitemaction)

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

### createAction()

```php
public function createAction(
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    \Symfony\Component\HttpFoundation\Request $request
): Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||

Return Value: [`Wishlist`](../Domain/Wishlist.md)

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

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
