#  ClientFactory

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\ClientFactory`](../../../../src/php/ShopwareBundle/Domain/ClientFactory.php)

## Methods

* [__construct()](#__construct)
* [factorForProjectAndType()](#factorforprojectandtype)

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

### factorForProjectAndType()

```php
public function factorForProjectAndType(
    Project $project,
    string $typeName
): Client
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|[`Project`](../../ReplicatorBundle/Domain/Project.md)||
`$typeName`|`string`||

Return Value: [`Client`](Client.md)

