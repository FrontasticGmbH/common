<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi\Commercetools;

use Frontastic\Common\AccountApiBundle\Domain\Address;

class Mapper
{
    public function mapAddressToData(Address $address): array
    {
        return array_merge(
            (array)$address->rawApiInput,
            [
                'id' => $address->addressId,
                'salutation' => $address->salutation,
                'firstName' => $address->firstName,
                'lastName' => $address->lastName,
                'streetName' => $address->streetName,
                'streetNumber' => $address->streetNumber,
                'additionalStreetInfo' => $address->additionalStreetInfo,
                'additionalAddressInfo' => $address->additionalAddressInfo,
                'postalCode' => $address->postalCode,
                'city' => $address->city,
                'country' => $address->country,
                'state' => $address->state,
                'phone' => $address->phone,
            ]
        );
    }
}
