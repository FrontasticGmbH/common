<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Discount;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper as ProductMapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

class Mapper
{
    /** @var ProductMapper */
    private $productMapper;

    public function __construct(ProductMapper $productMapper)
    {
        $this->productMapper = $productMapper;
    }

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

    /**
     * @return LineItem[]
     */
    public function mapDataToLineItems(array $cart, CommercetoolsLocale $locale): array
    {
        $lineItems = array_merge(
            array_map(
                function (array $lineItem) use ($locale): LineItem {
                    list($price, $currency, $discountedPrice) = $this->productMapper->dataToPrice($lineItem);
                    return new LineItem\Variant([
                        'lineItemId' => $lineItem['id'],
                        'name' => $this->productMapper->getLocalizedValue($locale, $lineItem['name']),
                        'type' => 'variant',
                        'variant' => $this->productMapper->dataToVariant(
                            $lineItem['variant'],
                            new Query(),
                            $locale
                        ),
                        'custom' => $lineItem['custom']['fields'] ?? [],
                        'count' => $lineItem['quantity'],
                        'price' => $price,
                        'discountedPrice' => $discountedPrice,
                        'discountTexts' => array_map(
                            function ($discount): array {
                                return $discount['discount']['obj']['name'] ?? [];
                            },
                            (isset($lineItem['discountedPrice']['includedDiscounts'])
                                ? $lineItem['discountedPrice']['includedDiscounts']
                                : []
                            )
                        ),
                        'totalPrice' => $lineItem['totalPrice']['centAmount'],
                        'currency' => $currency,
                        'isGift' => ($lineItem['lineItemMode'] === 'GiftLineItem'),
                        'dangerousInnerItem' => $lineItem,
                    ]);
                },
                $cart['lineItems']
            ),
            array_map(
                function (array $lineItem) use ($locale): LineItem {
                    return new LineItem([
                        'lineItemId' => $lineItem['id'],
                        'name' => $this->productMapper->getLocalizedValue($locale, $lineItem['name']),
                        'type' => $lineItem['custom']['type'] ?? $lineItem['slug'],
                        'custom' => $lineItem['custom']['fields'] ?? [],
                        'count' => $lineItem['quantity'],
                        'price' => $lineItem['money']['centAmount'],
                        'discountedPrice' => (isset($lineItem['discountedPrice'])
                            ? $lineItem['totalPrice']['centAmount']
                            : null
                        ),
                        'discountTexts' => array_map(
                            function ($discount): array {
                                return $discount['discount']['obj']['name'] ?? [];
                            },
                            (isset($lineItem['discountedPrice']['includedDiscounts'])
                                ? $lineItem['discountedPrice']['includedDiscounts']
                                : []
                            )
                        ),
                        'totalPrice' => $lineItem['totalPrice']['centAmount'],
                        'dangerousInnerItem' => $lineItem,
                    ]);
                },
                $cart['customLineItems']
            )
        );

        usort(
            $lineItems,
            function (LineItem $a, LineItem $b): int {
                return ($a->custom['bundleNumber'] ?? $a->name) <=>
                    ($b->custom['bundleNumber'] ?? $b->name);
            }
        );

        return $lineItems;
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
