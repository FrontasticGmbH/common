#  ArrayToTemplateResponseConverter

**Fully Qualified**: [`\Frontastic\Common\MvcBundle\Controller\ResultConverter\ArrayToTemplateResponseConverter`](../../../../../src/php/MvcBundle/Controller/ResultConverter/ArrayToTemplateResponseConverter.php)

**Implements**: [`ControllerResultConverter`](ControllerResultConverter.md)

Guess the template names with the same algorithm that @Template() in Sensio's
FrameworkExtraBundle uses.

## Methods

* [__construct()](#__construct)
* [supports()](#supports)
* [convert()](#convert)

### __construct()

```php
public function __construct(
    \Twig\Environment $twig,
    TemplateGuesser $guesser,
    string $engine
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$twig`|`\Twig\Environment`||
`$guesser`|[`TemplateGuesser`](../../View/TemplateGuesser.md)||
`$engine`|`string`||

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
