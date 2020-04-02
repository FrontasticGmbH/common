<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi\LifecycleEventDecorator;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;

/**
 * Base implementation of the AccountApi LifecycleDecorator, which should be used when writing own LifecycleDecorators
 * as base class for future type-safety and convenience reasons, as it will provide the needed function naming as well
 * as parameter type-hinting.
 *
 * The before* Methods will be obviously called *before* the original method is executed and will get all the parameters
 * handed over, which the original method will get called with. Overwriting this method can be useful if you want to
 * manipulate the handed over parameters by simply manipulating it.
 * These methods doesn't return anything.
 *
 * The after* Methods will be oviously called *after* the orignal method is executed and will get the unwrapped result
 * from the original method handed over. So if the original methods returns a Promise, the resolved value will be
 * handed over to this function here.
 * Overwriting this method could be useful if you want to manipulate the result.
 * These methods need to return null if nothing should be manipulating, thus will lead to the original result being
 * returned or they need to return the same data-type as the original method returns, otherwise you will get Type-Errors
 * at some point.
 *
 * In order to make this class available to the Lifecycle-Decorator, you will need to tag your service based on this
 * class with "accountApi.lifecycleEventListener": e.g. by adding the tag inside the `services.xml`
 * ```
 * <tag name="accountApi.lifecycleEventListener" />
 * ```
 */
abstract class BaseImplementation
{
    /*** get() ********************************************************************************************************/
    public function beforeGet(AccountApi $accountApi, string $email): void
    {
    }

    public function afterGet(AccountApi $accountApi, Account $account): ?Account
    {
        return null;
    }

    /*** confirmEmail() ***********************************************************************************************/
    public function beforeConfirmEmail(AccountApi $accountApi, string $token): void
    {
    }

    public function afterConfirmEmail(AccountApi $accountApi, Account $account): ?Account
    {
        return null;
    }

    /*** create() *****************************************************************************************************/
    public function beforeCreate(AccountApi $accountApi, Account $account, ?Cart $cart = null): void
    {
    }

    public function afterCreate(AccountApi $accountApi, Account $account): ?Account
    {
        return null;
    }

    /*** update() *****************************************************************************************************/
    public function beforeUpdate(AccountApi $accountApi, Account $account): void
    {
    }

    public function afterUpdate(AccountApi $accountApi, Account $account): ?Account
    {
        return null;
    }

    /*** updatePassword() *********************************************************************************************/
    public function beforeUpdatePassword(
        AccountApi $accountApi,
        string $accountId,
        string $oldPassword,
        string $newPassword
    ): void {
    }

    public function afterUpdatePassword(AccountApi $accountApi, Account $account): ?Account
    {
        return null;
    }

    /*** generatePasswordResetToken() *********************************************************************************/
    public function beforeGeneratePasswordResetToken(AccountApi $accountApi, Account $account): void
    {
    }

    public function afterGeneratePasswordResetToken(AccountApi $accountApi, Account $account): ?Account
    {
        return null;
    }

    /*** resetPassword() **********************************************************************************************/
    public function beforeResetPassword(AccountApi $accountApi, string $token, string $newPassword): void
    {
    }

    public function afterResetPassword(AccountApi $accountApi, Account $account): ?Account
    {
        return null;
    }

    /*** login() ******************************************************************************************************/
    public function beforeLogin(AccountApi $accountApi, Account $account, ?Cart $cart = null): void
    {
    }

    public function afterLogin(AccountApi $accountApi, bool $successful): ?bool
    {
        return null;
    }

    /*** getAddresses() ***********************************************************************************************/
    public function beforeGetAddresses(AccountApi $accountApi, string $accountId): void
    {
    }

    /**
     * @param AccountApi $accountApi
     * @param Address[] $addresses
     * @return Address[]|null
     */
    public function afterGetAddresses(AccountApi $accountApi, array $addresses): ?array
    {
        return null;
    }

    /*** addAddress() *************************************************************************************************/
    public function beforeAddAddress(AccountApi $accountApi, string $accountId, Address $address): void
    {
    }

    public function afterAddAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return null;
    }

    /*** updateAddress() **********************************************************************************************/
    public function beforeUpdateAddress(AccountApi $accountApi, string $accountId, Address $address): void
    {
    }

    public function afterUpdateAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return null;
    }

    /*** removeAddress() **********************************************************************************************/
    public function beforeRemoveAddress(AccountApi $accountApi, string $accountId, string $addressId): void
    {
    }

    public function afterRemoveAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return null;
    }

    /*** setDefaultBillingAddress() ***********************************************************************************/
    public function beforeSetDefaultBillingAddress(AccountApi $accountApi, string $accountId, string $addressId): void
    {
    }

    public function afterSetDefaultBillingAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return null;
    }

    /*** setDefaultShippingAddress() **********************************************************************************/
    public function beforeSetDefaultShippingAddress(AccountApi $accountApi, string $accountId, string $addressId): void
    {
    }

    public function afterSetDefaultShippingAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return null;
    }
}
