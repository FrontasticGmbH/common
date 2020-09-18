<?php

namespace Frontastic\Common\ShopifyBundle\Domain\AccountApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\AccountApiBundle\Domain\DuplicateAccountException;
use Frontastic\Common\AccountApiBundle\Domain\PasswordResetToken;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\ShopifyBundle\Domain\ShopifyClient;

class ShopifyAccountApi implements AccountApi
{
    private const DEFAULT_ELEMENTS_TO_FETCH = 10;

    /**
     * @var ShopifyClient
     */
    private $client;

    public function __construct(ShopifyClient $client)
    {
        $this->client = $client;
    }

    public function getSalutations(string $locale): ?array
    {
        // TODO: Implement getSalutations() method.
        return ['Mrs.'];
    }

    public function confirmEmail(string $token, string $locale = null): Account
    {
        throw new RuntimeException('Email confirmation is not supported by the Shopify Storefront API.');
    }

    public function create(Account $account, ?Cart $cart = null, string $locale = null): Account
    {
        $mutation = "
            mutation {
                customerCreate(input: {
                    email: \"$account->email\",
                    password: \"{$account->getPassword()}\",
                    firstName: \"$account->firstName\"
                    lastName: \"$account->lastName\"
                }) {
                    customer {
                        id
                        firstName
                        lastName
                        email
                    }
                    customerUserErrors {
                        code
                        field
                        message
                    }
                }
            }";

        return $this->client
            ->request($mutation, $locale)
            ->then(function ($result) use ($account) : Account {
                if ($result['errors']) {
                    // TODO handle error
                }

                if ($result['body']['data']['customerCreate']['customerUserErrors']) {
                    if ($result['body']['data']['customerCreate']['customerUserErrors'][0]['code'] == "TAKEN") {
                        throw new DuplicateAccountException($account->email);
                    }
                }

                return $this->login($account);
            })
            ->wait();
    }

    public function update(Account $account, string $locale = null): Account
    {
        if (is_null($account->authToken)) {
            $account = $this->login($account);
        }

        $mutation = "
            mutation {
                customerUpdate(
                    customer: {
                        email: \"$account->email\",
                        password: \"{$account->getPassword()}\",
                        firstName: \"$account->firstName\"
                        lastName: \"$account->lastName\"
                    },
                    customerAccessToken: \"$account->authToken\"
                ) {
                    customer {
                        id
                        firstName
                        lastName
                        email
                        addresses(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    id
                                    address1
                                    address2
                                    city
                                    country
                                    firstName
                                    lastName
                                    phone
                                    province
                                    zip
                                }
                            }
                        }
                    }
                    customerAccessToken {
                        accessToken
                        expiresAt
                    }
                    customerUserErrors {
                        code
                        field
                        message
                    }
                }
            }";

        return $this->client
            ->request($mutation, $locale)
            ->then(function ($result) use ($account) : Account {
                if ($result['errors']) {
                    // TODO handle error
                }

                $updatedAccount = $this->mapDataToAccount($result['body']['data']['customerUpdate']['customer']);
                $updatedAccount->authToken = $account->authToken;

                return $updatedAccount;
            })
            ->wait();
    }

    public function updatePassword(Account $account, string $oldPassword, string $newPassword, string $locale = null): Account
    {
        $account->setPassword($oldPassword);

        if (is_null($account = $this->login($account))) {
            // TODO handle error
        }

        $account->setPassword($newPassword);

        return $this->update($account);
    }

    public function generatePasswordResetToken(string $email): PasswordResetToken
    {
        // TODO: Implement generatePasswordResetToken() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function resetPassword(string $token, string $newPassword, string $locale = null): Account
    {
        // TODO: Implement resetPassword() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function login(Account $account, ?Cart $cart = null, string $locale = null): ?Account
    {
        $mutation = "
            mutation {
                customerAccessTokenCreate(input: {
                    email: \"$account->email\",
                    password: \"{$account->getPassword()}\"
                }) {
                    customerAccessToken {
                        accessToken
                        expiresAt
                    }
                    customerUserErrors {
                        code
                        field
                        message
                    }
                }
            }";

        return $this->client
            ->request($mutation, $locale)
            ->then(function ($result) {
                if ($result['errors']) {
                    // TODO handle error
                }

                return $result['body']['data']['customerAccessTokenCreate']['customerAccessToken'];
            })
            ->then(function ($token) {
                if (is_null($token)) {
                    return null;
                }

                $query = "
                    query {
                        customer(customerAccessToken: \"{$token['accessToken']}\") {
                            id
                            email
                            firstName
                            lastName
                            addresses(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                                edges {
                                    node {
                                        id
                                        address1
                                        address2
                                        city
                                        country
                                        firstName
                                        lastName
                                        phone
                                        province
                                        zip
                                    }
                                }
                            }
                        }
                    }";

                return $this->client
                    ->request($query)
                    ->then(function (array $result) use ($token): Account {
                        if ($result['errors']) {
                            // TODO handle error
                        }

                        $account = $this->mapDataToAccount($result['body']['data']['customer']);
                        $account->authToken = $token['accessToken'];

                        return $account;
                    });
            })
            ->wait();
    }

    public function refreshAccount(Account $account, string $locale = null): Account
    {
        if (is_null($account->authToken)) {
            $account = $this->login($account);
        }

        $query = "
            query {
                customer(customerAccessToken: \"{$account->authToken}\") {
                    id
                    email
                    firstName
                    lastName
                }
            }";

        return $this->client
            ->request($query)
            ->then(function (array $result) use ($account): Account {
                if ($result['errors']) {
                    // TODO handle error
                }

                $fetchedAccount = $this->mapDataToAccount($result['body']['data']['customer']);
                $fetchedAccount->authToken = $account->authToken;

                return $fetchedAccount;
            })
            ->wait();
    }

    public function getAddresses(Account $account, string $locale = null): array
    {
        // TODO: Implement getAddresses() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function addAddress(Account $account, Address $address, string $locale = null): Account
    {
        if (is_null($account->authToken)) {
            $account = $this->login($account);
        }

        $mutation = "
            mutation {
                customerAddressCreate(
                    address: {
                        address1: \"$address->streetName\",
                        address2: \"$address->streetNumber\",
                        city: \"$address->city\",
                        country: \"$address->country\",
                        firstName: \"$address->firstName\",
                        lastName: \"$address->lastName\",
                        phone: \"$address->phone\",
                        province: \"$address->state \",
                        zip: \"$address->postalCode\",
                    },
                    customerAccessToken: \"$account->authToken\"
                ) {
                    customerAddress {
                        id
                        address1
                        address2
                        city
                        country
                        firstName
                        lastName
                        phone
                        province
                        zip
                    }
                    customerUserErrors {
                        code
                        field
                        message
                    }
                }
            }";

        return $this->client
            ->request($mutation, $locale)
            ->then(function ($result) use ($account) : Account {
                if ($result['errors']) {
                    // TODO handle error
                }

                $account->addresses[] = $this->mapDataToAddress(
                    $result['body']['data']['customerAddressCreate']['customerAddress']
                );

                return $account;
            })
            ->wait();

    }

    public function updateAddress(Account $account, Address $address, string $locale = null): Account
    {
        // TODO: Implement updateAddress() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function removeAddress(Account $account, string $addressId, string $locale = null): Account
    {
        // TODO: Implement removeAddress() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function verifyEmail(string $token): Account
    {
        // TODO: Implement verifyEmail() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setDefaultShippingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        // TODO: Implement setDefaultShippingAddress() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function get(string $email): Account
    {
        // TODO: Implement get() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setDefaultBillingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        // TODO: Implement setDefaultBillingAddress() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getDangerousInnerClient()
    {
        // TODO: Implement getDangerousInnerClient() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function mapDataToAccount(array $accountData): Account
    {
        $addresses = [];

        if (!empty($accountData['addresses']['edges'])) {
            $edges = $accountData['addresses']['edges'];
            $addresses = array_map(
                function (array $addressData) : Address {
                    $node = $addressData['node'];
                    return $this->mapDataToAddress($addressData['node']);
                },
                $accountData['addresses']['edges']
            );
        }

        return new Account([
            'accountId' => $accountData['id'],
            'firstName' => $accountData['firstName'],
            'lastName' => $accountData['lastName'],
            'email' => $accountData['email'],
            'addresses' => $addresses,
            'confirmed' => true,
        ]);
    }

    protected function mapDataToAddress(array $addressData): Address
    {
        return new Address([
            'addressId' => $addressData['id'],
            'streetName' => $addressData['address1'],
            'streetNumber' => $addressData['address2'],
            'city' => $addressData['city'],
            'country' => $addressData['country'],
            'firstName' => $addressData['firstName'],
            'lastName' => $addressData['lastName'],
            'phone' => $addressData['phone'],
            'state' => $addressData['province'],
            'postalCode' => $addressData['zip'],
        ]);
    }
}
