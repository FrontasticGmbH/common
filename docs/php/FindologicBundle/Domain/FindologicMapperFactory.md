#  FindologicMapperFactory

**Fully Qualified**: [`\Frontastic\Common\FindologicBundle\Domain\FindologicMapperFactory`](../../../../src/php/FindologicBundle/Domain/FindologicMapperFactory.php)

## Methods

* [__construct()](#__construct)
* [factorForConfigs()](#factorforconfigs)

### __construct()

```php
public function __construct(
    \Symfony\Component\Routing\Router $router
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$router`|`\Symfony\Component\Routing\Router`||

Return Value: `mixed`

### factorForConfigs()

```php
public function factorForConfigs(
    object $typeSpecificConfig,
    ?object $findologicConfig = null
): Mapper
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$typeSpecificConfig`|`object`||
`$findologicConfig`|`?object`|`null`|

Return Value: [`Mapper`](ProductSearchApi/Mapper.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
