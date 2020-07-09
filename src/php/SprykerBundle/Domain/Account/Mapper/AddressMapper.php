<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account\Mapper;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\SprykerBundle\Domain\Account\SalutationHelper;
use Frontastic\Common\SprykerBundle\Domain\ExtendedMapperInterface;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class AddressMapper implements ExtendedMapperInterface
{
    public const MAPPER_NAME = 'address';

    /**
     * @param ResourceObject $resource
     * @return Address|mixed
     */
    public function mapResource(ResourceObject $resource)
    {
        $address = new Address();

        return $this->mapAddressData($address, $resource);
    }

    /**
     * @param Address|mixed $address
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject $resource
     *
     * @return Address|mixed
     */
    protected function mapAddressData($address, ResourceObject $resource)
    {
        $address->addressId = $resource->id();
        $address->firstName = $resource->attribute('firstName');
        $address->lastName = $resource->attribute('lastName');
        $address->city = $resource->attribute('city');
        $address->postalCode = $resource->attribute('zipCode');
        $address->country = $resource->attribute('country');
        $address->isDefaultBillingAddress = $resource->attribute('isDefaultBilling');
        $address->isDefaultShippingAddress = $resource->attribute('isDefaultShipping');
        $address->streetName = $resource->attribute('address1');
        $address->streetNumber = $resource->attribute('address2');
        $address->additionalStreetInfo = $resource->attribute('address3');
        $address->salutation = SalutationHelper::getFrontasticSalutation($resource->attribute('salutation'));
        $address->dangerousInnerAddress = $resource->attributes();

        return $address;
    }

    /**
     * @param ResourceObject[] $resources
     * @return array
     */
    public function mapResourceArray(array $resources): array
    {
        $array = [];

        foreach ($resources as $primaryResource) {
            $array[] = $this->mapResource($primaryResource);
        }

        return $array;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::MAPPER_NAME;
    }
}
