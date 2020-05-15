#  FactoryServiceLocator

**Fully Qualified**: [`\Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator`](../../../../../src/php/CoreBundle/Domain/Api/FactoryServiceLocator.php)

**Implements**: `\Psr\Container\ContainerInterface`, `\Symfony\Contracts\Service\ServiceSubscriberInterface`

## Methods

* [__construct()](#__construct)
* [has()](#has)
* [get()](#get)
* [hasSubscribedService()](#hassubscribedservice)
* [addSubscribedService()](#addsubscribedservice)
* [getSubscribedServices()](#getsubscribedservices)

### __construct()

```php
public function __construct(
    \Psr\Container\ContainerInterface $container,
    \Symfony\Component\DependencyInjection\ServiceLocator $taggedServiceContainer
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$container`|`\Psr\Container\ContainerInterface`||
`$taggedServiceContainer`|`\Symfony\Component\DependencyInjection\ServiceLocator`||

Return Value: `mixed`

### has()

```php
public function has(
    mixed $id
): bool
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$id`|`mixed`||

Return Value: `bool`

### get()

```php
public function get(
    mixed $id
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$id`|`mixed`||

Return Value: `mixed`

### hasSubscribedService()

```php
static public function hasSubscribedService(
    string $serviceId
): bool
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$serviceId`|`string`||

Return Value: `bool`

### addSubscribedService()

```php
static public function addSubscribedService(
    string $serviceId
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$serviceId`|`string`||

Return Value: `void`

### getSubscribedServices()

```php
static public function getSubscribedServices(): array
```

Return Value: `array`

