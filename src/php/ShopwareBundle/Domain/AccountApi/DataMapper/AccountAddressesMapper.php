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

        $billingAddress = $this->getAddressMapper()->map($accountData['defaultBillingAddress']);
        $billingAddress->isDefaultBillingAddress = true;

        $addresses[$billingAddress->addressId] = $billingAddress;

        if (!array_key_exists($accountData['defaultShippingAddress']['id'], $addresses)) {
            $shippingAddress = $this->getAddressMapper()->map($accountData['defaultShippingAddress']);
            $shippingAddress->isDefaultShippingAddress = true;

            $addresses[$shippingAddress->addressId] = $shippingAddress;
        } else {
            $billingAddress->isDefaultShippingAddress = true;
        }

        return array_values($addresses);
    }

    private function getAddressMapper(): AddressMapper
    {
        return $this->addressMapper->setProjectConfigApi($this->getProjectConfigApi());
    }
}
