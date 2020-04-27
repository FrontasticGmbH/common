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
        return $this->client
            ->post(
                '/rest/v2/{siteId}/users',
                [
                    'uid' => $account->email,
                    'titleCode' => 'mrs',
                    'firstName' => $account->firstName,
                    'lastName' => $account->firstName,
                    'password' => $account->getPassword(),
                ],
                [
                    'fields' => 'FULL',
                ]
            )
            ->then(function (array $accountData): Account {
                return $this->dataMapper->mapDataToAccount($accountData);
            })
            ->wait();
    }

    public function update(Account $account): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function updatePassword(Account $account, string $oldPassword, string $newPassword): Account
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

    public function getAddresses(Account $account): array
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function addAddress(Account $account, Address $address): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function updateAddress(Account $account, Address $address): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function removeAddress(Account $account, string $addressId): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setDefaultBillingAddress(Account $account, string $addressId): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setDefaultShippingAddress(Account $account, string $addressId): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getDangerousInnerClient()
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }
}
