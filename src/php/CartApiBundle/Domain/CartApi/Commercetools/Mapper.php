<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Discount;
use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;

class Mapper
{
    public function mapDataToAddress(array $address): ?Address
    {
        if (!count($address)) {
            return null;
        }

        return new Address([
            'addressId' => $address['id'] ?? null,
            'salutation' => $address['salutation'] ?? null,
            'firstName' => $address['firstName'] ?? null,
            'lastName' => $address['lastName'] ?? null,
            'streetName' => $address['streetName'] ?? null,
            'streetNumber' => $address['streetNumber'] ?? null,
            'additionalStreetInfo' => $address['additionalStreetInfo'] ?? null,
            'additionalAddressInfo' => $address['additionalAddressInfo'] ?? null,
            'postalCode' => $address['postalCode'] ?? null,
            'city' => $address['city'] ?? null,
            'country' => $address['country'] ?? null,
            'phone' => $address['phone'] ?? null,
        ]);
    }

    public function mapAddressToData(Address $address): array
    {
        return [
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
            'phone' => $address->phone,
        ];
    }

    public function mapDataToDiscounts(array $cart): array
    {
        if (empty($cart['discountCodes'])) {
            return [];
        }

        $discounts = [];
        foreach ($cart['discountCodes'] as $discount) {
            // Get the state from the $discount and save it in $discountCodeState variable
            // before assigning $discount['discountCode'] to $discount.
            $discountCodeState = $discount['state'] ?? null;
            $discount = $discount['discountCode'] ?? [];
            $discount = isset($discount['obj']) ? $discount['obj'] : $discount;
            $discounts[] = new Discount([
                'discountId' => $discount['id'] ?? 'undefined',
                'name' => $discount['name'] ?? null,
                'code' => $discount['code'] ?? null,
                'description' => $discount['description'] ?? null,
                'state' => $discountCodeState,
                'dangerousInnerDiscount' => $discount,
            ]);
        }

        return $discounts;
    }

    public function mapDataToShippingMethod(array $shipping): ?ShippingMethod
    {
        if (!count($shipping)) {
            return null;
        }

        return new ShippingMethod([
            'name' => $shipping['shippingMethodName'] ?? null,
            'price' => $shipping['price']['centAmount'] ?? null,
        ]);
    }
}
