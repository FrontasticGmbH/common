<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreator;

class SapAccountApi implements AccountApi
{
    /** @var SapClient */
    private $client;

    /** @var SapLocaleCreator */
    private $localeCreator;

    /** @var SapDataMapper */
    private $dataMapper;

    public function __construct(SapClient $client, SapLocaleCreator $localeCreator, SapDataMapper $dataMapper)
    {
        $this->client = $client;
        $this->localeCreator = $localeCreator;
        $this->dataMapper = $dataMapper;
    }

    public function get(string $email): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function confirmEmail(string $token): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function create(Account $account, ?Cart $cart = null): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function update(Account $account): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function updatePassword(string $accountId, string $oldPassword, string $newPassword): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function generatePasswordResetToken(Account $account): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function resetPassword(string $token, string $newPassword): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function login(Account $account, ?Cart $cart = null): bool
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getAddresses(string $accountId): array
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function addAddress(string $accountId, Address $address): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function updateAddress(string $accountId, Address $address): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function removeAddress(string $accountId, string $addressId): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setDefaultBillingAddress(string $accountId, string $addressId): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setDefaultShippingAddress(string $accountId, string $addressId): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getDangerousInnerClient()
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }
}
