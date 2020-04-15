<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;

class AddressMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'address';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $addressData)
    {
        return new Address([
            'addressId' => $addressData['id'] ?? null,
            // @TODO: map salutation to frontastic, inherit from parent?
            'salutation' => $addressData['salutationId'] ?? null,
            'firstName' => $addressData['firstName'] ?? null,
            'lastName' => $addressData['lastName'] ?? null,
            'streetName' => $addressData['street'] ?? null,
            'additionalAddressInfo' => $addressData['additionalAddressLine1'] ?? null,
            'additionalStreetInfo' => $addressData['additionalAddressLine2'] ?? null,
            'postalCode' => $addressData['zipcode'] ?? null,
            'city' => $addressData['city'] ?? null,
            // @TODO: resolve country name by id
            'country' => $addressData['country']['translated']['name'] ?? $addressData['country']['name'] ?? $addressData['countryId'] ?? null,
            'phone' => $addressData['phoneNumber'] ?? null,
            'dangerousInnerAddress' => $addressData,
        ]);
    }
}
