<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools;

use Frontastic\Common\AccountApiBundle\Domain\AccountApi\Commercetools\Mapper as AccountMapper;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\Discount;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper as ProductMapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

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
         * [ ] Map tax information
         * [ ] Map discount text locales to our scheme
         */
        return new Cart([
            'cartId' => $cartData['id'],
            'cartVersion' => (string)$cartData['version'],
            'lineItems' => $this->mapDataToLineItems($cartData, $locale),
            'email' => $cartData['customerEmail'] ?? null,
            'shippingMethod' => $this->mapDataToShippingMethod($cartData['shippingInfo'] ?? []),
            'shippingAddress' => $this->mapDataToAddress($cartData['shippingAddress'] ?? []),
            'billingAddress' => $this->mapDataToAddress($cartData['billingAddress'] ?? []),
            'sum' => $cartData['totalPrice']['centAmount'],
            'currency' => $cartData['totalPrice']['currencyCode'],
            'payments' => $this->mapDataToPayments($cartData),
            'discountCodes' => $this->mapDataToDiscounts($cartData),
            'dangerousInnerCart' => $cartData,
        ]);
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
         * [ ] Map tax information
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
            'shippingMethod' => $this->mapDataToShippingMethod($orderData['shippingInfo'] ?? []),
            'shippingAddress' => $this->mapDataToAddress($orderData['shippingAddress'] ?? []),
            'billingAddress' => $this->mapDataToAddress($orderData['billingAddress'] ?? []),
            'sum' => $orderData['totalPrice']['centAmount'],
            'payments' => $this->mapDataToPayments($orderData),
            'discountCodes' => $this->mapDataToDiscounts($orderData),
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
            $paymentDetails = json_decode($frontasticPaymentDetails, true);
        }

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
                'paymentDetails' => $paymentDetails,
                'version' => $paymentData['version'] ?? 0,
            ]
        );
    }

    public function mapPaymentToData(Payment $payment): array
    {
        $customFields = $payment->rawApiInput['custom']['fields'] ?? [];
        if ($payment->paymentDetails !== null) {
            $customFields['frontasticPaymentDetails'] = json_encode($payment->paymentDetails);
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
