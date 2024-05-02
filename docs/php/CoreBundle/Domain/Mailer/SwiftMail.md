#  SwiftMail

**Fully Qualified**: [`\Frontastic\Common\CoreBundle\Domain\Mailer\SwiftMail`](../../../../../src/php/CoreBundle/Domain/Mailer/SwiftMail.php)

**Extends**: [`Mailer`](../Mailer.md)

## Methods

* [__construct()](#__construct)
* [sendToUser()](#sendtouser)

### __construct()

```php
public function __construct(
    \Swift_Mailer $mailer,
    \Twig\Environment $twig,
    string $sender = 'support@frontastic.io'
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$mailer`|`\Swift_Mailer`||
`$twig`|`\Twig\Environment`||
`$sender`|`string`|`'support@frontastic.io'`|

Return Value: `mixed`

### sendToUser()

```php
public function sendToUser(
    mixed $user,
    string $type,
    string $subject,
    array $parameters = array()
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$user`|`mixed`||
`$type`|`string`||
`$subject`|`string`||
`$parameters`|`array`|`array()`|

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
