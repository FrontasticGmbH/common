<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\CartApiBundle\Domain\Cart;

interface AccountApi
{
    /**
     * @param string $email
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function get(string $email): Account;

    /**
     * @param string $token
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function confirmEmail(string $token): Account;

    public function create(Account $account, ?Cart $cart = null): Account;

    /**
     * @param string $token
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function verifyEmail(string $token): Account;

    /**
     * @param \Frontastic\Common\AccountApiBundle\Domain\Account $account
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function update(Account $account): Account;

    /**
     * @param string $accountId
     * @param string $oldPassword
     * @param string $newPassword
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function updatePassword(string $accountId, string $oldPassword, string $newPassword): Account;

    /**
     * @param \Frontastic\Common\AccountApiBundle\Domain\Account $account
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function generatePasswordResetToken(Account $account): Account;

    /**
     * @param string $token
     * @param string $newPassword
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function resetPassword(string $token, string $newPassword): Account;

    public function login(Account $account, ?Cart $cart = null): bool;

    /**
     * @param string $accountId
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account[]
     */
    public function getAddresses(string $accountId): array;

    /**
     * @param string $accountId
     * @param \Frontastic\Common\AccountApiBundle\Domain\Address $address
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function addAddress(string $accountId, Address $address): Account;

    /**
     * @param string $accountId
     * @param \Frontastic\Common\AccountApiBundle\Domain\Address $address
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function updateAddress(string $accountId, Address $address): Account;

    /**
     * @param string $accountId
     * @param string $addressId
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function removeAddress(string $accountId, string $addressId): Account;

    /**
     * @param string $accountId
     * @param string $addressId
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function setDefaultBillingAddress(string $accountId, string $addressId): Account;

    /**
     * @param string $accountId
     * @param string $addressId
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function setDefaultShippingAddress(string $accountId, string $addressId): Account;

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
