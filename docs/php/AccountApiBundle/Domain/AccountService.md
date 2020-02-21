#  AccountService

Fully Qualified: [`\Frontastic\Common\AccountApiBundle\Domain\AccountService`](../../../../src/php/AccountApiBundle/Domain/AccountService.php)




## Methods

### __construct

`function __construct(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\CoreBundle\Domain\Mailer mailer): mixed`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$mailer`|`\Frontastic\Common\CoreBundle\Domain\Mailer`|``|

### getSessionFor

`function getSessionFor(\Frontastic\Common\AccountApiBundle\Domain\Account account = null): mixed`






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|`null`|

### sendConfirmationMail

`function sendConfirmationMail(\Frontastic\Common\AccountApiBundle\Domain\Account account): mixed`






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### sendPasswordResetMail

`function sendPasswordResetMail(\Frontastic\Common\AccountApiBundle\Domain\Account account): mixed`






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### get

`function get(string email): \Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`|``|

### exists

`function exists(string email): bool`






Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`|``|

### confirmEmail

`function confirmEmail(string confirmationToken): \Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$confirmationToken`|`string`|``|

### login

`function login(\Frontastic\Common\AccountApiBundle\Domain\Account account, ?\Frontastic\Common\CartApiBundle\Domain\Cart cart = null): bool`






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|
`$cart`|`?\Frontastic\Common\CartApiBundle\Domain\Cart`|`null`|

### create

`function create(\Frontastic\Common\AccountApiBundle\Domain\Account account, ?\Frontastic\Common\CartApiBundle\Domain\Cart cart = null): \Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|
`$cart`|`?\Frontastic\Common\CartApiBundle\Domain\Cart`|`null`|

### update

`function update(\Frontastic\Common\AccountApiBundle\Domain\Account account): \Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### updatePassword

`function updatePassword(\Frontastic\Common\AccountApiBundle\Domain\Account account, string oldPassword, string newPassword): \Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|
`$oldPassword`|`string`|``|
`$newPassword`|`string`|``|

### resetPassword

`function resetPassword(string token, string newPassword): \Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`|``|
`$newPassword`|`string`|``|

### remove

`function remove(\Frontastic\Common\AccountApiBundle\Domain\Account account): mixed`






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

