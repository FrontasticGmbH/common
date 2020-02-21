#  Account

Fully Qualified: [`\Frontastic\Common\AccountApiBundle\Domain\Account`](../../../../src/php/AccountApiBundle/Domain/Account.php)



Property|Type|Default|Description
--------|----|-------|-----------
`accountId`|`string`|``|
`email`|`string`|``|
`salutation`|`string`|``|
`firstName`|`string`|``|
`lastName`|`string`|``|
`birthday`|`\DateTime`|``|
`data`|`array`|`[]`|
`groups`|`\Frontastic\Common\AccountApiBundle\Domain\Group[]`|`[]`|
`confirmationToken`|`string`|``|
`confirmed`|`string`|`false`|
`tokenValidUntil`|`\Frontastic\Common\AccountApiBundle\Domain\DateTime`|``|
`addresses`|`\Frontastic\Common\AccountApiBundle\Domain\Address[]`|`[]`|
`dangerousInnerAccount`|`mixed`|``|Access original object from backend

## Methods

### setPassword

`function setPassword(string password): mixed`






Argument|Type|Default|Description
--------|----|-------|-----------
`$password`|`string`|``|

### isValidPassword

`function isValidPassword(string password): bool`






Argument|Type|Default|Description
--------|----|-------|-----------
`$password`|`string`|``|

### getUsername

`function getUsername(): mixed`







### getRoles

`function getRoles(): mixed`







### getPassword

`function getPassword(): mixed`







### getSalt

`function getSalt(): mixed`







### eraseCredentials

`function eraseCredentials(): mixed`







### assertPermission

`function assertPermission(string required): mixed`






Argument|Type|Default|Description
--------|----|-------|-----------
`$required`|`string`|``|

### cleanForSession

`function cleanForSession(): \Frontastic\Common\AccountApiBundle\Domain\Account`




**


### serialize

`function serialize(): mixed`







### unserialize

`function unserialize(mixed serialized): mixed`






Argument|Type|Default|Description
--------|----|-------|-----------
`$serialized`|`mixed`|``|

