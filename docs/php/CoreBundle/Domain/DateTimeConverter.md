#  DateTimeConverter

**Fully Qualified**: [`\Frontastic\Common\CoreBundle\Domain\DateTimeConverter`](../../../../src/php/CoreBundle/Domain/DateTimeConverter.php)

## Methods

* [dateTimeInterfaceToImmutable()](#datetimeinterfacetoimmutable)
* [dateTimeInterfaceToMutable()](#datetimeinterfacetomutable)

### dateTimeInterfaceToImmutable()

```php
static public function dateTimeInterfaceToImmutable(
    \DateTimeInterface $original
): \DateTimeImmutable
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$original`|`\DateTimeInterface`||

Return Value: `\DateTimeImmutable`

### dateTimeInterfaceToMutable()

```php
static public function dateTimeInterfaceToMutable(
    \DateTimeInterface $original
): \DateTime
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$original`|`\DateTimeInterface`||

Return Value: `\DateTime`

