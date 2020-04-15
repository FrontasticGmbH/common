<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\CartApiBundle\Domain\Cart;

interface AccountApi
{
    public function get(string $email): Account;

    public function confirmEmail(string $token): Account;

    public function create(Account $account, ?Cart $cart = null): Account;

    public function update(Account $account): Account;

    public function updatePassword(Account $account, string $oldPassword, string $newPassword): Account;

    public function generatePasswordResetToken(Account $account): Account;

    public function resetPassword(string $token, string $newPassword): Account;

    public function login(Account $account, ?Cart $cart = null): bool;

    /**
     * @return Address[]
     */
    public function getAddresses(Account $account): array;

    public function addAddress(Account $account, Address $address): Account;

    public function updateAddress(Account $account, Address $address): Account;

    public function removeAddress(Account $account, string $addressId): Account;

    public function setDefaultBillingAddress(Account $account, string $addressId): Account;

    public function setDefaultShippingAddress(Account $account, string $addressId): Account;

    /**
     * Get *dangerous* inner client
     *
     * This method exists to enable you to use features which are not yet part
     * of the abstraction layer.
     *
     * Be aware that any usage of this method might seriously hurt backwards
     * compatibility and the future abstractions might differ a lot from the
     * vendor provided abstraction.
     *
     * Use this with care for features necessary in your customer and talk with
     * Frontastic about provising an abstraction.
     *
     * @return mixed
     */
    public function getDangerousInnerClient();
}
