#  ShopwareProjectConfigApiFactory

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory`](../../../../../src/php/ShopwareBundle/Domain/ProjectConfigApi/ShopwareProjectConfigApiFactory.php)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    \Psr\SimpleCache\CacheInterface $cache,
    DataMapperResolver $dataMapperResolver,
    bool $debug
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cache`|`\Psr\SimpleCache\CacheInterface`||
`$dataMapperResolver`|[`DataMapperResolver`](../DataMapper/DataMapperResolver.md)||
`$debug`|`bool`||

Return Value: `mixed`

### factor()

```php
public function factor(
    ClientInterface $client
): ShopwareProjectConfigApiInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`ClientInterface`](../ClientInterface.md)||

Return Value: [`ShopwareProjectConfigApiInterface`](ShopwareProjectConfigApiInterface.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
