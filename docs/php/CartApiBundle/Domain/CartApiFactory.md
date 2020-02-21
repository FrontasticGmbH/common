#  CartApiFactory

Fully Qualified: [`\Frontastic\Common\CartApiBundle\Domain\CartApiFactory`](../../../../src/php/CartApiBundle/Domain/CartApiFactory.php)




## Methods

### __construct

`function __construct(\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory commercetoolsClientFactory, \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory localeCreatorFactory, \Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator orderIdGenerator, iterable decorators): mixed`






Argument|Type|Default|Description
--------|----|-------|-----------
`$commercetoolsClientFactory`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory`|``|
`$localeCreatorFactory`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory`|``|
`$orderIdGenerator`|`\Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator`|``|
`$decorators`|`iterable`|``|

### factor

`function factor(\Frontastic\Common\ReplicatorBundle\Domain\Project project): \Frontastic\Common\CartApiBundle\Domain\CartApi`






Argument|Type|Default|Description
--------|----|-------|-----------
`$project`|`\Frontastic\Common\ReplicatorBundle\Domain\Project`|``|

