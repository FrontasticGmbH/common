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
    \Symfony\Component\Templating\EngineInterface $twig,
    string $sender = 'support@frontastic.io'
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$mailer`|`\Swift_Mailer`||
`$twig`|`\Symfony\Component\Templating\EngineInterface`||
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

