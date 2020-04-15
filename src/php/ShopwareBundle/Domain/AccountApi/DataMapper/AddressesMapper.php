<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;

class AddressesMapper extends AbstractDataMapper
{
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

    public function map(array $resource)
    {
        $addressData = $this->extractData($resource);

        $result = [];
        foreach ($addressData as $item) {
            $result[] = $this->addressMapper->map($item);
        }

        return $result;
    }
}
