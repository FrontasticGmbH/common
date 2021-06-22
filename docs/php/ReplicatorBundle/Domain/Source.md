# `interface`  Source

**Fully Qualified**: [`\Frontastic\Common\ReplicatorBundle\Domain\Source`](../../../../src/php/ReplicatorBundle/Domain/Source.php)

## Methods

* [updates()](#updates)

### updates()

```php
public function updates(
    string $since,
    int $count
): array
```

*Return a sequence of max $count updates since the last revision $since.*

Argument|Type|Default|Description
--------|----|-------|-----------
`$since`|`string`||
`$count`|`int`||

Return Value: `array`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
