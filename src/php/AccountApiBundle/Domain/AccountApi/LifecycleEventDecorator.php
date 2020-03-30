<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
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
    protected function getAggregate(): object
    {
        return $this->aggregate;
    }

    public function get(string $email): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function confirmEmail(string $token): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function create(Account $account, ?Cart $cart = null): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function verifyEmail(string $token): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function update(Account $account): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function updatePassword(string $accountId, string $oldPassword, string $newPassword): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function generatePasswordResetToken(Account $account): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function resetPassword(string $token, string $newPassword): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function login(Account $account, ?Cart $cart = null): bool
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @return Account[]
     */
    public function getAddresses(string $accountId): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function addAddress(string $accountId, Address $address): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function updateAddress(string $accountId, Address $address): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function removeAddress(string $accountId, string $addressId): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setDefaultBillingAddress(string $accountId, string $addressId): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setDefaultShippingAddress(string $accountId, string $addressId): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }
}
