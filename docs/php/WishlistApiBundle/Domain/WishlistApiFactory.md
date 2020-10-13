#  WishlistApiFactory

**Fully Qualified**: [`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApiFactory`](../../../../src/php/WishlistApiBundle/Domain/WishlistApiFactory.php)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    \Psr\Container\ContainerInterface $container,
    ProductApiFactory $productApiFactory,
    Commercetools\ClientFactory $commercetoolsClientFactory,
    iterable $decorators
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$container`|`\Psr\Container\ContainerInterface`||
`$productApiFactory`|[`ProductApiFactory`](../../ProductApiBundle/Domain/ProductApiFactory.md)||
`$commercetoolsClientFactory`|[`Commercetools`](../../ProductApiBundle/Domain/ProductApi/Commercetools.md)\ClientFactory||
`$decorators`|`iterable`||

Return Value: `mixed`

### factor()

```php
public function factor(
    Project $project
): WishlistApi
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../ReplicatorBundle/Domain/Project.md)||

Return Value: [`WishlistApi`](WishlistApi.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
