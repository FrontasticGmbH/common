#  DefaultContentApiFactory

**Fully Qualified**: [`\Frontastic\Common\ContentApiBundle\Domain\DefaultContentApiFactory`](../../../../src/php/ContentApiBundle/Domain/DefaultContentApiFactory.php)

**Implements**: [`ContentApiFactory`](ContentApiFactory.md)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    \Psr\Container\ContainerInterface $container,
    \Doctrine\Common\Cache\Cache $cache,
    \Psr\SimpleCache\CacheInterface $psrCache,
    \Contentful\RichText\Renderer $richtextRenderer,
    bool $debug,
    iterable $decorators
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$container`|`\Psr\Container\ContainerInterface`||
`$cache`|`\Doctrine\Common\Cache\Cache`||
`$psrCache`|`\Psr\SimpleCache\CacheInterface`||
`$richtextRenderer`|`\Contentful\RichText\Renderer`||
`$debug`|`bool`||
`$decorators`|`iterable`||

Return Value: `mixed`

### factor()

```php
public function factor(
    Project $project
): ContentApi
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../ReplicatorBundle/Domain/Project.md)||

Return Value: [`ContentApi`](ContentApi.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
