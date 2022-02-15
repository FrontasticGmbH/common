<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\AccountApiBundle\Domain\PasswordResetToken;
use Frontastic\Common\CartApiBundle\Domain\Cart;

class DummyAccountApi implements AccountApi
{
    public function getSalutations(string $locale): ?array
    {
        $this->doNotUse();
    }

    public function confirmEmail(string $token, string $locale = null): Account
    {
        $this->doNotUse();
    }

    public function create(Account $account, ?Cart $cart = null, string $locale = null): Account
    {
        $this->doNotUse();
    }

    public function update(Account $account, string $locale = null): Account
    {
        $this->doNotUse();
    }

    public function updatePassword(
        Account $account,
        string $oldPassword,
        string $newPassword,
        string $locale = null
    ): Account {
        $this->doNotUse();
    }

    public function generatePasswordResetToken(string $email): PasswordResetToken
    {
        $this->doNotUse();
    }

    public function resetPassword(string $token, string $newPassword, string $locale = null): Account
    {
        $this->doNotUse();
    }

    public function login(Account $account, ?Cart $cart = null, string $locale = null): ?Account
    {
        $this->doNotUse();
    }

    public function refreshAccount(Account $account, string $locale = null): Account
    {
        $this->doNotUse();
    }

    public function getAddresses(Account $account, string $locale = null): array
    {
        $this->doNotUse();
    }

    public function addAddress(Account $account, Address $address, string $locale = null): Account
    {
        $this->doNotUse();
    }

    public function updateAddress(Account $account, Address $address, string $locale = null): Account
    {
        $this->doNotUse();
    }

    public function removeAddress(Account $account, string $addressId, string $locale = null): Account
    {
        $this->doNotUse();
    }

    public function setDefaultBillingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        $this->doNotUse();
    }

    public function setDefaultShippingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        $this->doNotUse();
    }

    public function getDangerousInnerClient()
    {
        $this->doNotUse();
    }

    private function doNotUse(): void
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }
}
