#  LegacyAccountApiController

**Fully Qualified**: [`\Frontastic\Common\AccountApiBundle\Controller\LegacyAccountApiController`](../../../../src/php/AccountApiBundle/Controller/LegacyAccountApiController.php)

## Methods

* [__construct()](#__construct)
* [addAddressAction()](#addaddressaction)
* [updateAddressAction()](#updateaddressaction)
* [removeAddressAction()](#removeaddressaction)
* [setDefaultBillingAddressAction()](#setdefaultbillingaddressaction)
* [setDefaultShippingAddressAction()](#setdefaultshippingaddressaction)

### __construct()

```php
public function __construct(
    AccountApi $accountApi
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../Domain/AccountApi.md)||

Return Value: `mixed`

### addAddressAction()

```php
public function addAddressAction(
    \Symfony\Component\HttpFoundation\Request $request,
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context
): \Symfony\Component\HttpFoundation\JsonResponse
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|`\Symfony\Component\HttpFoundation\Request`||
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||

Return Value: `\Symfony\Component\HttpFoundation\JsonResponse`

### updateAddressAction()

```php
public function updateAddressAction(
    \Symfony\Component\HttpFoundation\Request $request,
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context
): \Symfony\Component\HttpFoundation\JsonResponse
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|`\Symfony\Component\HttpFoundation\Request`||
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||

Return Value: `\Symfony\Component\HttpFoundation\JsonResponse`

### removeAddressAction()

```php
public function removeAddressAction(
    \Symfony\Component\HttpFoundation\Request $request,
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context
): \Symfony\Component\HttpFoundation\JsonResponse
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|`\Symfony\Component\HttpFoundation\Request`||
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||

Return Value: `\Symfony\Component\HttpFoundation\JsonResponse`

### setDefaultBillingAddressAction()

```php
public function setDefaultBillingAddressAction(
    \Symfony\Component\HttpFoundation\Request $request,
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context
): \Symfony\Component\HttpFoundation\JsonResponse
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|`\Symfony\Component\HttpFoundation\Request`||
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||

Return Value: `\Symfony\Component\HttpFoundation\JsonResponse`

### setDefaultShippingAddressAction()

```php
public function setDefaultShippingAddressAction(
    \Symfony\Component\HttpFoundation\Request $request,
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context
): \Symfony\Component\HttpFoundation\JsonResponse
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|`\Symfony\Component\HttpFoundation\Request`||
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||

Return Value: `\Symfony\Component\HttpFoundation\JsonResponse`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
