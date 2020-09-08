# `abstract`  AbstractSprykerBaseFactory

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\BaseApi\Factory\AbstractSprykerBaseFactory`](../../../../../src/php/SprykerBundle/BaseApi/Factory/AbstractSprykerBaseFactory.php)

## Methods

* [__construct()](#__construct)
* [factor()](#factor)

### __construct()

```php
public function __construct(
    \Symfony\Component\DependencyInjection\ContainerInterface $container
): mixed
```

*SprykerCatalogSearchSuggestionsApiFactory constructor.*

Argument|Type|Default|Description
--------|----|-------|-----------
`$container`|`\Symfony\Component\DependencyInjection\ContainerInterface`||

Return Value: `mixed`

### factor()

```php
abstract public function factor(
    Project $project
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../../ReplicatorBundle/Domain/Project.md)||

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
