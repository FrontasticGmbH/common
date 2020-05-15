#  ContentQueryFactory

**Fully Qualified**: [`\Frontastic\Common\ContentApiBundle\Domain\ContentQueryFactory`](../../../../src/php/ContentApiBundle/Domain/ContentQueryFactory.php)

## Methods

* [queryFromParameters()](#queryfromparameters)
* [queryFromRequest()](#queryfromrequest)

### queryFromParameters()

```php
static public function queryFromParameters(
    array $parameters
): Query
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$parameters`|`array`||Query parameters (typically from HTTP request)

Return Value: [`Query`](Query.md)

### queryFromRequest()

```php
static public function queryFromRequest(
    \Symfony\Component\HttpFoundation\Request $request
): Query
```

*Creates a Query from a Request and will ignore additional parameters send to the request*

Argument|Type|Default|Description
--------|----|-------|-----------
`$request`|`\Symfony\Component\HttpFoundation\Request`||

Return Value: [`Query`](Query.md)

