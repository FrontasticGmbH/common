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
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function confirmEmail(string $token, string $locale = null): Account
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function create(Account $account, ?Cart $cart = null, string $locale = null): Account
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function update(Account $account, string $locale = null): Account
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function updatePassword(
        Account $account,
        string $oldPassword,
        string $newPassword,
        string $locale = null
    ): Account {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function generatePasswordResetToken(string $email): PasswordResetToken
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function resetPassword(string $token, string $newPassword, string $locale = null): Account
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function login(Account $account, ?Cart $cart = null, string $locale = null): ?Account
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function refreshAccount(Account $account, string $locale = null): Account
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function getAddresses(Account $account, string $locale = null): array
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function addAddress(Account $account, Address $address, string $locale = null): Account
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function updateAddress(Account $account, Address $address, string $locale = null): Account
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function removeAddress(Account $account, string $addressId, string $locale = null): Account
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function setDefaultBillingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function setDefaultShippingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }

    public function getDangerousInnerClient()
    {
        throw new \Exception("AccountApi is not available for Nextjs projects.");
    }
}
