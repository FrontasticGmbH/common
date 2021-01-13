<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\AccountApiBundle\Domain\PasswordResetToken;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\LifecycleTrait;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) Central API entry point is OK to have many public methods.
 */
class LifecycleEventDecorator implements AccountApi
{
    use LifecycleTrait;

    /**
     * @var AccountApi
     */
    private $aggregate;

    public function __construct(AccountApi $aggregate, iterable $listeners = [])
    {
        $this->aggregate = $aggregate;

        foreach ($listeners as $listener) {
            $this->addListener($listener);
        }
    }

    /**
     * @return AccountApi
     */
    public function getAggregate(): object
    {
        return $this->aggregate;
    }

    public function getSalutations(string $locale): ?array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function confirmEmail(string $token, string $locale = null): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function create(Account $account, ?Cart $cart = null, string $locale = null): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function update(Account $account, string $locale = null): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function updatePassword(
        Account $account,
        string $oldPassword,
        string $newPassword,
        string $locale = null
    ): Account {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function generatePasswordResetToken(string $email, string $locale = null): PasswordResetToken
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function resetPassword(string $token, string $newPassword, string $locale = null): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function login(Account $account, ?Cart $cart = null, string $locale = null): ?Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function refreshAccount(Account $account, string $locale = null): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @return Account[]
     */
    public function getAddresses(Account $account, string $locale = null): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function addAddress(Account $account, Address $address, string $locale = null): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function updateAddress(Account $account, Address $address, string $locale = null): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function removeAddress(Account $account, string $addressId, string $locale = null): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setDefaultBillingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setDefaultShippingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }
}
