<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use Frontastic\Common\AccountApiBundle\Domain\Payment;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;

class Commercetools implements AccountApi
{
    /**
     * @var Client
     */
    private $client;

    private $customerType;

    const TYPE_NAME = 'frontastic-customer-type';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get(string $email): Account
    {
        $result = $this->client->fetch('/customers', [
            'where' => 'email="' . $email . '"',
        ]);

        if ($result->count >= 1) {
            return $this->mapAccount($result->results[0]);
        } else {
            throw new \OutOfBoundsException('Could not find account with email ' . $email);
        }
    }

    public function confirmEmail(string $token): Account
    {
        try {
            return $this->mapAccount($this->client->post(
                '/customers/email/confirm',
                [],
                [],
                json_encode([
                    'tokenValue' => $token,
                ])
            ));
        } catch (RequestException $e) {
            throw new \OutOfBoundsException('Could not find account with confirmation token ' . $token, 0, $e);
        }
    }

    public function create(Account $account): Account
    {
        $account = $this->mapAccount($this->client->post(
            '/customers',
            [],
            [],
            json_encode([
                'email' => $account->email,
                'salutation' => $account->salutation,
                'firstName' => $account->firstName,
                'lastName' => $account->lastName,
                'dateOfBirth' => $account->birthday->format('Y-m-d'),
                'password' => $account->getPassword(),
                'isEmailVerified' => $account->confirmed,
                'custom' => [
                    'type' => $this->getCustomerType(),
                    'fields' => [
                        'data' => json_encode($account->data),
                    ],
                ],
            ])
        )['customer']);

        $token = $this->client->post(
            '/customers/email-token',
            [],
            [],
            json_encode([
                'id' => $account->accountId,
                'ttlMinutes' => 2 * 7 * 24 * 60,
            ])
        );

        $account->confirmationToken = $token['value'];
        $account->tokenValidUntil = new \DateTimeImmutable($token['expiresAt']);

        return $account;
    }

    public function verifyEmail(string $token): Account
    {
        return $this->mapAccount($this->client->post(
            '/customers/email/confirm',
            [],
            [],
            json_encode([
                'token' => $token,
            ])
        ));
    }

    public function update(Account $account): Account
    {
        $accountVersion = $this->client->get('/customers/' . $account->accountId);
        return $this->mapAccount($this->client->post(
            '/customers/' . $account->accountId,
            [],
            [],
            json_encode([
                'version' => $accountVersion['version'],
                'actions' => [
                    [
                        'action' => 'setFirstName',
                        'firstName' => $account->firstName,
                    ],
                    [
                        'action' => 'setLastName',
                        'lastName' => $account->lastName,
                    ],
                    [
                        'action' => 'setSalutation',
                        'salutation' => $account->salutation,
                    ],
                    [
                        'action' => 'setDateOfBirth',
                        'dateOfBirth' => $account->birthday->format('Y-m-d'),
                    ],
                    [
                        'action' => 'setCustomField',
                        'name' => 'data',
                        'value' => json_encode($account->data),
                    ],
                ],
            ])
        ));
    }

    public function updatePassword(string $accountId, string $oldPassword, string $newPassword): Account
    {
        $account= $this->client->get('/customers/' . $accountId);
        return $this->mapAccount($this->client->post(
            '/customers/password',
            [],
            [],
            json_encode([
                'id' => $accountId,
                'version' => $account['version'],
                'currentPassword' => $oldPassword,
                'newPassword' => $newPassword,
            ])
        ));
    }

    public function generatePasswordResetToken(Account $account): Account
    {
        $token = $this->client->post(
            '/customers/password-token',
            [],
            [],
            json_encode([
                'email' => $account->email,
                'ttlMinutes' => 2 * 24 * 60,
            ])
        );

        $account->confirmationToken = $token['value'];
        $account->tokenValidUntil = new \DateTimeImmutable($token['expiresAt']);

        return $account;
    }

    public function resetPassword(string $token, string $newPassword): Account
    {
        return $this->mapAccount($this->client->post(
            '/customers/password/reset',
            [],
            [],
            json_encode([
                'tokenValue' => $token,
                'newPassword' => $newPassword,
            ])
        ));
    }

    public function login(Account $account): bool
    {
        try {
            $account = $this->mapAccount($this->client->post(
                '/login',
                [],
                [],
                json_encode([
                    // @TODO: We should pass existing anonymous cart IDs so
                    // that this cart is merged into the users cart.
                    'email' => $account->email,
                    'password' => $account->getPassword(),
                ])
            )['customer']);

            return $account->confirmed;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAddresses(string $accountId): array
    {
        return $this->mapAddresses($this->client->get('/customers/' . $accountId));
    }

    public function addAddress(string $accountId, Address $address): Account
    {
        $account = $this->client->get('/customers/' . $accountId);
        return $this->mapAccount($this->client->post(
            '/customers/' . $accountId,
            [],
            [],
            json_encode([
                'version' => $account['version'],
                'actions' => [
                    [
                        'action' => 'addAddress',
                        'address' => [
                            'firstName' => $address->firstName,
                            'lastName' => $address->lastName,
                            'streetName' => $address->streetName,
                            'streetNumber' => $address->streetNumber,
                            'additionalStreetInfo' => $address->additionalStreetInfo,
                            'postalCode' => $address->postalCode,
                            'city' => $address->city,
                            'country' => $address->country,
                        ],
                    ],
                ],
            ])
        ));
    }

    private function mapAccount(array $account): Account
    {
        return new Account([
            'accountId' => $account['id'],
            'email' => $account['email'],
            'salutation' => $account['salutation'] ?? null,
            'firstName' => $account['firstName'] ?? null,
            'lastName' => $account['lastName'] ?? null,
            'birthday' => isset($account['dateOfBirth']) ? new \DateTimeImmutable($account['dateOfBirth']) : null,
            'data' => json_decode($account['custom']['fields']['data'] ?? '{}'),
            // Do NOT map the password back
            'confirmed' => $account['isEmailVerified'],
        ]);
    }

    private function mapAddresses(array $account): array
    {
        return array_map(
            function (array $address): Address {;
                return new Address([
                    'addressId' => $address['id'],
                    'firstName' => $address['firstName'],
                    'lastName' => $address['lastName'],
                    'streetName' => $address['streetName'],
                    'streetNumber' => $address['streetNumber'],
                    'additionalStreetInfo' => $address['additionalStreetInfo'],
                    'postalCode' => $address['postalCode'],
                    'city' => $address['city'],
                    'country' => $address['country'],
                ]);
            },
            $account['addresses']
        );
    }

    /**
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client
     */
    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    public function getCustomerType()
    {
        if ($this->customerType) {
            return $this->customerType;
        }

        try {
            $customerType = $this->client->get('/types/key=' . self::TYPE_NAME);
        } catch (RequestException $e) {
            $customerType = $this->createCustomerType();
        }

        return $this->customerType = ['id' => $customerType['id']];
    }

    private function createCustomerType()
    {
        return $this->client->post(
            '/types',
            [],
            [],
            json_encode([
                'key' => self::TYPE_NAME,
                'name' => ['de' => 'Frontastic Customer'],
                'description' => ['de' => 'Additional data fields'],
                'resourceTypeIds' => ['customer'],
                'fieldDefinitions' => [
                    [
                        'name' => 'data',
                        'type' => ['name' => 'String'],
                        'label' => ['de' => 'Data (JSON)'],
                        'required' => false,
                    ],
                ],
            ])
        );
    }
}
