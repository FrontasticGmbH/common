<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi\LifecycleEventDecorator;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\AccountApiBundle\Domain\PasswordResetToken;
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
    /*** getSalutations() *********************************************************************************************/
    public function beforeGetSalutations(AccountApi $accountApi, string $locale): void
    {
    }

    public function afterGetSalutations(AccountApi $accountApi, ?array $salutations): ?array
    {
        return null;
    }

    /*** confirmEmail() ***********************************************************************************************/
    public function beforeConfirmEmail(AccountApi $accountApi, string $token, string $locale = null): void
    {
    }

    public function afterConfirmEmail(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->mapReturnedAccount($account);
    }

    /*** create() *****************************************************************************************************/
    public function beforeCreate(
        AccountApi $accountApi,
        Account $account,
        ?Cart $cart = null,
        string $locale = null
    ): void {
    }

    public function afterCreate(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->mapReturnedAccount($account);
    }

    /*** update() *****************************************************************************************************/
    public function beforeUpdate(AccountApi $accountApi, Account $account, string $locale = null): void
    {
    }

    public function afterUpdate(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->mapReturnedAccount($account);
    }

    /*** updatePassword() *********************************************************************************************/
    public function beforeUpdatePassword(
        AccountApi $accountApi,
        Account $account,
        string $oldPassword,
        string $newPassword,
        string $locale = null
    ): void {
    }

    public function afterUpdatePassword(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->mapReturnedAccount($account);
    }

    /*** generatePasswordResetToken() *********************************************************************************/
    public function beforeGeneratePasswordResetToken(AccountApi $accountApi, string $email): void
    {
    }

    public function afterGeneratePasswordResetToken(
        AccountApi $accountApi,
        PasswordResetToken $token
    ): ?PasswordResetToken {
        return null;
    }

    /*** resetPassword() **********************************************************************************************/
    public function beforeResetPassword(
        AccountApi $accountApi,
        string $token,
        string $newPassword,
        string $locale = null
    ): void {
    }

    public function afterResetPassword(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->mapReturnedAccount($account);
    }

    /*** login() ******************************************************************************************************/
    public function beforeLogin(
        AccountApi $accountApi,
        Account $account,
        ?Cart $cart = null,
        string $locale = null
    ): void {
    }

    public function afterLogin(AccountApi $accountApi, ?Account $account = null): ?Account
    {
        if (!isset($account)) {
            return null;
        }
        return $this->mapReturnedAccount($account);
    }

    /*** refreshAccount() *********************************************************************************************/
    public function beforeRefreshAccount(AccountApi $accountApi, Account $account, string $locale = null): void
    {
    }

    public function afterRefreshAccount(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->mapReturnedAccount($account);
    }

    /*** getAddresses() ***********************************************************************************************/
    public function beforeGetAddresses(AccountApi $accountApi, Account $account, string $locale = null): void
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
    public function beforeAddAddress(
        AccountApi $accountApi,
        Account $account,
        Address $address,
        string $locale = null
    ): void {
    }

    public function afterAddAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->mapReturnedAccount($account);
    }

    /*** updateAddress() **********************************************************************************************/
    public function beforeUpdateAddress(
        AccountApi $accountApi,
        Account $account,
        Address $address,
        string $locale = null
    ): void {
    }

    public function afterUpdateAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->mapReturnedAccount($account);
    }

    /*** removeAddress() **********************************************************************************************/
    public function beforeRemoveAddress(
        AccountApi $accountApi,
        Account $account,
        string $addressId,
        string $locale = null
    ): void {
    }

    public function afterRemoveAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->mapReturnedAccount($account);
    }

    /*** setDefaultBillingAddress() ***********************************************************************************/
    public function beforeSetDefaultBillingAddress(
        AccountApi $accountApi,
        Account $account,
        string $addressId,
        string $locale = null
    ): void {
    }

    public function afterSetDefaultBillingAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->mapReturnedAccount($account);
    }

    /*** setDefaultShippingAddress() **********************************************************************************/
    public function beforeSetDefaultShippingAddress(
        AccountApi $accountApi,
        Account $account,
        string $addressId,
        string $locale = null
    ): void {
    }

    public function afterSetDefaultShippingAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->mapReturnedAccount($account);
    }

    public function mapReturnedAccount(Account $account): ?Account
    {
        return null;
    }
}
