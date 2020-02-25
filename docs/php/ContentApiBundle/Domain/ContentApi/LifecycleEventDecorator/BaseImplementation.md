# `abstract`  BaseImplementation

**Fully Qualified**: [`\Frontastic\Common\ContentApiBundle\Domain\ContentApi\LifecycleEventDecorator\BaseImplementation`](../../../../../../src/php/ContentApiBundle/Domain/ContentApi/LifecycleEventDecorator/BaseImplementation.php)

The before* Methods will be obviously called *before* the original method is
executed and will get all the parameters handed over, which the original
method will get called with. Overwriting this method can be useful if you want
to manipulate the handed over parameters by simply manipulating it. These
methods doesn't return anything.

The after* Methods will be oviously called *after* the orignal method is
executed and will get the unwrapped result from the original method handed
over. So if the original methods returns a Promise, the resolved value will be
handed over to this function here. Overwriting this method could be useful if
you want to manipulate the result. These methods need to return null if
nothing should be manipulating, thus will lead to the original result being
returned or they need to return the same data-type as the original method
returns, otherwise you will get Type-Errors at some point.

In order to make this class available to the Lifecycle-Decorator, you will
need to tag your service based on this class with
"contentApi.lifecycleEventListener": e.g. by adding the tag inside the
`services.xml` ``` <tag name="contentApi.lifecycleEventListener" /> ```

## Methods

* [beforeGetContentTypes()](#beforegetcontenttypes)
* [afterGetContentTypes()](#aftergetcontenttypes)
* [beforeGetContent()](#beforegetcontent)
* [afterGetContent()](#aftergetcontent)
* [beforeQuery()](#beforequery)
* [afterQuery()](#afterquery)

### beforeGetContentTypes()

```php
public function beforeGetContentTypes(
    ContentApi $contentApi
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$contentApi`|[`ContentApi`](../../ContentApi.md)||

Return Value: `void`

### afterGetContentTypes()

```php
public function afterGetContentTypes(
    ContentApi $contentApi,
    array $contentTypes
): ?array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$contentApi`|[`ContentApi`](../../ContentApi.md)||
`$contentTypes`|`array`||

Return Value: `?array`

### beforeGetContent()

```php
public function beforeGetContent(
    ContentApi $contentApi,
    string $contentId,
    string $locale = null,
    string $mode = ContentApi::QUERY_SYNC
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$contentApi`|[`ContentApi`](../../ContentApi.md)||
`$contentId`|`string`||
`$locale`|`string`|`null`|
`$mode`|`string`|`ContentApi::QUERY_SYNC`|

Return Value: `void`

### afterGetContent()

```php
public function afterGetContent(
    ContentApi $contentApi,
    ?Content $content
): ?Content
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$contentApi`|[`ContentApi`](../../ContentApi.md)||
`$content`|?[`Content`](../Content.md)||

Return Value: ?[`Content`](../Content.md)

### beforeQuery()

```php
public function beforeQuery(
    ContentApi $contentApi,
    Query $query,
    string $locale = null,
    string $mode = ContentApi::QUERY_SYNC
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$contentApi`|[`ContentApi`](../../ContentApi.md)||
`$query`|[`Query`](../../Query.md)||
`$locale`|`string`|`null`|
`$mode`|`string`|`ContentApi::QUERY_SYNC`|

Return Value: `void`

### afterQuery()

```php
public function afterQuery(
    ContentApi $contentApi,
    ?Result $result
): ?Result
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$contentApi`|[`ContentApi`](../../ContentApi.md)||
`$result`|?[`Result`](../../Result.md)||

Return Value: ?[`Result`](../../Result.md)

