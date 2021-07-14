<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;

class AddressesMapper extends AbstractDataMapper implements ProjectConfigApiAwareDataMapperInterface
{
    use ProjectConfigApiAwareDataMapperTrait;

    public const MAPPER_NAME = 'addresses';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\
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
        $addressData = $this->extractElements($resource, $resource);

        $result = [];
        foreach ($addressData as $item) {
            $result[] = $this->getAddressMapper()->map($item);
        }

        return $result;
    }

    private function getAddressMapper(): AddressMapper
    {
        return $this->addressMapper->setProjectConfigApi($this->getProjectConfigApi());
    }
}
