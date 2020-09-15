#  FindologicClientFactory

**Fully Qualified**: [`\Frontastic\Common\FindologicBundle\Domain\FindologicClientFactory`](../../../../src/php/FindologicBundle/Domain/FindologicClientFactory.php)

## Methods

* [__construct()](#__construct)
* [factorForConfigs()](#factorforconfigs)

### __construct()

```php
public function __construct(
    HttpClient $httpClient
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$httpClient`|[`HttpClient`](../../HttpClient.md)||

Return Value: `mixed`

### factorForConfigs()

```php
public function factorForConfigs(
    array $languages,
    object $typeSpecificConfig,
    ?object $findologicConfig = null
): FindologicClient
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$languages`|`array`||
`$typeSpecificConfig`|`object`||
`$findologicConfig`|`?object`|`null`|

Return Value: [`FindologicClient`](FindologicClient.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
