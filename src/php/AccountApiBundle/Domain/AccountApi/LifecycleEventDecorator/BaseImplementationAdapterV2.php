<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi\LifecycleEventDecorator;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\AccountApiBundle\Domain\PasswordResetToken;
use Frontastic\Common\CartApiBundle\Domain\Cart;

class BaseImplementationAdapterV2 extends BaseImplementationV2
{
    /**
     * @var BaseImplementation
     */
    private $baseImplementation;

    public function __construct(BaseImplementation $baseImplementation)
    {
        $this->baseImplementation = $baseImplementation;
    }

    public function beforeGetSalutations(AccountApi $accountApi, string $locale): ?array
    {
        $this->baseImplementation->beforeGetSalutations($accountApi, $locale);
        return [$locale];
    }

    public function afterGetSalutations(AccountApi $accountApi, ?array $salutations): ?array
    {
        return $this->baseImplementation->afterGetSalutations($accountApi, $salutations);
    }

    public function beforeConfirmEmail(AccountApi $accountApi, string $token, string $locale = null): ?array
    {
        $this->baseImplementation->beforeConfirmEmail($accountApi, $token, $locale);
        return [$token, $locale];
    }

    public function afterConfirmEmail(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->baseImplementation->afterConfirmEmail($accountApi, $account);
    }

    public function beforeCreate(
        AccountApi $accountApi,
        Account $account,
        ?Cart $cart = null,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeCreate($accountApi, $account, $cart, $locale);
        return [$account, $cart, $locale];
    }

    public function afterCreate(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->baseImplementation->afterCreate($accountApi, $account);
    }

    public function beforeUpdate(AccountApi $accountApi, Account $account, string $locale = null): ?array
    {
        $this->baseImplementation->beforeUpdate($accountApi, $account, $locale);
        return [$account, $locale];
    }

    public function afterUpdate(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->baseImplementation->afterUpdate($accountApi, $account);
    }

    public function beforeUpdatePassword(
        AccountApi $accountApi,
        Account $account,
        string $oldPassword,
        string $newPassword,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeUpdatePassword(
            $accountApi,
            $account,
            $oldPassword,
            $newPassword,
            $locale
        );
        return [$account, $oldPassword, $newPassword, $locale];
    }

    public function afterUpdatePassword(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->baseImplementation->afterUpdatePassword($accountApi, $account);
    }

    public function beforeGeneratePasswordResetToken(AccountApi $accountApi, string $email): ?array
    {
        $this->baseImplementation->beforeGeneratePasswordResetToken($accountApi, $email);
        return [$email];
    }

    public function afterGeneratePasswordResetToken(
        AccountApi $accountApi,
        PasswordResetToken $token
    ): ?PasswordResetToken {
        return $this->baseImplementation->afterGeneratePasswordResetToken($accountApi, $token);
    }

    public function beforeResetPassword(
        AccountApi $accountApi,
        string $token,
        string $newPassword,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeResetPassword($accountApi, $token, $newPassword, $locale);
        return [$token, $newPassword, $locale];
    }

    public function afterResetPassword(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->baseImplementation->afterResetPassword($accountApi, $account);
    }

    public function beforeLogin(
        AccountApi $accountApi,
        Account $account,
        ?Cart $cart = null,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeLogin($accountApi, $account, $cart, $locale);
        return [$account, $cart, $locale];
    }

    public function afterLogin(AccountApi $accountApi, ?Account $account = null): ?Account
    {
        return $this->baseImplementation->afterLogin($accountApi, $account);
    }

    public function beforeRefreshAccount(AccountApi $accountApi, Account $account, string $locale = null): ?array
    {
        $this->baseImplementation->beforeRefreshAccount($accountApi, $account, $locale);
        return [$account, $locale];
    }

    public function afterRefreshAccount(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->baseImplementation->afterRefreshAccount($accountApi, $account);
    }

    public function beforeGetAddresses(AccountApi $accountApi, Account $account, string $locale = null): ?array
    {
        $this->baseImplementation->beforeGetAddresses($accountApi, $account, $locale);
        return [$account, $locale];
    }

    /**
     * @param AccountApi $accountApi
     * @param Address[] $addresses
     * @return Address[]|null
     */
    public function afterGetAddresses(AccountApi $accountApi, array $addresses): ?array
    {
        return $this->baseImplementation->afterGetAddresses($accountApi, $addresses);
    }

    public function beforeAddAddress(
        AccountApi $accountApi,
        Account $account,
        Address $address,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeAddAddress($accountApi, $account, $address, $locale);
        return [$account, $address, $locale];
    }

    public function afterAddAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->baseImplementation->afterAddAddress($accountApi, $account);
    }

    public function beforeUpdateAddress(
        AccountApi $accountApi,
        Account $account,
        Address $address,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeUpdateAddress($accountApi, $account, $address, $locale);
        return [$account, $address, $locale];
    }

    public function afterUpdateAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->baseImplementation->afterUpdateAddress($accountApi, $account);
    }

    public function beforeRemoveAddress(
        AccountApi $accountApi,
        Account $account,
        string $addressId,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeRemoveAddress($accountApi, $account, $addressId, $locale);
        return [$account, $addressId, $locale];
    }

    public function afterRemoveAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->baseImplementation->afterRemoveAddress($accountApi, $account);
    }

    public function beforeSetDefaultBillingAddress(
        AccountApi $accountApi,
        Account $account,
        string $addressId,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeSetDefaultBillingAddress($accountApi, $account, $addressId, $locale);
        return [$account, $addressId, $locale];
    }

    public function afterSetDefaultBillingAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->baseImplementation->afterSetDefaultBillingAddress($accountApi, $account);
    }

    public function beforeSetDefaultShippingAddress(
        AccountApi $accountApi,
        Account $account,
        string $addressId,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeSetDefaultShippingAddress($accountApi, $account, $addressId, $locale);
        return [$account, $addressId, $locale];
    }

    public function afterSetDefaultShippingAddress(AccountApi $accountApi, Account $account): ?Account
    {
        return $this->baseImplementation->afterSetDefaultShippingAddress($accountApi, $account);
    }

    public function mapReturnedAccount(Account $account): ?Account
    {
        return $this->baseImplementation->mapReturnedAccount($account);
    }
}
