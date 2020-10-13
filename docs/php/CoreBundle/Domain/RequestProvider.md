#  RequestProvider

**Fully Qualified**: [`\Frontastic\Common\CoreBundle\Domain\RequestProvider`](../../../../src/php/CoreBundle/Domain/RequestProvider.php)

## Methods

* [__construct()](#__construct)
* [getCurrentRequest()](#getcurrentrequest)

### __construct()

```php
public function __construct(
    \Symfony\Component\HttpFoundation\RequestStack $requestStack
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$requestStack`|`\Symfony\Component\HttpFoundation\RequestStack`||

Return Value: `mixed`

### getCurrentRequest()

```php
public function getCurrentRequest(): ?\Symfony\Component\HttpFoundation\Request
```

Return Value: `?\Symfony\Component\HttpFoundation\Request`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
