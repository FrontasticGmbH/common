#  DefaultLocaleCreatorFactory

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\Locale\DefaultLocaleCreatorFactory`](../../../../../src/php/SprykerBundle/Domain/Locale/DefaultLocaleCreatorFactory.php)

**Extends**: [`LocaleCreatorFactory`](LocaleCreatorFactory.md)

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
    SprykerClientInterface $client
): LocaleCreator
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../../ReplicatorBundle/Domain/Project.md)||
`$client`|[`SprykerClientInterface`](../SprykerClientInterface.md)||

Return Value: [`LocaleCreator`](LocaleCreator.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
