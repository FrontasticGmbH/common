<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Discount;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;

class Mapper
{
    public function mapDataToAddress(array $addressData): ?Address
    {
        if (!count($addressData)) {
            return null;
        }

        return new Address([
            'addressId' => $addressData['id'] ?? null,
            'salutation' => $addressData['salutation'] ?? null,
            'firstName' => $addressData['firstName'] ?? null,
            'lastName' => $addressData['lastName'] ?? null,
            'streetName' => $addressData['streetName'] ?? null,
            'streetNumber' => $addressData['streetNumber'] ?? null,
            'additionalStreetInfo' => $addressData['additionalStreetInfo'] ?? null,
            'additionalAddressInfo' => $addressData['additionalAddressInfo'] ?? null,
            'postalCode' => $addressData['postalCode'] ?? null,
            'city' => $addressData['city'] ?? null,
            'country' => $addressData['country'] ?? null,
            'phone' => $addressData['phone'] ?? null,
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

    public function mapDataToDiscounts(array $cartData): array
    {
        if (empty($cartData['discountCodes'])) {
            return [];
        }

        $discounts = [];
        foreach ($cartData['discountCodes'] as $discount) {
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

    public function mapDataToPayments(array $cartData): array
    {
        if (empty($cartData['paymentInfo']['payments'])) {
            return [];
        }

        $payments = [];
        foreach ($cartData['paymentInfo']['payments'] as $payment) {
            $payments[] = $this->mapDataToPayment($payment);
        }

        return $payments;
    }

    public function mapDataToPayment(array $paymentData): Payment
    {
        $paymentData = isset($paymentData['obj']) ? $paymentData['obj'] : $paymentData;

        return new Payment(
            [
                'id' => $paymentData['key'] ?? null,
                'paymentId' => $paymentData['interfaceId'] ?? null,
                'paymentProvider' => $paymentData['paymentMethodInfo']['paymentInterface'] ?? null,
                'paymentMethod' => $paymentData['paymentMethodInfo']['method'] ?? null,
                'amount' => $paymentData['amountPlanned']['centAmount'] ?? null,
                'currency' => $paymentData['amountPlanned']['currencyCode'] ?? null,
                'debug' => json_encode($paymentData),
                'paymentStatus' => $paymentData['paymentStatus']['interfaceCode'] ?? null,
                'version' => $paymentData['version'] ?? 0,
            ]
        );
    }

    public function mapDataToShippingMethod(array $shippingData): ?ShippingMethod
    {
        if (!count($shippingData)) {
            return null;
        }

        return new ShippingMethod([
            'name' => $shippingData['shippingMethodName'] ?? null,
            'price' => $shippingData['price']['centAmount'] ?? null,
        ]);
    }
}
