# `interface`  ControllerResultConverter

**Fully Qualified**: [`\Frontastic\Common\MvcBundle\Controller\ResultConverter\ControllerResultConverter`](../../../../../src/php/MvcBundle/Controller/ResultConverter/ControllerResultConverter.php)

## Methods

* [supports()](#supports)
* [convert()](#convert)

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
