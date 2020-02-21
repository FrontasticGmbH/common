# `interface`  ContentApi

Fully Qualified: [`\Frontastic\Common\ContentApiBundle\Domain\ContentApi`](../../../../src/php/ContentApiBundle/Domain/ContentApi.php)




## Methods

* [getContentTypes()](#getcontenttypes)
* [getContent()](#getcontent)
* [query()](#query)
* [getDangerousInnerClient()](#getdangerousinnerclient)


### getContentTypes()


```php
public function getContentTypes(
    
): array
```







Return Value: `array`

### getContent()


```php
public function getContent(
    string $contentId,
    string $locale = null,
    string $mode = self::QUERY_SYNC
): ?object
```


*Fetch content with $contentId in $locale. If $locale is null, project default locale is used.*



Argument|Type|Default|Description
--------|----|-------|-----------
`$contentId`|`string`||
`$locale`|`string`|`null`|
`$mode`|`string`|`self::QUERY_SYNC`|One of the QUERY_* connstants. Execute the query synchronously or asynchronously?

Return Value: `?object`

### query()


```php
public function query(
    Query $query,
    string $locale = null,
    string $mode = self::QUERY_SYNC
): ?object
```


*Fetch content with by a $query in $locale. Interpretation of the query
attributes depend on the content API implementation. If $locale is null,
project default locale is used.*



Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](Query.md)||
`$locale`|`string`|`null`|
`$mode`|`string`|`self::QUERY_SYNC`|One of the QUERY_* connstants. Execute the query synchronously or asynchronously?

Return Value: `?object`

### getDangerousInnerClient()


```php
public function getDangerousInnerClient(
    
): mixed
```


*Get *dangerous* inner client*

This method exists to enable you to use features which are not yet part
of the abstraction layer.

Be aware that any usage of this method might seriously hurt backwards
compatibility and the future abstractions might differ a lot from the
vendor provided abstraction.

Use this with care for features necessary in your customer and talk with
Frontastic about provising an abstraction.


Return Value: `mixed`

