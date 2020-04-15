<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AccountMapper;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AddressesMapper;
use Frontastic\Common\ShopwareBundle\Domain\ClientInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\Exception\RequestException;
use RuntimeException;

class ShopwareAccountApi implements AccountApi
{
    private const TOKEN_TYPE = 'shopware';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ClientInterface
     */
    private $client;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver
     */
    private $mapperResolver;

    public function __construct(
        ClientInterface $client,
        DataMapperResolver $mapperResolver
    ) {
        $this->client = $client;
        $this->mapperResolver = $mapperResolver;
    }

    public function get(string $token): Account
    {
        return $this->client
            ->withContextToken($token)
            ->get('/customer')
            ->then(function ($response) {
                return $this->mapResponse($response, AccountMapper::MAPPER_NAME);
            })
            ->then(function (Account $account) use ($token) {
                // Lets not loose the token
                $account->setToken(self::TOKEN_TYPE, $token);

                return $account;
            })
            ->wait();
    }

    public function confirmEmail(string $token): Account
    {
        // Shopware does not have an endpoint to handle this
        throw new RuntimeException('Not implemented');
    }

    public function create(Account $account, ?Cart $cart = null): Account
    {
        $requestData = $this->mapCustomerCreateRequestData($account);

        return $this->client
            ->post('/customer', [], $requestData)
            ->then(static function ($response) use ($account, $requestData) {
                $account->accountId = $response['data'];

                // @TODO: remove
                $account->email = $requestData['email'];

                return $account;
            })
            ->wait();
    }

    public function update(Account $account): Account
    {
        $requestData = $this->mapCustomerPatchRequestData($account);

        $this->client
            ->withContextToken($account->getToken(self::TOKEN_TYPE))
            ->patch('/customer', [], $requestData)
            ->wait();

        return $account;
    }

    public function updatePassword(Account $account, string $oldPassword, string $newPassword): Account
    {
        $requestData = [
            'password' => $oldPassword,
            'newPassword' => $newPassword,
            'newPasswordConfirm' => $newPassword
        ];

        $this->client
            ->withContextToken($account->getToken(self::TOKEN_TYPE))
            ->patch('/customer/password', [], $requestData)
            ->wait();

        return $account;
    }

    public function generatePasswordResetToken(Account $account): Account
    {
        // Shopware does not have an endpoint to handle this
        throw new RuntimeException('Not implemented');
    }

    public function resetPassword(string $token, string $newPassword): Account
    {
        // Shopware does not have an endpoint to handle this
        throw new RuntimeException('Not implemented');
    }

    public function login(Account $account, ?Cart $cart = null): bool
    {
        try {
            return $this->client
                ->post('/customer/login', [], [
                    'username' => $account->getUsername(),
                    'password' => $account->getPassword(),
                ])->then(static function ($response) use (&$account) {
                    $account->setToken(self::TOKEN_TYPE, $response['sw-context-token']);

                    return true;
                }, static function () use ($account) {
                    $account->resetToken(self::TOKEN_TYPE);

                    return false;
                })
                ->wait();
        } catch (RequestException $exception) {
            $account->resetToken(self::TOKEN_TYPE);

            return false;
        }
    }

    public function logout(Account $account): bool
    {
        $this->client
            ->withContextToken($account->getToken(self::TOKEN_TYPE))
            ->post('/customer/logout')
            ->then(static function () use ($account) {
                $account->resetToken(self::TOKEN_TYPE);
            })
            ->wait();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getAddresses(Account $account): array
    {
        return $this->client
            ->withContextToken($account->getToken(self::TOKEN_TYPE))
            ->get('/customer/address')
            ->then(function ($response) {
                return $this->mapResponse($response, AddressesMapper::MAPPER_NAME);
            })
            ->wait();
    }

    public function addAddress(Account $account, Address $address): Account
    {
        $requestData = $this->mapAddressCreateRequestData($address);

        return $this->client
            ->withContextToken($account->getToken(self::TOKEN_TYPE))
            ->post('/customer/address', [], $requestData)
            ->then(static function ($response) use ($account, $address) {
                $account->addresses[] = $address;
                $address->addressId = $response['data'];

                return $address;
            })
            ->then(function (Address $address) use ($account) {
                if ($address->isDefaultBillingAddress === true) {
                    return $this->setDefaultBillingAddress($account, $address->addressId);
                }

                if ($address->isDefaultShippingAddress === true) {
                    return $this->setDefaultShippingAddress($account, $address->addressId);
                }

                return $account;
            })
            ->wait();
    }

    public function updateAddress(Account $account, Address $address): Account
    {
        // TODO: Implement updateAddress() method.
    }

    public function removeAddress(Account $account, string $addressId): Account
    {
        return $this->client
            ->withContextToken($account->getToken(self::TOKEN_TYPE))
            ->delete(sprintf('/customer/address/%s', $addressId))
            ->then(static function ($response) use ($account) {
                $deletedAddressId = $response['data'];

                foreach ($account->addresses as $index => $address) {
                    if ($address->addressId === $deletedAddressId) {
                        unset($account->addresses[$index]);
                        break;
                    }
                }

                // Reindex
                $account->addresses = array_values($account->addresses);

                return $account;
            })
            ->wait();
    }

    public function setDefaultBillingAddress(Account $account, string $addressId): Account
    {
        return $this->client
            ->withContextToken($account->getToken(self::TOKEN_TYPE))
            ->patch(sprintf('/customer/address/%s/default-billing', $addressId))
            ->then(static function ($response) use ($account) {
                $updatedBillingAddressId = $response['data'];

                foreach ($account->addresses as $address) {
                    $address->isDefaultBillingAddress = ($address->addressId === $updatedBillingAddressId);
                }

                return $account;
            })
            ->wait();
    }

    public function setDefaultShippingAddress(Account $account, string $addressId): Account
    {
        return $this->client
            ->withContextToken($account->getToken(self::TOKEN_TYPE))
            ->patch(sprintf('/customer/address/%s/default-shipping', $addressId))
            ->then(static function ($response) use ($account) {
                $updatedShippingAddressId = $response['data'];

                foreach ($account->addresses as $address) {
                    $address->isDefaultShippingAddress = ($address->addressId === $updatedShippingAddressId);
                }

                return $account;
            })
            ->wait();
    }

    /**
     * @inheritDoc
     */
    public function getDangerousInnerClient(): ClientInterface
    {
        return $this->client;
    }

    private function mapAddressCreateRequestData(Address $address): array
    {
        return [
            'id' => $address->addressId ?? null,
            'salutationId' => $address->salutation, // @TODO: resolve salutation id?
            'title' => null, // Not part of Frontastic Address,
            'firstName' => $address->firstName,
            'lastName' => $address->lastName,
            'company' => null, // Not part of Frontastic Address
            'department' => null, // Not part of Frontastic Address
            'vatId' => null, // Not part of Frontastic Address
            'street' => trim(sprintf('%s %s', $address->streetName, $address->streetNumber)),
            'additionalAddressLine1' => $address->additionalAddressInfo ?? '',
            'additionalAddressLine2' => $address->additionalStreetInfo ?? '',
            'zipcode' => $address->postalCode,
            'city' => $address->city,
            'countryId' => $address->country, // TODO: map to country ID
            'countryStateId' => null, // Not part of address
            'phoneNumber' => $address->phone,
        ];
    }

    private function mapCustomerCreateRequestData(Account $account): array
    {
        $requestData = [
            'salutationId' => $account->salutation, // @TODO: resolve salutation id?
            'firstName' => $account->firstName,
            'lastName' => $account->lastName,
            'guest' => $account->isGuest,
            'email' => str_replace('DYNAMIC', time(), $account->email),
            'password' => $account->getPassword(),
            'birthdayDay' => $account->birthday ? $account->birthday->format('d') : null,
            'birthdayMonth' => $account->birthday ? $account->birthday->format('m') : null,
            'birthdayYear' => $account->birthday ? $account->birthday->format('Y') : null,
            'billingAddress' => [
                'company' => $account->data['billingAddress']['company'] ?? '',
                'department' => $account->data['billingAddress']['department'] ?? '',
                'vatId' => $account->data['billingAddress']['vatId'] ?? '',
                'street' => $account->data['billingAddress']['street'],
                'additionalAddressLine1' => $account->data['billingAddress']['additionalAddressLine1'] ?? '',
                'additionalAddressLine2' => $account->data['billingAddress']['additionalAddressLine2'] ?? '',
                'zipcode' => $account->data['billingAddress']['zipCode'],
                'city' => $account->data['billingAddress']['city'],
                'countryId' => $account->data['billingAddress']['country'],
                // @TODO: resolve country id?
                'countryStateId' => $account->data['billingAddress']['countryState'] ?? '',
                'phoneNumber' => $account->data['billingAddress']['phoneNumber'] ?? '',
            ]
        ];

        if (!empty($account->data['shippingAddress'])) {
            // @TODO: map shipping address
        }

        return $requestData;
    }

    private function mapCustomerPatchRequestData(Account $account): array
    {
        return [
            'salutationId' => $account->salutation, // @TODO: resolve salutation id?
            'firstName' => $account->firstName,
            'lastName' => $account->lastName,
            'birthdayDay' => $account->birthday ? $account->birthday->format('d') : null,
            'birthdayMonth' => $account->birthday ? $account->birthday->format('m') : null,
            'birthdayYear' => $account->birthday ? $account->birthday->format('Y') : null,
        ];
    }

    private function mapResponse(array $response, string $mapperName)
    {
        $mapper = $this->mapperResolver->getMapper($mapperName);

        return $mapper->map($response);
    }
}
