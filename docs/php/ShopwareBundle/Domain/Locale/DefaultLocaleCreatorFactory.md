#  DefaultLocaleCreatorFactory

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\Locale\DefaultLocaleCreatorFactory`](../../../../../src/php/ShopwareBundle/Domain/Locale/DefaultLocaleCreatorFactory.php)

**Extends**: [`LocaleCreatorFactory`](LocaleCreatorFactory.md)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    ShopwareProjectConfigApiFactory $projectConfigApiFactory
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$projectConfigApiFactory`|[`ShopwareProjectConfigApiFactory`](../ProjectConfigApi/ShopwareProjectConfigApiFactory.md)||

Return Value: `mixed`

### factor()

```php
public function factor(
    Project $project,
    ClientInterface $client
): LocaleCreator
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../../ReplicatorBundle/Domain/Project.md)||
`$client`|[`ClientInterface`](../ClientInterface.md)||

Return Value: [`LocaleCreator`](LocaleCreator.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
