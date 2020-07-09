<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\AccountApiBundle\Domain\PasswordResetToken;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\SprykerBundle\BaseApi\SprykerApiBase;
use Frontastic\Common\SprykerBundle\Domain\Account\Mapper\SprykerSalutationMapper;
use Frontastic\Common\SprykerBundle\Domain\Account\Mapper\TokenMapper;
use Frontastic\Common\SprykerBundle\Domain\Account\Mapper\AccountMapper;
use Frontastic\Common\SprykerBundle\Domain\Account\Mapper\AddressMapper;
use Frontastic\Common\SprykerBundle\Domain\Account\Request\AccessTokenRequestData;
use Frontastic\Common\SprykerBundle\Domain\Account\Request\CustomerAddressRequestData;
use Frontastic\Common\SprykerBundle\Domain\Account\Request\CustomerRequestData;
use Frontastic\Common\SprykerBundle\Domain\Account\Request\CustomerPasswordRequestData;
use Frontastic\Common\SprykerBundle\Domain\Account\Request\ForgotPasswordRequestData;
use Frontastic\Common\SprykerBundle\Domain\Account\Request\RestorePasswordRequestData;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;
use Frontastic\Common\SprykerBundle\Domain\SprykerSalutation;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class SprykerAccountApi extends SprykerApiBase implements AccountApi
{
    /**
     * @var AccountHelper
     */
    protected $accountHelper;

    /**
     * @var TokenDecoder
     */
    protected $tokenDecoder;

    /**
     * @param \Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface $client
     * @param \Frontastic\Common\SprykerBundle\Domain\MapperResolver $mapper
     * @param \Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper $accountHelper
     * @param \Frontastic\Common\SprykerBundle\Domain\Account\TokenDecoder $tokenDecoder
     * @param LocaleCreator $localeCreator
     */
    public function __construct(
        SprykerClientInterface $client,
        MapperResolver $mapper,
        AccountHelper $accountHelper,
        TokenDecoder $tokenDecoder,
        LocaleCreator $localeCreator
    ) {
        parent::__construct($client, $mapper, $localeCreator);
        $this->accountHelper = $accountHelper;
        $this->tokenDecoder = $tokenDecoder;
    }

    /**
     * Confirm email from: /account/confirm/{token}
     *
     * @param string $token
     * @param string|null $locale
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function confirmEmail(string $token, string $locale = null): Account
    {
        // TODO: Implement confirmEmail() method.
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param Account $account
     * @param Cart|null $cart
     * @param string|null $locale
     * @return Account
     */
    public function create(Account $account, ?Cart $cart = null, string $locale = null): Account
    {
        $request = CustomerRequestData::createFromAccount($account);

//        $headers = $cart ? $this->accountHelper->getAnonymousHeader() : [];
        $headers = [];

        $response = $this->client->post('/customers', $headers, $request->encode());

        return $this->mapAccount($response->document()->primaryResource());
    }

    /**
     * @param string $token
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function verifyEmail(string $token): Account
    {
        // @TODO: To be migrated form Prym
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param \Frontastic\Common\AccountApiBundle\Domain\Account $account
     * @param string|null $locale
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function update(Account $account, string $locale = null): Account
    {
        $request = CustomerRequestData::createFromAccount($account);

        $response = $this->client->patch(
            sprintf('/customers/%s', $account->accountId),
//            $this->getAuthHeader(),
            $this->getAuthHeader($account->authToken),
            $request->encode()
        );

//        return $this->getAccountFromToken();
        return $this->mapAccount($response->document()->primaryResource());
    }

    /**
     * @param Account $account
     * @param string $oldPassword
     * @param string $newPassword
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function updatePassword(
        Account $account,
        string $oldPassword,
        string $newPassword,
        string $locale = null
    ): Account
    {
        $request = new CustomerPasswordRequestData($oldPassword, $newPassword, $newPassword);

        $response = $this->client->patch(
            sprintf('/customer-password/%s', $account->accountId),
            $this->getAuthHeader($account->authToken),
            $request->encode()
        );

//        return $this->getAccountFromToken();
        return $this->mapAccount($response->document()->primaryResource());
    }

    /**
     * @param string $account
     * @return PasswordResetToken
     */
    public function generatePasswordResetToken(string $email): PasswordResetToken
    {
        $request = new ForgotPasswordRequestData($email);

        $this->client->post('/customer-forgotten-password', [], $request->encode());

        // @TODO: Implement generatePasswordResetToken() method
        return new PasswordResetToken();
    }

    /**
     * Request password change from: /account/forgotPassword/{token}
     *
     * @param string $token
     * @param string $newPassword
     * @param string|null $locale
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function resetPassword(string $token, string $newPassword, string $locale = null): Account
    {
        $request = new RestorePasswordRequestData($token, $newPassword, $newPassword);

        $response = $this->client->patch("/customer-restore-password/{$token}", [], $request->encode());

        $accessToken = $this->mapResource($response->document()->primaryResource(), TokenMapper::MAPPER_NAME);

        $this->accountHelper->getAccount()->setToken(AccountHelper::TOKEN_TYPE, $accessToken);
        $this->accountHelper->getAccount()->authToken($token);

        return $this->getAccountFromToken();
    }

    /**
     * @param Account $account
     * @param Cart|null $cart
     * @param string|null $locale
     * @return Account|null
     */
    public function login(Account $account, ?Cart $cart = null, string $locale = null): ?Account
    {
        $request = new AccessTokenRequestData($account->email, $account->getPassword());

//        $headers = $cart ? $this->accountHelper->getAnonymousHeader() : [];
        $headers = [];

        $response = $this->client->post('/access-tokens', $headers, $request->encode());

        $token = $this->mapResource($response->document()->primaryResource(), TokenMapper::MAPPER_NAME);

//        $account->setToken(AccountHelper::TOKEN_TYPE, $token);
        $account->authToken($token);

        return $account;
    }

    /**
     * @param Account $account
     * @param string|null $locale
     * @return \Frontastic\Common\AccountApiBundle\Domain\Address[]
     */
    public function getAddresses(Account $account, string $locale = null): array
    {
        $response = $this->client->get(
            sprintf('/customers/%s/addresses', $account->accountId),
            $this->getAuthHeader($account->authToken)
        );

        return $this->mapAddressArray($response->document()->primaryResources());
    }

    /**
     * @param Account $account
     * @param \Frontastic\Common\AccountApiBundle\Domain\Address $address
     * @param string|null $locale
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function addAddress(Account $account, Address $address, string $locale = null): Account
    {
        $request = CustomerAddressRequestData::createFromAddress($address);

        $response = $this->client->post(
            sprintf('/customers/%s/addresses', $account->accountId),
            $this->getAuthHeader($account->authToken),
            $request->encode()
        );

//        return $this->getAccountFromToken();
        return $this->mapAccount($response->document()->primaryResource());
    }

    /**
     * @param Account $account
     * @param \Frontastic\Common\AccountApiBundle\Domain\Address $address
     * @param string|null $locale
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function updateAddress(Account $account, Address $address, string $locale = null): Account
    {
        $request = CustomerAddressRequestData::createFromAddress($address);

        $response = $this->client->patch(
            sprintf('/customers/%s/addresses/%s', $account->accountId, $address->addressId),
//            $this->getAuthHeader(),
            $this->accountHelper->getAuthHeader($account->authToken),
            $request->encode()
        );

//        return $this->getAccountFromToken();
        return $this->mapAccount($response->document()->primaryResource());
    }

    /**
     * @param Account $account
     * @param string $addressId
     * @param string|null $locale
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function removeAddress(Account $account, string $addressId, string $locale = null): Account
    {
        $this->client->delete(
            sprintf('/customers/%s/addresses/%s', $account->accountId, $addressId),
//            $this->getAuthHeader()
            $this->accountHelper->getAuthHeader($account->authToken)
        );

//        return $this->getAccountFromToken();
        return $account;
    }

    /**
     * @param Account $account
     * @param string $addressId
     * @param string|null $locale
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function setDefaultBillingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        $response = $this->client->get(
            sprintf('/customers/%s/addresses/%s', $account->accountId, $addressId),
//            $this->getAuthHeader()
            $this->accountHelper->getAuthHeader($account->authToken)
        );

        $address = $this->mapAddress($response->document()->primaryResource());
        $address->isDefaultBillingAddress = true;

        return $this->updateAddress($account, $address);

//        return $this->getAccountFromToken();
    }

    /**
     * @param Account $account
     * @param string $addressId
     * @param string|null $locale
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    public function setDefaultShippingAddress(Account $account, string $addressId, string $locale = null): Account
    {
        $response = $this->client->get(
            sprintf('/customers/%s/addresses/%s', $account->accountId, $addressId),
//            $this->getAuthHeader()
            $this->accountHelper->getAuthHeader($account->authToken)
        );

        $address = $this->mapAddress($response->document()->primaryResource());
        $address->isDefaultShippingAddress = true;

        return $this->updateAddress($account, $address);

//        return $this->getAccountFromToken();
    }

    public function getSalutations(string $locale): ?array
    {
        $response = $this->client->get('/salutations');

        $salutations = $this->mapResponseResource($response, SprykerSalutationMapper::MAPPER_NAME);

        return array_map(
            function (SprykerSalutation $salutation): string {
                return $salutation->label;
            },
            $salutations
        );
    }

    public function refreshAccount(Account $account, string $locale = null): Account
    {
        // TODO: Implement refreshAccount() method.
        throw new \RuntimeException('refreshAccount() is not implemented');
    }

    /**
     * @return SprykerClientInterface
     */
    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    /**
     * @param string $token
     * @return array
     */
    protected function getAuthHeader(string $token): array
    {
//        return $this->accountHelper->getAuthHeader($token);
        return ['Authorization' => sprintf('Bearer %s', $token)];
    }

    /**
     * @return Account
     */
    private function getAccountFromToken(): Account
    {
        return $this->get($this->accountHelper->getAccount()->getToken(AccountHelper::TOKEN_TYPE));
    }

    /**
     * @param ResourceObject $resource
     * @return Address
     */
    private function mapAddress(ResourceObject $resource): Address
    {
        return $this->mapResource($resource, AddressMapper::MAPPER_NAME);
    }

    /**
     * @param ResourceObject[] $primaryResources
     * @return Address[]
     */
    private function mapAddressArray(array $primaryResources): array
    {
        return $this->mapperResolver->getExtendedMapper(AddressMapper::MAPPER_NAME)->mapResourceArray($primaryResources);
    }

    /**
     * @param ResourceObject $resource
     * @return Account
     */
    private function mapAccount(ResourceObject $resource): Account
    {
        return $this->mapResource($resource, AccountMapper::MAPPER_NAME);
    }

    /**
     * @param ResourceObject $resource
     * @param string $mapperName
     * @return mixed
     */
    private function mapResource(ResourceObject $resource, string $mapperName)
    {
        return $this->mapperResolver->getMapper($mapperName)->mapResource($resource);
    }
}
