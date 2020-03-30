<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) Central API entry point is OK to have many public methods.
 */
class Commercetools implements AccountApi
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $customerType;

    const TYPE_NAME = 'frontastic-customer-type';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function get(string $email): Account
    {
        $result = $this->client
            ->fetchAsync(
                '/customers',
                [
                    'where' => 'email="' . $email . '"',
                ]
            )
            ->wait();

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

    /**
     * @todo Should we catch the RequestException here?
     */
    public function create(Account $account, ?Cart $cart = null): Account
    {
        try {
            $account = $this->mapAccount($this->client->post(
                '/customers',
                [],
                [],
                json_encode([
                    'email' => $account->email,
                    'salutation' => $account->salutation,
                    'firstName' => $account->firstName,
                    'lastName' => $account->lastName,
                    'dateOfBirth' => $account->birthday ? $account->birthday->format('Y-m-d') : null,
                    'password' => $this->sanitizePassword($account->getPassword()),
                    'isEmailVerified' => $account->confirmed,
                    'custom' => [
                        'type' => $this->getCustomerType(),
                        'fields' => [
                            'data' => json_encode($account->data),
                        ],
                    ],
                    'anonymousCartId' => $cart ? $cart->cartId : null,
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
        } catch (RequestException $e) {
            if ($cart !== null && $e->getCode() === 400) {
                /*
                 * The cart might already belong to another user so we try to login without the cart.
                 */
                return $this->create($account);
            }

            throw $e;
        }

        $account->confirmationToken = $token['value'];
        $account->tokenValidUntil = new \DateTimeImmutable($token['expiresAt']);

        return $account;
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
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

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
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

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function updatePassword(string $accountId, string $oldPassword, string $newPassword): Account
    {
        $account = $this->client->get('/customers/' . $accountId);
        return $this->mapAccount($this->client->post(
            '/customers/password',
            [],
            [],
            json_encode([
                'id' => $accountId,
                'version' => $account['version'],
                'currentPassword' => $this->sanitizePassword($oldPassword),
                'newPassword' => $this->sanitizePassword($newPassword),
            ])
        ));
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
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

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function resetPassword(string $token, string $newPassword): Account
    {
        return $this->mapAccount($this->client->post(
            '/customers/password/reset',
            [],
            [],
            json_encode([
                'tokenValue' => $token,
                'newPassword' => $this->sanitizePassword($newPassword),
            ])
        ));
    }

    public function login(Account $account, ?Cart $cart = null): bool
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
                    'password' => $this->sanitizePassword($account->getPassword()),
                    'anonymousCartId' => $cart ? $cart->cartId : null,
                ])
            )['customer']);
        } catch (RequestException $e) {
            if ($cart !== null && $e->getCode() === 400) {
                /*
                 * The cart might already belong to another user so we try to login without the cart.
                 */
                return $this->login($account);
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
        if (!$account->confirmed) {
            throw new AuthenticationException('Your email address was not yet verified.');
        }

        return $account->confirmed;
    }

    /**
     * @return Account[]
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function getAddresses(string $accountId): array
    {
        return $this->mapAddresses($this->client->get('/customers/' . $accountId));
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
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
                            'salutation' => $address->salutation,
                            'firstName' => $address->firstName,
                            'lastName' => $address->lastName,
                            'streetName' => $address->streetName,
                            'streetNumber' => $address->streetNumber,
                            'additionalStreetInfo' => $address->additionalStreetInfo,
                            'additionalAddressInfo' => $address->additionalAddressInfo,
                            'postalCode' => $address->postalCode,
                            'city' => $address->city,
                            'country' => $address->country,
                        ],
                    ],
                ],
            ])
        ));
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function updateAddress(string $accountId, Address $address): Account
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
                        'action' => 'changeAddress',
                        'addressId' => $address->addressId,
                        'address' => [
                            'salutation' => $address->salutation,
                            'firstName' => $address->firstName,
                            'lastName' => $address->lastName,
                            'streetName' => $address->streetName,
                            'streetNumber' => $address->streetNumber,
                            'additionalStreetInfo' => $address->additionalStreetInfo,
                            'additionalAddressInfo' => $address->additionalAddressInfo,
                            'postalCode' => $address->postalCode,
                            'city' => $address->city,
                            'country' => $address->country,
                        ],
                    ],
                ],
            ])
        ));
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function removeAddress(string $accountId, string $addressId): Account
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
                        'action' => 'removeAddress',
                        'addressId' => $addressId,
                    ],
                ],
            ])
        ));
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function setDefaultBillingAddress(string $accountId, string $addressId): Account
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
                        'action' => 'setDefaultBillingAddress',
                        'addressId' => $addressId,
                    ],
                ],
            ])
        ));
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function setDefaultShippingAddress(string $accountId, string $addressId): Account
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
                        'action' => 'setDefaultShippingAddress',
                        'addressId' => $addressId,
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
            'addresses' => $this->mapAddresses($account),
            'dangerousInnerAccount' => $account,
        ]);
    }

    /**
     * @return Address[]
     */
    private function mapAddresses(array $account): array
    {
        $account = array_merge(
            [
                'defaultBillingAddressId' => null,
                'defaultShippingAddressId' => null,
            ],
            $account
        );

        return array_map(
            function (array $address) use ($account): Address {
                return new Address([
                    'addressId' => $address['id'],
                    'salutation' => $address['salutation'] ?? 'Herr',
                    'firstName' => $address['firstName'] ?? null,
                    'lastName' => $address['lastName'] ?? null,
                    'streetName' => $address['streetName'] ?? null,
                    'streetNumber' => $address['streetNumber'] ?? null,
                    'additionalStreetInfo' => $address['additionalStreetInfo'] ?? null,
                    'additionalAddressInfo' => $address['additionalAddressInfo'] ?? null,
                    'postalCode' => $address['postalCode'] ?? null,
                    'city' => $address['city'] ?? null,
                    'country' => $address['country'] ?? null,
                    'isDefaultBillingAddress' => ($address['id'] === $account['defaultBillingAddressId']),
                    'isDefaultShippingAddress' => ($address['id'] === $account['defaultShippingAddressId']),
                    'dangerousInnerAddress' => $address,
                ]);
            },
            $account['addresses']
        );
    }

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
     * @return Client
     */
    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    /**
     * @throws RequestException
     */
    public function getCustomerType(): array
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

    /**
     * @throws RequestException
     */
    private function createCustomerType(): array
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

    private function sanitizePassword(string $password): string
    {
        return str_replace('%', '', $password);
    }
}
