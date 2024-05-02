#  SymfonyConventionsTemplateGuesser

**Fully Qualified**: [`\Frontastic\Common\MvcBundle\View\SymfonyConventionsTemplateGuesser`](../../../../src/php/MvcBundle/View/SymfonyConventionsTemplateGuesser.php)

**Implements**: [`TemplateGuesser`](TemplateGuesser.md)

## Methods

* [__construct()](#__construct)
* [guessControllerTemplateName()](#guesscontrollertemplatename)

### __construct()

```php
public function __construct(
    BundleLocation $bundleLocation,
    GyroControllerNameParser $parser
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$bundleLocation`|[`BundleLocation`](BundleLocation.md)||
`$parser`|[`GyroControllerNameParser`](../Controller/GyroControllerNameParser.md)||

Return Value: `mixed`

### guessControllerTemplateName()

```php
public function guessControllerTemplateName(
    string $controller,
    ?string $actionName,
    string $format,
    string $engine
): string
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$controller`|`string`||
`$actionName`|`?string`||
`$format`|`string`||
`$engine`|`string`||

Return Value: `string`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
