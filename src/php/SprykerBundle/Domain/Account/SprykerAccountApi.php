<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\AccountApiBundle\Domain\DuplicateAccountException;
use Frontastic\Common\AccountApiBundle\Domain\PasswordResetToken;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\SprykerBundle\BaseApi\SprykerApiBase;
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

        $headers = $this->accountHelper->getAnonymousHeader();

        try {
            $this->client->post('/customers', $headers, $request->encode());

            $account = $this->login($account, $cart, $locale);
        } catch (\Exception $e) {
            if ($e->getCode() === 422) {
                throw new DuplicateAccountException($account->email, 0);
            }
            throw $e;
        }

        return $account;
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
            $this->getAuthHeader(),
            $request->encode()
        );

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
            $this->getAuthHeader(),
            $request->encode()
        );

        return $this->mapAccount($response->document()->primaryResource());
    }

    /**
     * @param string $email
     * @return PasswordResetToken
     */
    public function generatePasswordResetToken(string $email): PasswordResetToken
    {
        $request = new ForgotPasswordRequestData($email);

        $response = $this->client->post('/customer-forgotten-password', [], $request->encode());

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

        $account = $this->accountHelper->getAccount();
        $account->authToken = $accessToken;

        return $account;
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

        $headers = $cart ? $this->accountHelper->getAnonymousHeader() : [];

        try {
            $response = $this->client->post('/access-tokens', $headers, $request->encode());

            $token = $this->mapResource($response->document()->primaryResource(), TokenMapper::MAPPER_NAME);
        } catch (\Exception $e) {
            return null;
        }

        $account->authToken = $token;

        return $this->refreshAccount($account, $locale);
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
            $this->getAuthHeader()
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
            $this->getAuthHeader(),
            $request->encode()
        );

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
            $this->getAuthHeader(),
            $request->encode()
        );

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
            $this->getAuthHeader()
        );

        return $this->refreshAccount($account, $locale);
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
            $this->getAuthHeader()
        );

        $address = $this->mapAddress($response->document()->primaryResource());
        $address->isDefaultBillingAddress = true;

        return $this->updateAddress($account, $address);
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
            $this->getAuthHeader()
        );

        $address = $this->mapAddress($response->document()->primaryResource());
        $address->isDefaultShippingAddress = true;

        return $this->updateAddress($account, $address);
    }

    public function getSalutations(string $locale): ?array
    {
        // TODO: Implement salutations strategy
        return [SalutationHelper::DEFAULT_SPRYKER_SALUTATION];
    }

    public function refreshAccount(Account $account, string $locale = null): Account
    {
        $authToken = $account->authToken;

        if ($authToken === null) {
            throw new \OutOfBoundsException('Could not refresh account');
        }

        $id = $this->getCustomerReference($authToken);

        $response = $this->client->get(
            $this->withIncludes("/customers/{$id}", ['addresses']),
            $this->accountHelper->getAutoHeader($id, $authToken)
        );

        $account = $this->mapAccount($response->document()->primaryResource());
        $account->authToken = $authToken;

        return $account;
    }

    /**
     * @return SprykerClientInterface
     */
    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    /**
     * @return array
     */
    protected function getAuthHeader(): array
    {
        return $this->accountHelper->getAuthHeader();
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

    /**
     * @param string $token
     * @return string
     */
    private function getCustomerReference(string $token): string
    {
        $data = $this->tokenDecoder->decode($token);

        $sub = json_decode($data['sub'], true);

        return $sub['customer_reference'];
    }
}
