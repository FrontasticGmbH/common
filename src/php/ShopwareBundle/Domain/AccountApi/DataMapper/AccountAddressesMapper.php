<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;

class AccountAddressesMapper extends AbstractDataMapper implements ProjectConfigApiAwareDataMapperInterface
{
    use ProjectConfigApiAwareDataMapperTrait;

    public const MAPPER_NAME = 'account-addresses';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AddressMapper
     */
    private $addressMapper;

    public function __construct(AddressMapper $addressMapper)
    {
        $this->addressMapper = $addressMapper;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $accountData = $this->extractData($resource, $resource);

        $addresses = [];
        $billingAddress = [];
        $shippingAddress = [];

        $defaultBillingAddressId = $accountData['defaultBillingAddressId'] ?? null;
        $defaultShippingAddressId = $accountData['defaultShippingAddressId'] ?? null;

        $addressesData = $accountData['addresses'] ?? [];

        foreach ($addressesData as $addressData) {
            $address = $this->getAddressMapper()->map($addressData);
            $addresses[$address->addressId] = $address;
        }

        if (key_exists('defaultBillingAddress', $accountData) && !empty($accountData['defaultBillingAddress'])) {
            $billingAddress = $this->getAddressMapper()->map($accountData['defaultBillingAddress']);
            $billingAddress->isDefaultBillingAddress = true;

            $addresses[$billingAddress->addressId] = $billingAddress;
        }

        if (key_exists('defaultShippingAddress', $accountData) && !empty($accountData['defaultShippingAddress'])) {
            $shippingAddress = $this->getAddressMapper()->map($accountData['defaultShippingAddress']);
            $shippingAddress->isDefaultShippingAddress = true;

            $addresses[$shippingAddress->addressId] = $shippingAddress;
        }

        if (!empty($billingAddress)) {
            $billingAddress->isDefaultShippingAddress = empty($shippingAddress);
        }

        if (!empty($shippingAddress)) {
            $billingAddress->isDefaultBillingAddress = empty($billingAddress);
        }

        if (!empty($defaultBillingAddressId) &&
            key_exists($defaultBillingAddressId, $addresses)
        ) {
            $addresses[$defaultBillingAddressId]->isDefaultBillingAddress = true;
        }

        if (!empty($defaultShippingAddressId) &&
            key_exists($defaultShippingAddressId, $addresses)
        ) {
            $addresses[$defaultShippingAddressId]->isDefaultShippingAddress = true;
        }

        return array_values($addresses);
    }

    private function getAddressMapper(): AddressMapper
    {
        return $this->addressMapper->setProjectConfigApi($this->getProjectConfigApi());
    }
}
