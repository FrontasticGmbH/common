#  DefaultSapLocaleCreatorFactory

**Fully Qualified**: [`\Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\DefaultSapLocaleCreatorFactory`](../../../../../src/php/SapCommerceCloudBundle/Domain/Locale/DefaultSapLocaleCreatorFactory.php)

**Extends**: [`SapLocaleCreatorFactory`](SapLocaleCreatorFactory.md)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    \Psr\SimpleCache\CacheInterface $cache
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cache`|`\Psr\SimpleCache\CacheInterface`||

Return Value: `mixed`

### factor()

```php
public function factor(
    Project $project,
    SapClient $client
): SapLocaleCreator
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../../ReplicatorBundle/Domain/Project.md)||
`$client`|[`SapClient`](../SapClient.md)||

Return Value: [`SapLocaleCreator`](SapLocaleCreator.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
