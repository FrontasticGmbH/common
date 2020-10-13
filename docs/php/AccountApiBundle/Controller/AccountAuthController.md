#  AccountAuthController

**Fully Qualified**: [`\Frontastic\Common\AccountApiBundle\Controller\AccountAuthController`](../../../../src/php/AccountApiBundle/Controller/AccountAuthController.php)

**Extends**: `\Symfony\Bundle\FrameworkBundle\Controller\Controller`

## Methods

* [indexAction()](#indexaction)
* [registerAction()](#registeraction)
* [confirmAction()](#confirmaction)
* [requestResetAction()](#requestresetaction)
* [resetAction()](#resetaction)
* [changePasswordAction()](#changepasswordaction)
* [updateAction()](#updateaction)

### indexAction()

```php
public function indexAction(
    \Symfony\Component\HttpFoundation\Request $request,
    \Symfony\Component\Security\Core\User\UserInterface $account = null
): \Symfony\Component\HttpFoundation\JsonResponse
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|`\Symfony\Component\HttpFoundation\Request`||
`$account`|`\Symfony\Component\Security\Core\User\UserInterface`|`null`|

Return Value: `\Symfony\Component\HttpFoundation\JsonResponse`

### registerAction()

```php
public function registerAction(
    \Symfony\Component\HttpFoundation\Request $request,
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context
): \Symfony\Component\HttpFoundation\Response
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|`\Symfony\Component\HttpFoundation\Request`||
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||

Return Value: `\Symfony\Component\HttpFoundation\Response`

### confirmAction()

```php
public function confirmAction(
    \Symfony\Component\HttpFoundation\Request $request,
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    string $confirmationToken
): \Symfony\Component\HttpFoundation\Response
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|`\Symfony\Component\HttpFoundation\Request`||
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$confirmationToken`|`string`||

Return Value: `\Symfony\Component\HttpFoundation\Response`

### requestResetAction()

```php
public function requestResetAction(
    \Symfony\Component\HttpFoundation\Request $request
): \QafooLabs\MVC\RedirectRoute
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|`\Symfony\Component\HttpFoundation\Request`||

Return Value: `\QafooLabs\MVC\RedirectRoute`

### resetAction()

```php
public function resetAction(
    \Symfony\Component\HttpFoundation\Request $request,
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context,
    string $token
): \Symfony\Component\HttpFoundation\Response
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|`\Symfony\Component\HttpFoundation\Request`||
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||
`$token`|`string`||

Return Value: `\Symfony\Component\HttpFoundation\Response`

### changePasswordAction()

```php
public function changePasswordAction(
    \Symfony\Component\HttpFoundation\Request $request,
    \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context
): \Symfony\Component\HttpFoundation\JsonResponse
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|`\Symfony\Component\HttpFoundation\Request`||
`$context`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\Context`||

Return Value: `\Symfony\Component\HttpFoundation\JsonResponse`

### updateAction()

```php
public function updateAction(
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
