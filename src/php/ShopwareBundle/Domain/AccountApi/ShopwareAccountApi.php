<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\ShopwareBundle\Domain\AbstractShopwareApi;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AccountMapper;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AddressCreateRequestDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AddressesMapper;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\CustomerCreateRequestDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\CustomerPatchRequestDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\ClientInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\Exception\RequestException;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory;
use RuntimeException;

class ShopwareAccountApi extends AbstractShopwareApi implements AccountApi
{
    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiInterface
     */
    private $projectConfigApi;

    public function __construct(
        ClientInterface $client,
        DataMapperResolver $mapperResolver,
        ShopwareProjectConfigApiFactory $projectConfigApiFactory
    ) {
        parent::__construct($client, $mapperResolver);

        $this->projectConfigApi = $projectConfigApiFactory->factor($this->client);
    }

    public function get(string $token): Account
    {
        return $this->client
            ->withContextToken($token)
            ->get('/customer')
            ->then(function ($response) {
                return $this->mapResponse($response, AccountMapper::MAPPER_NAME);
            })
            ->then(static function (Account $account) use ($token) {
                // Lets not loose the token
                $account->setToken(self::TOKEN_TYPE, $token);

                return $account;
            })
            ->wait();
    }

    public function confirmEmail(string $token): Account
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException('Not implemented');
    }

    public function create(Account $account, ?Cart $cart = null): Account
    {
        $requestData = $this->mapRequestData($account, CustomerCreateRequestDataMapper::MAPPER_NAME);

        return $this->client
            ->post('/customer', [], $requestData)
            ->then(static function ($response) use ($account) {
                $account->accountId = $response['data'];

                // Mark billing address as default, Shopware marks it as default when creating the account
                $account->addresses[0]->isDefaultBillingAddress = true;

                // Shipping address can be located at index 1 if it's custom, or 0 if it's same as billing
                $shippingAddressIndex = (int)isset($account->addresses[1]);
                $account->addresses[$shippingAddressIndex]->isDefaultShippingAddress = true;

                return $account;
            })
            ->wait();
    }

    public function update(Account $account): Account
    {
        $requestData = $this->mapRequestData($account, CustomerPatchRequestDataMapper::MAPPER_NAME);

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
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException('Not implemented');
    }

    public function resetPassword(string $token, string $newPassword): Account
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException('Not implemented');
    }

    public function login(Account $account, ?Cart $cart = null): bool
    {
        try {
            $requestData = [
                'username' => $account->getUsername(),
                'password' => $account->getPassword(),
            ];

            return $this->client
                ->post('/customer/login', [], $requestData)
                ->then(static function ($response) use (&$account) {
                    $account->setToken(self::TOKEN_TYPE, $response[self::KEY_CONTEXT_TOKEN]);

                    return true;
                }, static function ($reason) use ($account) {
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
        $requestData = $this->mapRequestData($address, AddressCreateRequestDataMapper::MAPPER_NAME);

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
        $requestData = $this->mapRequestData($address, AddressCreateRequestDataMapper::MAPPER_NAME);

        return $this->client
            ->withContextToken($account->getToken(self::TOKEN_TYPE))
            ->patch('/customer/address', [], $requestData)
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

    public function removeAddress(Account $account, string $addressId): Account
    {
        return $this->client
            ->withContextToken($account->getToken(self::TOKEN_TYPE))
            ->delete("/customer/address/{$addressId}")
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
            ->patch("/customer/address/{$addressId}/default-billing")
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
            ->patch("/customer/address/{$addressId}/default-shipping")
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

    protected function configureMapper(DataMapperInterface $mapper): void
    {
        if ($mapper instanceof ProjectConfigApiAwareDataMapperInterface) {
            $mapper->setProjectConfigApi($this->projectConfigApi);
        }
    }
}
