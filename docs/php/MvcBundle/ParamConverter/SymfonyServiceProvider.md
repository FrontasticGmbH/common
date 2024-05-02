#  SymfonyServiceProvider

**Fully Qualified**: [`\Frontastic\Common\MvcBundle\ParamConverter\SymfonyServiceProvider`](../../../../src/php/MvcBundle/ParamConverter/SymfonyServiceProvider.php)

**Implements**: [`ServiceProvider`](ServiceProvider.md)

## Methods

* [__construct()](#__construct)
* [getFormFactory()](#getformfactory)
* [getTokenStorage()](#gettokenstorage)
* [getAuthorizationChecker()](#getauthorizationchecker)

### __construct()

```php
public function __construct(
    ?\Symfony\Component\Form\FormFactoryInterface $formFactory,
    ?\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage,
    ?\Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authorizationChecker
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$formFactory`|`?\Symfony\Component\Form\FormFactoryInterface`||
`$tokenStorage`|`?\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface`||
`$authorizationChecker`|`?\Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface`||

Return Value: `mixed`

### getFormFactory()

```php
public function getFormFactory(): \Symfony\Component\Form\FormFactoryInterface
```

Return Value: `\Symfony\Component\Form\FormFactoryInterface`

### getTokenStorage()

```php
public function getTokenStorage(): \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
```

Return Value: `\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface`

### getAuthorizationChecker()

```php
public function getAuthorizationChecker(): \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
```

Return Value: `\Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
