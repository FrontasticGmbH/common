#  RedirectConverter

**Fully Qualified**: [`\Frontastic\Common\MvcBundle\Controller\ResultConverter\RedirectConverter`](../../../../../src/php/MvcBundle/Controller/ResultConverter/RedirectConverter.php)

**Implements**: [`ControllerResultConverter`](ControllerResultConverter.md)

## Methods

* [__construct()](#__construct)
* [supports()](#supports)
* [convert()](#convert)

### __construct()

```php
public function __construct(
    \Symfony\Component\Routing\Generator\UrlGeneratorInterface $router
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$router`|`\Symfony\Component\Routing\Generator\UrlGeneratorInterface`||

Return Value: `mixed`

### supports()

```php
public function supports(
    mixed $result
): bool
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$result`|`mixed`||

Return Value: `bool`

### convert()

```php
public function convert(
    mixed $result,
    \Symfony\Component\HttpFoundation\Request $request
): \Symfony\Component\HttpFoundation\Response
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$result`|`mixed`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||

Return Value: `\Symfony\Component\HttpFoundation\Response`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
