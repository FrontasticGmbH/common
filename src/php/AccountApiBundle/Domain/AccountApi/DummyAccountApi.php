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
        throw $this->exception();
    }

    public function confirmEmail(string $token, string $locale = null): Account
    {
        throw $this->exception();
    }

    public function create(Account $account, ?Cart $cart = null, string $locale = null): Account
    {
        throw $this->exception();
    }

    public function update(Account $account, string $locale = null): Account
    {
        throw $this->exception();
    }

    public function updatePassword(
        Account $account,
        string $oldPassword,
        string $newPassword,
        string $locale = null
    ): Account {
        throw $this->exception();
    }

    public function generatePasswordResetToken(string $email): PasswordResetToken
    {
        throw $this->exception();
    }

    public function resetPassword(string $token, string $newPassword, string $locale = null): Account
    {
        throw $this->exception();
    }

    public function login(Account $account, ?Cart $cart = null, string $locale = null): ?Account
    {
        throw $this->exception();
    }

    public function refreshAccount(Account $account, string $locale = null): Account
    {
        throw $this->exception();
    }

    public function getAddresses(Account $account, string $locale = null): array
    {
        throw $this->exception();
    }

    public function addAddress(Account $account, Address $address, string $locale = null): Account
    {
        throw $this->exception();
    }

    public function updateAddress(Account $account, Address $address, string $locale = null): Account
    {
        throw $this->exception();
    }

    public function removeAddress(Account $account, string $addressId, string $locale = null): Account
    {
        throw $this->exception();
    }

    public function setDefaultBillingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        throw $this->exception();
    }

    public function setDefaultShippingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        throw $this->exception();
    }

    public function getDangerousInnerClient()
    {
        throw $this->exception();
    }

    private function exception(): \Throwable
    {
        return new \Exception("AccountApi is not available for Nextjs projects.");
    }
}
