<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\AccountApiBundle\Domain\PasswordResetToken;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CoreBundle\Domain\Json\Json;
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
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchCriteriaBuilder;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareSalutation;

class ShopwareAccountApi extends AbstractShopwareApi implements AccountApi
{
    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiInterface
     */
    private $projectConfigApi;

    public function __construct(
        ClientInterface $client,
        LocaleCreator $localeCreator,
        DataMapperResolver $mapperResolver,
        ShopwareProjectConfigApiFactory $projectConfigApiFactory,
        ?string $defaultLanguage = null
    ) {
        parent::__construct($client, $mapperResolver, $localeCreator, $defaultLanguage);

        $this->projectConfigApi = $projectConfigApiFactory->factor(
            $this->client,
            $this->localeCreator,
            $defaultLanguage
        );
    }

    public function getSalutations(string $locale): ?array
    {
        $shopwareSalutations = $this->projectConfigApi->getSalutations(null, $locale);

        return array_map(
            function (ShopwareSalutation $shopwareSalutation): string {
                return $shopwareSalutation->displayName;
            },
            $shopwareSalutations
        );
    }

    public function confirmEmail(string $token, string $locale = null): Account
    {
        try {
            $requestData = Json::decode($token, true);

            return $this->client
                ->post('/store-api/v3/account/register-confirm', [], $requestData)
                ->then(function ($response) {
                    /** @var Account $account */
                    $account = $this->mapResponse($response, AccountMapper::MAPPER_NAME);
                    $account->confirmed = true;
                    $account->confirmationToken = null;
                    $account->authToken = isset($response['headers']['sw-context-token']) ?
                        explode(',', $response['headers']['sw-context-token'])[0] :
                        null;

                    return $account;
                })
                ->wait();
        } catch (RequestException $e) {
            throw new \OutOfBoundsException('Could not find account with confirmation token ' . $token, 0, $e);
        }
    }

    public function create(Account $account, ?Cart $cart = null, string $locale = null): Account
    {
        $requestData = $this->mapRequestData($account, CustomerCreateRequestDataMapper::MAPPER_NAME);

        return $this->client
            ->post('/sales-channel-api/v2/customer', [], $requestData)
            ->then(function ($response) use ($account) {
                // If the "Double opt-in on sign-up" option is not enabled in Shopware, login will not be possible
                // until the customer emails is been confirmed through AccountApi::confirmEmail
                $createdAccount = $this->login($account);
                if ($createdAccount instanceof Account) {
                    return $createdAccount;
                }

                return $this->client
                    ->get("/api/v3/customer/{$response['data']}", [], [$this->client->getAccessTokenHeader()])
                    ->then(function ($response): Account {
                        return $this->mapResponse($response, AccountMapper::MAPPER_NAME);
                    })
                    ->wait();
            })
            ->wait();
    }

    public function update(Account $account, string $locale = null): Account
    {
        $requestData = $this->mapRequestData($account, CustomerPatchRequestDataMapper::MAPPER_NAME);

        $this->client
            ->withContextToken($account->authToken)
            ->patch('/sales-channel-api/v2/customer', [], $requestData)
            ->wait();

        return $account;
    }

    public function updatePassword(
        Account $account,
        string $oldPassword,
        string $newPassword,
        string $locale = null
    ): Account {
        $requestData = [
            'password' => $oldPassword,
            'newPassword' => $newPassword,
            'newPasswordConfirm' => $newPassword,
        ];

        $this->client
            ->withContextToken($account->authToken)
            ->patch('/sales-channel-api/v2/customer/password', [], $requestData)
            ->wait();

        return $account;
    }

    public function generatePasswordResetToken(string $email, string $locale = null): PasswordResetToken
    {
        $requestData = array_merge(
            [
                'email' => $email,
                'storefrontUrl' => 'http://localhost',
            ]
        );

        return $this->client
            ->post('/store-api/v3/account/recovery-password', [], $requestData)
            ->then(function ($response) use ($email) {
                $criteria = SearchCriteriaBuilder::buildFromEmail($email);
                $criteria = array_merge($criteria, [
                    'associations' => [
                        "recoveryCustomer" => []
                    ]
                ]);

                return $this->client
                    ->post("/api/v3/search/customer", [$this->client->getAccessTokenHeader()], $criteria)
                    ->then(function ($response) use ($email): PasswordResetToken {
                        return new PasswordResetToken([
                            'email' => $email,
                            'confirmationToken' => Json::encode(
                                [
                                    'hash' => $response['included'][0]['attributes']['hash']
                                ]
                            )
                        ]);
                    });
            })
            ->wait();
    }

    public function resetPassword(string $token, string $newPassword, string $locale = null): Account
    {
        $requestData = array_merge(
            Json::decode($token, true),
            [
                'newPassword' => $newPassword,
                'newPasswordConfirm' => $newPassword,
            ]
        );

        return $this->client
            ->post('/store-api/v3/account/recovery-password-confirm', [], $requestData)
            ->then(function ($response): Account {
                return new Account();
            })
            ->wait();
    }

    public function login(Account $account, ?Cart $cart = null, string $locale = null): ?Account
    {
        $requestData = [
            'username' => $account->getUsername(),
            'password' => $account->getPassword(),
        ];

        return $this->client
            ->post('/sales-channel-api/v2/customer/login', [], $requestData)
            ->then(
                function ($response) {
                    $token = $response['sw-context-token'];
                    return $this->client
                        ->withContextToken($token)
                        ->get('/sales-channel-api/v2/customer')
                        ->then(function ($response) use ($token): Account {
                            $account = $this->mapResponse($response, AccountMapper::MAPPER_NAME);
                            $account->authToken = $token;
                            return $account;
                        });
                },
                static function () {
                    return null;
                }
            )
            ->wait();
    }

    public function refreshAccount(Account $account, string $locale = null): Account
    {
        $criteria = SearchCriteriaBuilder::buildFromEmail($account->email);

        return $this->client
            ->post("/api/v3/search/customer", [$this->client->getAccessTokenHeader()], $criteria)
            ->then(function ($response): Account {
                return $this->mapResponse($response['data'][0], AccountMapper::MAPPER_NAME);
            })
            ->wait();
    }

    /**
     * @inheritDoc
     */
    public function getAddresses(Account $account, string $locale = null): array
    {
        return $this->client
            ->withContextToken($account->authToken)
            ->get('/sales-channel-api/v2/customer/address')
            ->then(function ($response) {
                return $this->mapResponse($response, AddressesMapper::MAPPER_NAME);
            })
            ->wait();
    }

    public function addAddress(Account $account, Address $address, string $locale = null): Account
    {
        $requestData = $this->mapRequestData($address, AddressCreateRequestDataMapper::MAPPER_NAME);

        return $this->client
            ->withContextToken($account->authToken)
            ->post('/sales-channel-api/v2/customer/address', [], $requestData)
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

    public function updateAddress(Account $account, Address $address, string $locale = null): Account
    {
        $requestData = $this->mapRequestData($address, AddressCreateRequestDataMapper::MAPPER_NAME);

        return $this->client
            ->withContextToken($account->authToken)
            ->patch('/sales-channel-api/v2/customer/address', [], $requestData)
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

    public function removeAddress(Account $account, string $addressId, string $locale = null): Account
    {
        return $this->client
            ->withContextToken($account->authToken)
            ->delete("/sales-channel-api/v2/customer/address/{$addressId}")
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

    public function setDefaultBillingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        return $this->client
            ->withContextToken($account->authToken)
            ->patch("/sales-channel-api/v2/customer/address/{$addressId}/default-billing")
            ->then(static function ($response) use ($account) {
                $updatedBillingAddressId = $response['data'];

                foreach ($account->addresses as $address) {
                    $address->isDefaultBillingAddress = ($address->addressId === $updatedBillingAddressId);
                }

                return $account;
            })
            ->wait();
    }

    public function setDefaultShippingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        return $this->client
            ->withContextToken($account->authToken)
            ->patch("/sales-channel-api/v2/customer/address/{$addressId}/default-shipping")
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
        parent::configureMapper($mapper);

        if ($mapper instanceof ProjectConfigApiAwareDataMapperInterface) {
            $mapper->setProjectConfigApi($this->projectConfigApi);
        }
    }
}
