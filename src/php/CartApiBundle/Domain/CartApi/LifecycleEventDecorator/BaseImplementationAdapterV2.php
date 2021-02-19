<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\LifecycleEventDecorator;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;

class BaseImplementationAdapterV2 extends BaseImplementationV2
{
    /**
     * @var BaseImplementation
     */
    private $baseImplementation;

    public function __construct(BaseImplementation $baseImplementation)
    {
        $this->baseImplementation = $baseImplementation;
    }

    public function beforeGetForUser(CartApi $cartApi, Account $account, string $locale): ?array
    {
        $this->baseImplementation->beforeGetForUser($cartApi, $account, $locale);
        return [$account, $locale];
    }

    public function afterGetForUser(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterGetForUser($cartApi, $cart);
    }

    public function beforeGetAnonymous(CartApi $cartApi, string $anonymousId, string $locale): ?array
    {
        $this->baseImplementation->beforeGetAnonymous($cartApi, $anonymousId, $locale);
        return [$anonymousId, $locale];
    }

    public function afterGetAnonymous(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterGetAnonymous($cartApi, $cart);
    }

    public function beforeGetById(CartApi $cartApi, string $cartId, string $locale = null): ?array
    {
        $this->baseImplementation->beforeGetById($cartApi, $cartId, $locale);
        return [$cartId, $locale];
    }

    public function afterGetById(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterGetById($cartApi, $cart);
    }

    public function beforeAddToCart(CartApi $cartApi, Cart $cart, LineItem $lineItem, string $locale = null): ?array
    {
        $this->baseImplementation->beforeAddToCart($cartApi, $cart, $lineItem, $locale);
        return [$cart, $lineItem, $locale];
    }

    public function afterAddToCart(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterAddToCart($cartApi, $cart);
    }

    public function beforeUpdateLineItem(
        CartApi $cartApi,
        Cart $cart,
        LineItem $lineItem,
        int $count,
        ?array $custom = null,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeUpdateLineItem($cartApi, $cart, $lineItem, $count, $custom, $locale);
        return [$cart, $lineItem, $count, $custom, $locale];
    }

    public function afterUpdateLineItem(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterUpdateLineItem($cartApi, $cart);
    }

    public function beforeRemoveLineItem(
        CartApi $cartApi,
        Cart $cart,
        LineItem $lineItem,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeRemoveLineItem($cartApi, $cart, $lineItem, $locale);
        return [$cart, $lineItem, $locale];
    }

    public function afterRemoveLineItem(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterRemoveLineItem($cartApi, $cart);
    }

    public function beforeSetEmail(CartApi $cartApi, Cart $cart, string $email, string $locale = null): ?array
    {
        $this->baseImplementation->beforeSetEmail($cartApi, $cart, $email, $locale);
        return [$cart, $email, $locale];
    }

    public function afterSetEmail(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterSetEmail($cartApi, $cart);
    }

    public function beforeSetShippingMethod(
        CartApi $cartApi,
        Cart $cart,
        string $shippingMethod,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeSetShippingMethod($cartApi, $cart, $shippingMethod, $locale);
        return [$cart, $shippingMethod, $locale];
    }

    public function afterSetShippingMethod(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterSetShippingMethod($cartApi, $cart);
    }

    public function beforeSetCustomField(CartApi $cartApi, Cart $cart, array $fields, string $locale = null): ?array
    {
        $this->baseImplementation->beforeSetCustomField($cartApi, $cart, $fields, $locale);
        return [$cart, $fields, $locale];
    }

    public function afterSetCustomField(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterSetCustomField($cartApi, $cart);
    }

    public function beforeSetRawApiInput(CartApi $cartApi, Cart $cart, string $locale = null): ?array
    {
        $this->baseImplementation->beforeSetRawApiInput($cartApi, $cart, $locale);
        return [$cart, $locale];
    }

    public function afterSetRawApiInput(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterSetRawApiInput($cartApi, $cart);
    }

    public function beforeSetShippingAddress(
        CartApi $cartApi,
        Cart $cart,
        Address $address,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeSetShippingAddress($cartApi, $cart, $address, $locale);
        return [$cart, $address, $locale];
    }

    public function afterSetShippingAddress(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterSetShippingAddress($cartApi, $cart);
    }

    public function beforeSetBillingAddress(
        CartApi $cartApi,
        Cart $cart,
        Address $address,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeSetBillingAddress($cartApi, $cart, $address, $locale);
        return [$cart, $address, $locale];
    }

    public function afterSetBillingAddress(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterSetBillingAddress($cartApi, $cart);
    }

    public function beforeAddPayment(
        CartApi $cartApi,
        Cart $cart,
        Payment $payment,
        ?array $custom = null,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeAddPayment($cartApi, $cart, $payment, $custom, $locale);
        return [$cart, $payment, $custom, $locale];
    }

    public function afterAddPayment(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterAddPayment($cartApi, $cart);
    }

    public function beforeUpdatePayment(
        CartApi $cartApi,
        Cart $cart,
        Payment $payment,
        string $localeString
    ): ?array {
        $this->baseImplementation->beforeUpdatePayment($cartApi, $cart, $payment, $localeString);
        return [$cart, $payment, $localeString];
    }

    public function afterUpdatePayment(CartApi $cartApi, Payment $payment): ?Payment
    {
        return $this->baseImplementation->afterUpdatePayment($cartApi, $payment);
    }

    public function beforeRedeemDiscountCode(CartApi $cartApi, Cart $cart, string $code, string $locale = null): ?array
    {
        $this->baseImplementation->beforeRedeemDiscountCode($cartApi, $cart, $code, $locale);
        return [$cart, $code, $locale];
    }

    public function afterRedeemDiscountCode(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterRedeemDiscountCode($cartApi, $cart);
    }

    public function beforeRemoveDiscountCode(
        CartApi $cartApi,
        Cart $cart,
        LineItem $discountLineItem,
        string $locale = null
    ): ?array {
        $this->baseImplementation->beforeRemoveDiscountCode($cartApi, $cart, $discountLineItem, $locale);
        return [$cart, $discountLineItem, $locale];
    }

    public function afterRemoveDiscountCode(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterRemoveDiscountCode($cartApi, $cart);
    }

    public function beforeOrder(CartApi $cartApi, Cart $cart, string $locale = null): ?array
    {
        $this->baseImplementation->beforeOrder($cartApi, $cart, $locale);
        return [$cart, $locale];
    }

    public function afterOrder(CartApi $cartApi, Order $order): ?Order
    {
        return $this->baseImplementation->afterOrder($cartApi, $order);
    }

    public function beforeGetOrder(CartApi $cartApi, Account $account, string $orderId, string $locale = null): ?array
    {
        $this->baseImplementation->beforeGetOrder($cartApi, $account, $orderId, $locale);
        return [$account, $orderId, $locale];
    }

    public function afterGetOrder(CartApi $cartApi, Order $order): ?Order
    {
        return $this->baseImplementation->afterGetOrder($cartApi, $order);
    }

    public function beforeGetOrders(CartApi $cartApi, Account $account, string $locale = null): ?array
    {
        $this->baseImplementation->beforeGetOrders($cartApi, $account, $locale);
        return [$account, $locale];
    }

    /**
     * @param CartApi $cartApi
     * @param Order[] $orders
     * @return Order[]|null
     */
    public function afterGetOrders(CartApi $cartApi, array $orders): ?array
    {
        return $this->baseImplementation->afterGetOrders($cartApi, $orders);
    }

    public function beforeStartTransaction(CartApi $cartApi, Cart $cart): ?array
    {
        $this->baseImplementation->beforeStartTransaction($cartApi, $cart);
        return [$cart];
    }
    // as this original method returns `void` it does not make sense to have an after* method here

    public function beforeCommit(CartApi $cartApi, string $locale = null): ?array
    {
        $this->baseImplementation->beforeCommit($cartApi, $locale);
        return [$locale];
    }

    public function afterCommit(CartApi $cartApi, Cart $cart): ?Cart
    {
        return $this->baseImplementation->afterCommit($cartApi, $cart);
    }

    public function beforeGetAvailableShippingMethods(CartApi $cartApi, Cart $cart, string $localeString): ?array
    {
        return $this->baseImplementation->beforeGetAvailableShippingMethods($cartApi, $cart, $localeString);
    }

    public function afterGetAvailableShippingMethods(CartApi $cartApi, array $availableShippingMethods):  ?array
    {
        return $this->baseImplementation->afterGetAvailableShippingMethods($cartApi, $availableShippingMethods);
    }

    public function beforeGetShippingMethods(
        CartApi $cartApi,
        string $localeString,
        bool $onlyMatching = false
    ): ?array {
        return $this->baseImplementation->beforeGetShippingMethods($cartApi, $localeString, $onlyMatching);
    }

    public function afterGetShippingMethods(CartApi $cartApi, array $shippingMethods): ?array
    {
        return $this->baseImplementation->afterGetShippingMethods($cartApi, $shippingMethods);
    }

    public function mapReturnedCart(Cart $cart): ?Cart
    {
        return $this->baseImplementation->mapReturnedCart($cart);
    }

    public function mapReturnedOrder(Order $order): ?Order
    {
        return $this->baseImplementation->mapReturnedOrder($order);
    }
}
