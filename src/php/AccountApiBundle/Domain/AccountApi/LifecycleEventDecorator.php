<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi;

use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\LifecycleTrait;

/**
 * Class LifecycleEventDecorator
 *
 * @package Frontastic\Common\AccountApiBundle\Domain\AccountApi
 */
class LifecycleEventDecorator implements AccountApi
{
    use LifecycleTrait;

    /**
     * @var \Frontastic\Common\AccountApiBundle\Domain\AccountApi
     */
    private $aggregate;

    /**
     * LifecycleEventDecorator constructor.
     *
     * @param \Frontastic\Common\AccountApiBundle\Domain\AccountApi $aggregate
     * @param iterable $listeners
     */
    public function __construct(AccountApi $aggregate, iterable $listeners = [])
    {
        $this->aggregate = $aggregate;

        foreach ($listeners as $listener) {
            $this->addListener($listener);
        }
    }

    /**
     * @return \Frontastic\Common\AccountApiBundle\Domain\AccountApi
     */
    protected function getAggregate(): object
    {
        return $this->aggregate;
    }

    /**
     * @param string $email
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function get(string $email): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $token
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function confirmEmail(string $token): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\AccountApiBundle\Domain\Account $account
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function create(Account $account): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $token
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function verifyEmail(string $token): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\AccountApiBundle\Domain\Account $account
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function update(Account $account): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $accountId
     * @param string $oldPassword
     * @param string $newPassword
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function updatePassword(string $accountId, string $oldPassword, string $newPassword): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\AccountApiBundle\Domain\Account $account
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function generatePasswordResetToken(Account $account): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $token
     * @param string $newPassword
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function resetPassword(string $token, string $newPassword): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\AccountApiBundle\Domain\Account $account
     * @return bool
     */
    public function login(Account $account): bool
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $accountId
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account[]
     */
    public function getAddresses(string $accountId): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $accountId
     * @param \Frontastic\Common\AccountApiBundle\Domain\Address $address
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function addAddress(string $accountId, Address $address): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $accountId
     * @param \Frontastic\Common\AccountApiBundle\Domain\Address $address
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function updateAddress(string $accountId, Address $address): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $accountId
     * @param string $addressId
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function removeAddress(string $accountId, string $addressId): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $accountId
     * @param string $addressId
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function setDefaultBillingAddress(string $accountId, string $addressId): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $accountId
     * @param string $addressId
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function setDefaultShippingAddress(string $accountId, string $addressId): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }
}
