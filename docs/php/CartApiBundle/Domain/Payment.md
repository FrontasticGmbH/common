#  Payment

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\Payment`](../../../../src/php/CartApiBundle/Domain/Payment.php)

**Extends**: [`ApiDataObject`](../../CoreBundle/Domain/ApiDataObject.md)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`id` | `string` |  | - | An internal ID to identify this payment
`paymentProvider` | `string` |  | - | The name of the payment service provider
`paymentId` | `string` |  | - | The ID used by the payment service provider for this payment
`amount` | `int` |  | - | In cent
`currency` | `string` |  | - | 
`debug` | `string` |  | - | A text describing the current status of the payment
`paymentStatus` | `string` |  | - | One of the `PAYMENT_STATUS_*` constants
`version` | `int` |  | - | 
`paymentMethod` | `string` |  | - | The descriptor of the payment method used for this payment
`paymentDetails` | `array|null` |  | - | This data is stored as is by the `CartApi`. The payment integration can use this to store additional data which might be needed later in the payment process.

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
