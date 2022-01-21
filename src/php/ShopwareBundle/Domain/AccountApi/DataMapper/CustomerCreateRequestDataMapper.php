<?php

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\SalutationHelper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;

class CustomerCreateRequestDataMapper extends AbstractDataMapper implements ProjectConfigApiAwareDataMapperInterface
{
    use ProjectConfigApiAwareDataMapperTrait;

    public const MAPPER_NAME = 'customer-create-request';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AddressCreateRequestDataMapper
     */
    private $addressCreateRequestDataMapper;

    public function __construct(AddressCreateRequestDataMapper $addressCreateRequestDataMapper)
    {
        $this->addressCreateRequestDataMapper = $addressCreateRequestDataMapper;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    /**
     * @param \Frontastic\Common\AccountApiBundle\Domain\Account $account
     *
     * @return string[]
     */
    public function map($account)
    {
        if ($this->isGuestAccount($account)) {
            $account = $this->generateGuestData($account);
        }

        $requestData = [
            'salutationId' => $this->resolveSalutationId($account->salutation),
            'firstName' => $account->firstName,
            'lastName' => $account->lastName,
            'guest' => $this->isGuestAccount($account),
            'email' => $account->email,
            'password' => $this->isGuestAccount($account) ? substr(md5(microtime()), 2, 8) : $account->getPassword(),
            'birthdayDay' => $account->birthday ? $account->birthday->format('d') : null,
            'birthdayMonth' => $account->birthday ? $account->birthday->format('m') : null,
            'birthdayYear' => $account->birthday ? $account->birthday->format('Y') : null,
            'acceptedDataProtection' => true,
        ];

        $shippingAddresses = null;
        $billingAddresses = null;

        foreach ($account->addresses as $address) {
            $requestAddressData = $this->getAddressCreateRequestDataMapper()->map($address);

            $shippingAddresses = $shippingAddresses ?? $requestAddressData;
            $billingAddresses = $billingAddresses ?? $requestAddressData;

            if ($address->isDefaultShippingAddress) {
                $shippingAddresses = $requestAddressData;
            }

            if ($address->isDefaultBillingAddress) {
                $billingAddresses = $requestAddressData;
            }
        }

        $requestData['shippingAddress'] = $shippingAddresses ?? $billingAddresses;
        $requestData['billingAddress'] = $billingAddresses ?? $shippingAddresses;

        return $requestData;
    }

    private function isGuestAccount(Account $account): bool
    {
        return empty($account->getPassword());
    }

    private function generateGuestData(Account $account): Account
    {
        $defaultName = strstr($account->email, '@', true);

        $account->firstName =$account->firstName ?? $defaultName;
        $account->lastName = $account->lastName ?? $defaultName;
        $account->addresses = !empty($account->addresses) ? $account->addresses : [
            new Address([
                'salutation' => $account->salutation,
                'firstName' => $account->firstName,
                'lastName' => $account->lastName,
                'streetName' => strstr($account->email, '@', true),
                'postalCode' => '1234',
                'country' => 'DE',
                'city' => 'Berlin',
                ]),
        ];

        return $account;
    }

    private function getAddressCreateRequestDataMapper(): AddressCreateRequestDataMapper
    {
        return $this->addressCreateRequestDataMapper->setProjectConfigApi($this->getProjectConfigApi());
    }

    private function resolveSalutationId(?string $frontasticSalutation): ?string
    {
        if ($frontasticSalutation === null) {
            return null;
        }

        $shopwareSalutation = $this->getProjectConfigApi()->getSalutation(
            SalutationHelper::resolveShopwareSalutation($frontasticSalutation)
        );

        return $shopwareSalutation ? $shopwareSalutation->id : null;
    }
}
