#  FlashYieldApplier

**Fully Qualified**: [`\Frontastic\Common\MvcBundle\Controller\ResultConverter\FlashYieldApplier`](../../../../../src/php/MvcBundle/Controller/ResultConverter/FlashYieldApplier.php)

**Implements**: [`ControllerYieldApplier`](ControllerYieldApplier.md)

## Methods

* [supports()](#supports)
* [apply()](#apply)

### supports()

```php
public function supports(
    mixed $yield
): bool
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$yield`|`mixed`||

Return Value: `bool`

### apply()

```php
public function apply(
    mixed $yield,
    \Symfony\Component\HttpFoundation\Request $request,
    \Symfony\Component\HttpFoundation\Response $response
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$yield`|`mixed`||
`$request`|`\Symfony\Component\HttpFoundation\Request`||
`$response`|`\Symfony\Component\HttpFoundation\Response`||

Return Value: `void`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
