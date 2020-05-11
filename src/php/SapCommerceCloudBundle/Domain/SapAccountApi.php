<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\AccountApiBundle\Domain\DuplicateAccountException;
use Frontastic\Common\AccountApiBundle\Domain\PasswordResetToken;
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

    public function getSalutations(string $locale): ?array
    {
        /// @FIXME implement
        return ['Mrs.'];
    }

    public function confirmEmail(string $token, string $locale = null): Account
    {
        throw new \RuntimeException('Email confirmation is not supported by the SAP commerce cloud account API.');
    }

    public function create(Account $account, ?Cart $cart = null, string $locale = null): Account
    {
        return $this->client
            ->post(
                '/rest/v2/{siteId}/users',
                [
                    'uid' => $account->email,
                    'titleCode' => 'mrs',
                    'firstName' => $account->firstName,
                    'lastName' => $account->lastName,
                    'password' => $account->getPassword(),
                ],
                [
                    'fields' => 'FULL',
                ]
            )
            ->then(function (array $accountData): Account {
                return $this->dataMapper->mapDataToAccount($accountData);
            })
            ->otherwise(function (\Throwable $throwable) use ($account): Account {
                if (!$throwable instanceof SapRequestException) {
                    throw $throwable;
                }

                if ($throwable->hasErrorType('DuplicateUidError')) {
                    throw new DuplicateAccountException($account->email, 0, $throwable);
                }
                throw $throwable;
            })
            ->wait();
    }

    public function update(Account $account, string $locale = null): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function updatePassword(
        Account $account,
        string $oldPassword,
        string $newPassword,
        string $locale = null
    ): Account {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function generatePasswordResetToken(string $email, string $locale = null): PasswordResetToken
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function resetPassword(string $token, string $newPassword, string $locale = null): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function login(Account $account, ?Cart $cart = null, string $locale = null): ?Account
    {
        if (!$this->client->checkAccountCredentials($account->email, $account->getPassword())) {
            return null;
        }

        return $this->refreshAccount($account);
    }

    public function refreshAccount(Account $account, string $locale = null): Account
    {
        return $this->client
            ->get(
                '/rest/v2/{siteId}/users/' . $account->email,
                [
                    'fields' => 'FULL',
                ]
            )
            ->then(function (array $accountData): Account {
                return $this->dataMapper->mapDataToAccount($accountData);
            })
            ->wait();
    }

    public function getAddresses(Account $account, string $locale = null): array
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function addAddress(Account $account, Address $address, string $locale = null): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function updateAddress(Account $account, Address $address, string $locale = null): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function removeAddress(Account $account, string $addressId, string $locale = null): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setDefaultBillingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setDefaultShippingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getDangerousInnerClient()
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }
}
