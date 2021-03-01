<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi\Commercetools;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Ramsey\Uuid\Uuid;

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

    public function mapAddressesToData(Account $account): array
    {
        $addressesData = null;
        $defaultBillingAddress = null;
        $defaultShippingAddress = null;
        $billingAddresses = null;
        $shippingAddresses = null;

        foreach ($account->addresses as $index => $address) {
            $addressData = $this->mapAddressToData($address);
            unset($addressData['id']);
            if ($address->isDefaultBillingAddress || $address->isDefaultShippingAddress) {
                if (($addressData['key'] ?? null) === null) {
                    $addressData['key'] = Uuid::uuid4()->toString();
                }
            }

            if ($address->isDefaultBillingAddress) {
                $defaultBillingAddress = $defaultBillingAddress ?? $index;
                $billingAddresses[] = $index;
            }

            if ($address->isDefaultShippingAddress) {
                $defaultShippingAddress = $defaultShippingAddress ?? $index;
                $shippingAddresses[] = $index;
            }

            $addressesData[] = $addressData;
        }

        return [
            $addressesData,
            $defaultBillingAddress,
            $defaultShippingAddress,
            $billingAddresses,
            $shippingAddresses
        ];
    }
}
