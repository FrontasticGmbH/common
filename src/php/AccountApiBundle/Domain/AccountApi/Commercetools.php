<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi\Commercetools\Mapper as AccountMapper;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\AccountApiBundle\Domain\DuplicateAccountException;
use Frontastic\Common\AccountApiBundle\Domain\PasswordResetToken;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) Central API entry point is OK to have many public methods.
 */
class Commercetools implements AccountApi
{
    /**
     * @var AccountMapper
     */
    private $accountMapper;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $customerType;

    const TYPE_NAME = 'frontastic-customer-type';

    public function __construct(Client $client, AccountMapper $accountMapper)
    {
        $this->client = $client;
        $this->accountMapper = $accountMapper;
    }

    public function getSalutations(string $locale): ?array
    {
        return null;
    }

    public function confirmEmail(string $token, string $locale = null): Account
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
    public function create(Account $account, ?Cart $cart = null, string $locale = null): Account
    {
        try {
            $account = $this->mapAccount($this->client->post(
                '/customers',
                [],
                [],
                json_encode(
                    array_merge(
                        (array)$account->rawApiInput,
                        [
                            'email' => $account->email,
                            'salutation' => $account->salutation,
                            'firstName' => $account->firstName,
                            'lastName' => $account->lastName,
                            'dateOfBirth' => $account->birthday ? $account->birthday->format('Y-m-d') : null,
                            'password' => $this->sanitizePassword($account->getPassword()),
                            'isEmailVerified' => $account->confirmed,
                            /** @TODO: To guarantee BC only!
                             * This data should be mapped on the corresponding EventDecorator
                             * Remove the commented lines below if the data is already handle in MapAccountDataDecorator
                             */
                            // 'custom' => [
                                // 'type' => $this->getCustomerType(),
                                // 'fields' => [
                                   // 'data' => json_encode($account->data),
                                // ],
                            // ],
                            'anonymousCartId' => $cart ? $cart->cartId : null,
                        ]
                    )
                )
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
            if ($e->getCode() === 400 && $e->getTranslationCode() === 'commercetools.DuplicateField') {
                throw new DuplicateAccountException($account->email, 0, $e);
            }

            if ($cart !== null && $e->getCode() === 400) {
                /*
                 * The cart might already belong to another user so we try to login without the cart.
                 */
                return $this->create($account, null, $locale);
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
    public function update(Account $account, string $locale = null): Account
    {
        $accountVersion = $this->client->get('/customers/' . $account->accountId);

        return $this->mapAccount($this->client->post(
            '/customers/' . $account->accountId,
            [],
            [],
            json_encode([
                'version' => $accountVersion['version'],
                'actions' => array_merge(
                    (array)$account->rawApiInput,
                    [
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
                            'dateOfBirth' => ($account->birthday instanceof \DateTimeInterface
                                ? $account->birthday->format('Y-m-d')
                                : null
                            ),
                        ],
                        /** @TODO: To guarantee BC only!
                         * This data should be mapped on the corresponding EventDecorator
                         * Remove the commented lines below if the data is already handle in MapAccountDataDecorator
                         */
                        // [
                            // 'action' => 'setCustomField',
                            // 'name' => 'data',
                            // 'value' => json_encode($account->data),
                        // ],
                    ]
                ),
            ])
        ));
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function updatePassword(
        Account $account,
        string $oldPassword,
        string $newPassword,
        string $locale = null
    ): Account {
        $accountData = $this->client->get('/customers/' . $account->accountId);

        return $this->mapAccount($this->client->post(
            '/customers/password',
            [],
            [],
            json_encode([
                'id' => $account->accountId,
                'version' => $accountData['version'],
                'currentPassword' => $this->sanitizePassword($oldPassword),
                'newPassword' => $this->sanitizePassword($newPassword),
            ])
        ));
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function generatePasswordResetToken(string $email): PasswordResetToken
    {
        $token = $this->client->post(
            '/customers/password-token',
            [],
            [],
            json_encode([
                'email' => $email,
                'ttlMinutes' => 2 * 24 * 60,
            ])
        );

        return new PasswordResetToken([
            'email' => $email,
            'confirmationToken' => $token['value'],
            'tokenValidUntil' => new \DateTimeImmutable($token['expiresAt']),
        ]);
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function resetPassword(string $token, string $newPassword, string $locale = null): Account
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

    public function login(Account $account, ?Cart $cart = null, string $locale = null): ?Account
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
                return $this->login($account, null, $locale);
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
        if (!$account->confirmed) {
            throw new AuthenticationException('Your email address was not yet verified.');
        }

        return $account;
    }

    public function refreshAccount(Account $account, string $locale = null): Account
    {
        $result = $this->client
            ->fetchAsync(
                '/customers',
                [
                    'where' => 'email="' . $account->email . '"',
                ]
            )
            ->wait();

        if ($result->count >= 1) {
            return $this->mapAccount($result->results[0]);
        } else {
            throw new \OutOfBoundsException('Could not find account with email ' . $account->email);
        }
    }

    /**
     * @return Account[]
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function getAddresses(Account $account, string $locale = null): array
    {
        return $this->mapAddresses($this->client->get('/customers/' . $account->accountId));
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function addAddress(Account $account, Address $address, string $locale = null): Account
    {
        $accountData = $this->client->get('/customers/' . $account->accountId);

        $addressData = $this->accountMapper->mapAddressToData($address);
        unset($addressData['id']);

        $additionalActions = [];
        if ($address->isDefaultBillingAddress || $address->isDefaultShippingAddress) {
            if (($addressData['key'] ?? null) === null) {
                // @phpstan-ignore-next-line
                $addressData['key'] = Uuid::uuid4()->toString();
            }
        }
        if ($address->isDefaultBillingAddress) {
            $additionalActions[] = [
                'action' => 'setDefaultBillingAddress',
                'addressKey' => $addressData['key'],
            ];
        }
        if ($address->isDefaultShippingAddress) {
            $additionalActions[] = [
                'action' => 'setDefaultShippingAddress',
                'addressKey' => $addressData['key'],
            ];
        }

        return $this->mapAccount($this->client->post(
            '/customers/' . $account->accountId,
            [],
            [],
            json_encode([
                'version' => $accountData['version'],
                'actions' => array_merge(
                    [
                        [
                            'action' => 'addAddress',
                            'address' => $addressData,
                        ],
                    ],
                    $additionalActions
                ),
            ])
        ));
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function updateAddress(Account $account, Address $address, string $locale = null): Account
    {
        $accountData = $this->client->get('/customers/' . $account->accountId);

        $addressData = $this->accountMapper->mapAddressToData($address);
        unset($addressData['id']);

        $actions = [
            [
                'action' => 'changeAddress',
                'addressId' => $address->addressId,
                'address' => $addressData,
            ],
        ];

        if ($address->isDefaultBillingAddress) {
            $actions[] = [
                'action' => 'setDefaultBillingAddress',
                'addressId' => $address->addressId,
            ];
        }
        if ($address->isDefaultShippingAddress) {
            $actions[] = [
                'action' => 'setDefaultShippingAddress',
                'addressId' => $address->addressId,
            ];
        }

        return $this->mapAccount($this->client->post(
            '/customers/' . $account->accountId,
            [],
            [],
            json_encode([
                'version' => $accountData['version'],
                'actions' => $actions,
            ])
        ));
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function removeAddress(Account $account, string $addressId, string $locale = null): Account
    {
        $accountData = $this->client->get('/customers/' . $account->accountId);
        return $this->mapAccount($this->client->post(
            '/customers/' . $account->accountId,
            [],
            [],
            json_encode([
                'version' => $accountData['version'],
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
    public function setDefaultBillingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        $accountData = $this->client->get('/customers/' . $account->accountId);
        return $this->mapAccount($this->client->post(
            '/customers/' . $account->accountId,
            [],
            [],
            json_encode([
                'version' => $accountData['version'],
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
    public function setDefaultShippingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        $accountData = $this->client->get('/customers/' . $account->accountId);
        return $this->mapAccount($this->client->post(
            '/customers/' . $account->accountId,
            [],
            [],
            json_encode([
                'version' => $accountData['version'],
                'actions' => [
                    [
                        'action' => 'setDefaultShippingAddress',
                        'addressId' => $addressId,
                    ],
                ],
            ])
        ));
    }

    private function mapAccount(array $accountData): Account
    {
        return new Account([
            'accountId' => $accountData['id'],
            'email' => $accountData['email'],
            'salutation' => $accountData['salutation'] ?? null,
            'firstName' => $accountData['firstName'] ?? null,
            'lastName' => $accountData['lastName'] ?? null,
            'birthday' => isset($accountData['dateOfBirth']) ?
                new \DateTimeImmutable($accountData['dateOfBirth']) :
                null,
            /** @TODO: To guarantee BC only!
             * This data should be mapped on the corresponding EventDecorator
             * Remove the commented lines below if the data is already handle in MapAccountDataDecorator
             */
            // 'data' => json_decode($account['custom']['fields']['data'] ?? '{}'),
            // Do NOT map the password back
            'confirmed' => $accountData['isEmailVerified'],
            'addresses' => $this->mapAddresses($accountData),
            'dangerousInnerAccount' => $accountData,
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
                    'state' => $address['state'] ?? null,
                    'phone' => $address['phone'] ?? null,
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

    private function sanitizePassword(string $password): string
    {
        return str_replace('%', '', $password);
    }
}
