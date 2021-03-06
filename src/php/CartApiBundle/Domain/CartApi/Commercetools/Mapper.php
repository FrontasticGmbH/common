<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools;

use Frontastic\Common\AccountApiBundle\Domain\AccountApi\Commercetools\Mapper as AccountMapper;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\Discount;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\CartApiBundle\Domain\ShippingInfo;
use Frontastic\Common\CartApiBundle\Domain\ShippingLocation;
use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;
use Frontastic\Common\CartApiBundle\Domain\ShippingRate;
use Frontastic\Common\CartApiBundle\Domain\Tax;
use Frontastic\Common\CartApiBundle\Domain\TaxPortion;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper as ProductMapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\CoreBundle\Domain\Json\Json;

class Mapper
{
    public const CUSTOM_PAYMENT_FIELDS_KEY = 'frontastic-payment';

    /** @var AccountMapper */
    private $accountMapper;

    /** @var ProductMapper */
    private $productMapper;

    public function __construct(AccountMapper $accountMapper, ProductMapper $productMapper)
    {
        $this->accountMapper = $accountMapper;
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
            'state' => $addressData['state'] ?? null,
            'phone' => $addressData['phone'] ?? null,
        ]);
    }

    public function mapAddressToData(Address $address): array
    {
        return $this->accountMapper->mapAddressToData($address);
    }

    public function mapDataToCart(array $cartData, CommercetoolsLocale $locale): Cart
    {
        /**
         * @TODO:
         *
         * [ ] Map delivery costs / properties
         * [ ] Map product discounts
         * [ ] Map discount codes
         * [ ] Map discount text locales to our scheme
         */
        return new Cart([
            'cartId' => $cartData['id'],
            'cartVersion' => (string)$cartData['version'],
            'lineItems' => $this->mapDataToLineItems($cartData, $locale),
            'email' => $cartData['customerEmail'] ?? null,
            'shippingInfo' => $this->mapDataToShippingInfo($cartData['shippingInfo'] ?? []),
            'shippingMethod' => $this->mapDataToShippingInfo($cartData['shippingInfo'] ?? []),
            'shippingAddress' => $this->mapDataToAddress($cartData['shippingAddress'] ?? []),
            'billingAddress' => $this->mapDataToAddress($cartData['billingAddress'] ?? []),
            'sum' => $cartData['totalPrice']['centAmount'],
            'currency' => $cartData['totalPrice']['currencyCode'],
            'payments' => $this->mapDataToPayments($cartData),
            'discountCodes' => $this->mapDataToDiscounts($cartData),
            'taxed' => $this->mapDataToTax($cartData),
            'dangerousInnerCart' => $cartData,
        ]);
    }

    public function mapDataToDiscounts(array $cartData): array
    {
        if (empty($cartData['discountCodes'])) {
            return [];
        }

        $discounts = [];
        foreach ($cartData['discountCodes'] as $discountData) {
            $discount = $this->mapDataToDiscount(
                $discountData['discountCode']['obj'] ?? $discountData['discountCode'] ?? []
            );
            $discount->state = $discountData['state'] ?? null;
            $discounts[] = $discount;
        }

        return $discounts;
    }

    public function mapDataToDiscount(array $discountData): Discount
    {
        return new Discount([
            'discountId' => $discountData['id'] ?? 'undefined',
            'code' => $discountData['code'] ?? null,
            'name' => $discountData['name'] ?? null,
            'description' => $discountData['description'] ?? null,
            'dangerousInnerDiscount' => $discountData,
        ]);
    }

    public function mapDataToLineItemDiscounts(array $lineItemDiscountsData): array
    {
        return array_map(
            function ($discountData): Discount {
                $discount = $this->mapDataToDiscount($discountData['discount']['obj'] ?? []);
                $discount->discountedAmount = $discountData['discountedAmount']['centAmount'] ?? null;

                return $discount;
            },
            ($lineItemDiscountsData['includedDiscounts'] ?? [])
        );
    }

    /**
     * @return LineItem[]
     */
    public function mapDataToLineItems(array $cartData, CommercetoolsLocale $locale): array
    {
        return array_merge(
            array_map(
                function (array $lineItemData) use ($locale): LineItem {
                    list($price, $currency, $discountedPrice) = $this->productMapper->dataToPrice($lineItemData);
                    return new LineItem\Variant([
                        'lineItemId' => $lineItemData['id'],
                        'name' => $this->productMapper->getLocalizedValue($locale, $lineItemData['name']),
                        'type' => 'variant',
                        'variant' => $this->productMapper->dataToVariant(
                            $lineItemData['variant'],
                            new Query(),
                            $locale
                        ),
                        'count' => $lineItemData['quantity'],
                        'price' => $price,
                        'discountedPrice' => $discountedPrice,
                        'discountTexts' => array_map(
                            function ($discount): array {
                                return $discount['discount']['obj']['name'] ?? [];
                            },
                            (isset($lineItemData['discountedPrice']['includedDiscounts'])
                                ? $lineItemData['discountedPrice']['includedDiscounts']
                                : []
                            )
                        ),
                        'discounts' => $this->mapDataToLineItemDiscounts($lineItemData['discountedPrice'] ?? []),
                        'totalPrice' => $lineItemData['totalPrice']['centAmount'],
                        'currency' => $currency,
                        'isGift' => ($lineItemData['lineItemMode'] === 'GiftLineItem'),
                        'dangerousInnerItem' => $lineItemData,
                    ]);
                },
                $cartData['lineItems']
            ),
            array_map(
                function (array $lineItemData) use ($locale): LineItem {
                    return new LineItem([
                        'lineItemId' => $lineItemData['id'],
                        'name' => $this->productMapper->getLocalizedValue($locale, $lineItemData['name']),
                        'count' => $lineItemData['quantity'],
                        'price' => $lineItemData['money']['centAmount'],
                        'discountedPrice' => (isset($lineItemData['discountedPrice'])
                            ? $lineItemData['totalPrice']['centAmount']
                            : null
                        ),
                        'discountTexts' => array_map(
                            function ($discount): array {
                                return $discount['discount']['obj']['name'] ?? [];
                            },
                            (isset($lineItemData['discountedPrice']['includedDiscounts'])
                                ? $lineItemData['discountedPrice']['includedDiscounts']
                                : []
                            )
                        ),
                        'discounts' => $this->mapDataToLineItemDiscounts($lineItemData['discountedPrice'] ?? []),
                        'totalPrice' => $lineItemData['totalPrice']['centAmount'],
                        'dangerousInnerItem' => $lineItemData,
                    ]);
                },
                $cartData['customLineItems']
            )
        );
    }

    public function mapDataToOrder(array $orderData, CommercetoolsLocale $locale): Order
    {
        /**
         * @TODO:
         *
         * [ ] Map delivery costs / properties
         * [ ] Map product discounts
         * [ ] Map discount codes
         * [ ] Map delivery status
         * [ ] Map order status
         */
        return new Order([
            'cartId' => $orderData['id'],
            'orderState' => $orderData['orderState'],
            'createdAt' => new \DateTimeImmutable($orderData['createdAt']),
            'orderId' => $orderData['orderNumber'],
            'orderVersion' => $orderData['version'],
            'lineItems' => $this->mapDataToLineItems($orderData, $locale),
            'email' => $orderData['customerEmail'] ?? null,
            'shippingInfo' => $this->mapDataToShippingInfo($orderData['shippingInfo'] ?? []),
            'shippingMethod' => $this->mapDataToShippingInfo($orderData['shippingInfo'] ?? []),
            'shippingAddress' => $this->mapDataToAddress($orderData['shippingAddress'] ?? []),
            'billingAddress' => $this->mapDataToAddress($orderData['billingAddress'] ?? []),
            'sum' => $orderData['totalPrice']['centAmount'],
            'payments' => $this->mapDataToPayments($orderData),
            'discountCodes' => $this->mapDataToDiscounts($orderData),
            'taxed' => $this->mapDataToTax($orderData),
            'dangerousInnerCart' => $orderData,
            'dangerousInnerOrder' => $orderData,
            'currency' => $orderData['totalPrice']['currencyCode'],
        ]);
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

        $frontasticPaymentDetails = $paymentData['custom']['fields']['frontasticPaymentDetails'] ?? null;
        $paymentDetails = null;
        if (is_string($frontasticPaymentDetails)) {
            $paymentDetails = Json::decode($frontasticPaymentDetails, true);
        }

        return new Payment(
            [
                'id' => $paymentData['key'] ?? null,
                'paymentId' => $paymentData['interfaceId'] ?? null,
                'paymentProvider' => $paymentData['paymentMethodInfo']['paymentInterface'] ?? null,
                'paymentMethod' => $paymentData['paymentMethodInfo']['method'] ?? null,
                'amount' => $paymentData['amountPlanned']['centAmount'] ?? null,
                'currency' => $paymentData['amountPlanned']['currencyCode'] ?? null,
                'debug' => Json::encode($paymentData),
                'paymentStatus' => $paymentData['paymentStatus']['interfaceCode'] ?? null,
                'paymentDetails' => $paymentDetails,
                'version' => $paymentData['version'] ?? 0,
            ]
        );
    }

    public function mapPaymentToData(Payment $payment): array
    {
        $customFields = $payment->rawApiInput['custom']['fields'] ?? [];
        if ($payment->paymentDetails !== null) {
            $customFields['frontasticPaymentDetails'] = Json::encode($payment->paymentDetails);
        }

        return array_merge(
            $payment->rawApiInput,
            [
                'key' => $payment->id,
                'amountPlanned' => [
                    'centAmount' => $payment->amount,
                    'currencyCode' => $payment->currency,
                ],
                'interfaceId' => $payment->paymentId,
                'paymentMethodInfo' => [
                    'paymentInterface' => $payment->paymentProvider,
                    'method' => $payment->paymentMethod,
                ],
                'paymentStatus' => [
                    'interfaceCode' => $payment->paymentStatus,
                    'interfaceText' => $payment->debug,
                ],
                'custom' => [
                    'type' => [
                        'key' => self::CUSTOM_PAYMENT_FIELDS_KEY,
                    ],
                    'fields' => (object)$customFields,
                ],
            ]
        );
    }

    public function mapDataToShippingInfo(array $shippingInfoData): ?ShippingInfo
    {
        if (!count($shippingInfoData)) {
            return null;
        }

        $price = $shippingInfoData['price']['centAmount'] ?? null;

        if (key_exists('discountedPrice', $shippingInfoData)) {
            $price = $shippingInfoData['discountedPrice']['value']['centAmount'] ?? null;
        }

        return new ShippingInfo([
            'shippingMethodId' => $shippingInfoData['shippingMethod']['id'] ?? null,
            'name' => $shippingInfoData['shippingMethodName'] ?? null,
            'price' => $price,
        ]);
    }

    public function mapDataToShippingMethod(array $shippingMethodData, CommercetoolsLocale $locale): ShippingMethod
    {
        return new ShippingMethod([
            'shippingMethodId' => $shippingMethodData['id'],
            'name' => $shippingMethodData['name'] ?? null,
            'description' => $this->productMapper->getLocalizedValue(
                $locale,
                $shippingMethodData['localizedDescription'] ?? []
            ),
            'rates' => $this->mapDataToShippingRates($shippingMethodData['zoneRates'] ?? []),
            'dangerousInnerShippingMethod' => $shippingMethodData,
        ]);
    }

    public function mapDataToTax(array $cartData): ?Tax
    {
        if (empty($cartData['taxedPrice'])) {
            return null;
        }

        return new Tax([
            'amount' => $cartData['taxedPrice']['totalNet']['centAmount'],
            'currency' => $cartData['taxedPrice']['totalNet']['currencyCode'],
            'taxPortions' => array_map(
                function ($taxPortionData): TaxPortion {
                    return new TaxPortion([
                        'amount' => $taxPortionData['amount']['centAmount'],
                        'currency' => $taxPortionData['amount']['currencyCode'],
                        'name' => $taxPortionData['name'],
                        'rate' => $taxPortionData['rate'],
                    ]);
                },
                $cartData['taxedPrice']['taxPortions']
            ),
        ]);
    }

    private function mapDataToShippingRates(array $zoneRatesData): ?array
    {
        if (!count($zoneRatesData)) {
            return null;
        }

        $shippingRates = [];

        foreach ($zoneRatesData as $zoneRateData) {
            $zoneId = $zoneRateData['zone']['id'];
            $name = $zoneRateData['zone']['obj']['name'] ?? null;
            $locations = array_map(
                function ($location) {
                    return new ShippingLocation([
                        'country' => $location['country'] ?? null,
                        'state' => $location['state'] ?? null,
                    ]);
                },
                $zoneRateData['zone']['obj']['locations'] ?? []
            );

            $matchingShippingRates = array_filter(
                $zoneRateData['shippingRates'],
                function ($shippingRate) {
                    return (bool)($shippingRate['isMatching'] ?? true);
                }
            );

            if (!count($matchingShippingRates)) {
                continue;
            }

            $shippingRates = array_merge($shippingRates, array_map(
                function ($shippingRate) use ($zoneId, $name, $locations) {
                    if (key_exists('isMatching', $shippingRate) && $shippingRate['isMatching'] === false) {
                        return [];
                    }

                    return new ShippingRate([
                        'zoneId' => $zoneId,
                        'name' => $name,
                        'locations' => $locations,
                        'currency' => $shippingRate['price']['currencyCode'],
                        'price' => $shippingRate['price']['centAmount'],
                    ]);
                },
                $matchingShippingRates ?? []
            ));
        }

        return $shippingRates;
    }
}
