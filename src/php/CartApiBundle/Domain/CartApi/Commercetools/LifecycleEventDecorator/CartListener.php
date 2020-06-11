<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools\LifecycleEventDecorator;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\CartApi\LifecycleEventDecorator\BaseImplementation;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\CoreBundle\Domain\BaseObject;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client as CommerceToolsClient;

class CartListener extends BaseImplementation
{
    const COMMERCETOOLS_ADDRESS_FIELDS = [
        'key' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => null,
        ],
        'title' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => null,
        ],
        'region' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => null,
        ],
        'state' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => null,
        ],
        'company' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => null,
        ],
        'department' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => null,
        ],
        'building' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => null,
        ],
        'apartment' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => null,
        ],
        'pOBox' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => null,
        ],
        'mobile' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => null,
        ],
        'email' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => null,
        ],
        'fax' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => null,
        ],
    ];

    const COMMERCETOOLS_CART_FIELDS = [
        'country' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => 'setCountry',
        ],
        'customerGroup' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => 'setCustomerGroup',
        ],
        'taxMode' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => 'changeTaxMode',
        ],
        'taxRoundingMode' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => 'changeTaxRoundingMode',
        ],
        'shippingRateInput' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => 'setShippingRateInput',
        ],
        'taxCalculationMode' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => 'changeTaxCalculationMode',
        ],
        'deleteDaysAfterLastModification' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => 'setDeleteDaysAfterLastModification',
        ],
        'itemShippingAddresses' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => 'updateItemShippingAddress',
        ],
    ];

    const COMMERCETOOLS_LINE_ITEM_FIELDS = [];

    const COMMERCETOOLS_PAYMENT_FIELDS = [
        'customer' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => 'setCustomer',
        ],
        'anonymousId' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => 'setAnonymousId',
        ],
        'interfaceId' => [
            self::COMMERCETOOLS_ACTION_NAME_KEY => 'setInterfaceId',
        ],
    ];

    const COMMERCETOOLS_ACTION_NAME_KEY = 'actionName';

    /**
     * @var CommerceToolsClient
     */
    private $client;

    public function beforeAddToCart(CartApi $cartApi, Cart $cart, LineItem $lineItem, string $locale = null): void
    {
        if (!($cartApi->getDangerousInnerClient() instanceof CommerceToolsClient)) {
            return;
        }

        //@TODO: client needs to be defined on the constructor
        $this->client = $cartApi->getDangerousInnerClient();

        $lineItem->rawApiInput = $this->mapLineItemRawApiInputData($lineItem, $cartApi);
    }

    public function beforeUpdateLineItem(
        CartApi $cartApi,
        Cart $cart,
        LineItem $lineItem,
        int $count,
        ?array $custom = null,
        string $locale = null
    ): void {
        if (!($cartApi->getDangerousInnerClient() instanceof CommerceToolsClient)) {
            return;
        }

        //@TODO: client needs to be defined on the constructor
        $this->client = $cartApi->getDangerousInnerClient();

        $lineItem->rawApiInput = $this->mapLineItemRawApiInputActions($lineItem);
    }

    public function beforeSetShippingAddress(
        CartApi $cartApi,
        Cart $cart,
        Address $address,
        string $locale = null
    ): void {
        if (!($cartApi->getDangerousInnerClient() instanceof CommerceToolsClient)) {
            return;
        }

        //@TODO: client needs to be defined on the constructor
        $this->client = $cartApi->getDangerousInnerClient();

        $address->rawApiInput = $this->mapAddressRawApiInputData($address);
    }

    public function beforeSetBillingAddress(CartApi $cartApi, Cart $cart, Address $address, string $locale = null): void
    {
        if (!($cartApi->getDangerousInnerClient() instanceof CommerceToolsClient)) {
            return;
        }

        //@TODO: client needs to be defined on the constructor
        $this->client = $cartApi->getDangerousInnerClient();

        $address->rawApiInput = $this->mapAddressRawApiInputData($address);
    }

    public function beforeAddPayment(
        CartApi $cartApi,
        Cart $cart,
        Payment $payment,
        ?array $custom = null,
        string $locale = null
    ): void {
        if (!($cartApi->getDangerousInnerClient() instanceof CommerceToolsClient)) {
            return;
        }

        //@TODO: client needs to be defined on the constructor
        $this->client = $cartApi->getDangerousInnerClient();

        $payment->rawApiInput = $this->mapPaymentRawApiInputData($payment, $custom);
        $custom = null;
    }

    public function beforeSetRawApiInput(CartApi $cartApi, Cart $cart, string $locale = null): void
    {
        if (!($cartApi->getDangerousInnerClient() instanceof CommerceToolsClient)) {
            return;
        }

        $this->client = $cartApi->getDangerousInnerClient();

        $cart->rawApiInput = $this->mapCartRawApiInputActions($cart);
    }

    private function mapLineItemRawApiInputData(BaseObject $baseObject, CartApi $cartApi): array
    {
        $rawApiInputData = $this->extractRawApiInputData(
            $baseObject,
            self::COMMERCETOOLS_LINE_ITEM_FIELDS
        );
        $customFields = $this->extractCustomFieldsData($baseObject);

        if (!empty($customFields)) {
            $rawApiInputData['custom'] = [
                'type' => $cartApi->getCustomLineItemType(),
                'fields' => $customFields,
            ];
        }

        return $rawApiInputData;
    }

    private function mapLineItemRawApiInputActions(LineItem $lineItem): array
    {
        $rawApiInputData = $this->extractRawApiInputData(
            $lineItem,
            self::COMMERCETOOLS_LINE_ITEM_FIELDS
        );
        $customFields = $this->extractCustomFieldsData($lineItem);

        $actions = [];
        foreach ($rawApiInputData as $fieldKey => $fieldValue) {
            $actions[] = [
                'action' => $this->determineAction($fieldKey, self::COMMERCETOOLS_LINE_ITEM_FIELDS),
                $fieldKey => $fieldValue
            ];
        }

        return array_merge($actions, $this->determineLineItemCustomFieldsActions($lineItem, $customFields));
    }

    private function mapPaymentRawApiInputData(BaseObject $baseObject, ?array $custom = null): array
    {
        $rawApiInputData = $this->extractRawApiInputData(
            $baseObject,
            self::COMMERCETOOLS_PAYMENT_FIELDS
        );

        if (!empty($custom)) {
            $rawApiInputData['custom'] = $custom;
        }

        return $rawApiInputData;
    }

    private function mapAddressRawApiInputData(BaseObject $baseObject): array
    {
        return $this->extractRawApiInputData(
            $baseObject,
            self::COMMERCETOOLS_ADDRESS_FIELDS
        );
    }

    private function extractRawApiInputData(BaseObject $baseObject, array $commerceToolsFields): array
    {
        $rawApiInputData = [];

        foreach ($commerceToolsFields as $fieldKey => $value) {
            // CommerceTools has it, but we don't map
            if (key_exists($fieldKey, $baseObject->projectSpecificData)) {
                $rawApiInputData[$fieldKey] = $baseObject->projectSpecificData[$fieldKey];
            }
        }

        return $rawApiInputData;
    }

    private function extractCustomFieldsData(BaseObject $baseObject): array
    {
        $customFields = [];

        // Custom field created by the client
        if (key_exists('option', $baseObject->projectSpecificData)) {
            $customFields[] = $baseObject->projectSpecificData['option'];
        }

        //@TODO: Should we separate this statements into add and updation events
        // Custom field created by the client
        if (key_exists('custom', $baseObject->projectSpecificData)) {
            $customFields[] = $baseObject->projectSpecificData['custom'];
        }

        return $customFields;
    }

    private function determineAction(string $fieldKey, array $commerceToolsFields): string
    {
        if (!array_key_exists($fieldKey, $commerceToolsFields)) {
            throw new \InvalidArgumentException('Unknown CommerceTools property: ' . $fieldKey);
        }

        return $commerceToolsFields[$fieldKey][self::COMMERCETOOLS_ACTION_NAME_KEY];
    }

    private function determineLineItemCustomFieldsActions(LineItem $lineItem, array $fields): array
    {
        $actions = [];
        foreach ($fields as $customFieldKey => $customFieldValue) {
            $actions[] = [
                'action' => 'setLineItemCustomField',
                'lineItemId' => $lineItem->lineItemId,
                'name' => $customFieldKey,
                'value' => $customFieldValue,
            ];
        }
        return $actions;
    }

    private function mapCartRawApiInputActions(BaseObject $baseObject): array
    {
        $rawApiInputData = $this->extractRawApiInputData(
            $baseObject,
            self::COMMERCETOOLS_CART_FIELDS
        );
        $customFields = $this->extractCustomFieldsData($baseObject);

        $actions = [];
        foreach ($rawApiInputData as $fieldKey => $fieldValue) {
            $actions[] = [
                'action' => $this->determineAction($fieldKey, self::COMMERCETOOLS_CART_FIELDS),
                $fieldKey => $fieldValue
            ];
        }

        return array_merge($actions, $this->determineCustomFieldsActions($customFields));
    }

    private function determineCustomFieldsActions(array $fields): array
    {
        $actions = [];
        foreach ($fields as $customFieldKey => $customFieldValue) {
            $actions[] = [
                'action' => 'setCustomField',
                'name' => $customFieldKey,
                'value' => $customFieldValue,
            ];
        }
        return $actions;
    }

    public function mapCustomFieldDataToCart(Cart $cart): ?Cart
    {
        $cart->birthday = isset($cart->dangerousInnerCart['custom']['fields']['birthday']) ?
            new \DateTimeImmutable($cart->dangerousInnerCart['custom']['fields']['birthday']) :
            null;
        $cart->lineItems = $this->mapCustomFieldDataToLineItem($cart->lineItems);
        $cart->projectSpecificData = $cart->dangerousInnerCart['custom']['fields'] ?? [];
        return $cart;
    }

    public function mapCustomFieldDataToOrder(Order $order): ?Order
    {
        $order->birthday = isset($order->dangerousInnerOrder['custom']['fields']['birthday']) ?
            new \DateTimeImmutable($order->dangerousInnerOrder['custom']['fields']['birthday']) :
            null;
        $order->lineItems = $this->mapCustomFieldDataToLineItem($order->lineItems);
        $order->projectSpecificData = $order->dangerousInnerCart['custom']['fields'] ?? [];
        return $order;
    }

    public function mapCustomFieldDataToLineItem(array $lineItems): ?array
    {
        /** @var LineItem|LineItem\Variant $lineItem */
        foreach ($lineItems as &$lineItem) {
            if (!$lineItem instanceof LineItem\Variant) {
                $lineItem->type = $lineItem->dangerousInnerItem['custom']['type'] ??
                    $lineItem->dangerousInnerItem['slug'];
            }
            $lineItem->projectSpecificData = $lineItem->dangerousInnerItem['custom']['fields'] ?? [];
        }

        usort(
            $lineItems,
            function (LineItem $a, LineItem $b): int {
                return ($a->projectSpecificData['bundleNumber'] ?? $a->name) <=>
                    ($b->projectSpecificData['bundleNumber'] ?? $b->name);
            }
        );

        return $lineItems;
    }
}
