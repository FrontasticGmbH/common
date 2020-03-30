<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\LifecycleEventDecorator;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;

/**
 * Base implementation of the CartApi LifecycleDecorator, which should be used when writing own LifecycleDecorators
 * as base class for future type-safety and convenience reasons, as it will provide the needed function naming as well
 * as parameter type-hinting.
 *
 * The before* Methods will be obviously called *before* the original method is executed and will get all the parameters
 * handed over, which the original method will get called with. Overwriting this method can be useful if you want to
 * manipulate the handed over parameters by simply manipulating it.
 * These methods doesn't return anything.
 *
 * The after* Methods will be oviously called *after* the orignal method is executed and will get the unwrapped result
 * from the original method handed over. So if the original methods returns a Promise, the resolved value will be
 * handed over to this function here.
 * Overwriting this method could be useful if you want to manipulate the result.
 * These methods need to return null if nothing should be manipulating, thus will lead to the original result being
 * returned or they need to return the same data-type as the original method returns, otherwise you will get Type-Errors
 * at some point.
 *
 * In order to make this class available to the Lifecycle-Decorator, you will need to tag your service based on this
 * class with "cartApi.lifecycleEventListener": e.g. by adding the tag inside the `services.xml`
 * ```
 * <tag name="cartApi.lifecycleEventListener" />
 * ```
 */
abstract class BaseImplementation
{
    /*** getForUser() *************************************************************************************************/
    public function beforeGetForUser(CartApi $cartApi, string $userId, string $locale): void
    {
    }

    public function afterGetForUser(CartApi $cartApi, Cart $cart): ?Cart
    {
        return null;
    }

    /*** getAnonymous() ***********************************************************************************************/
    public function beforeGetAnonymous(CartApi $cartApi, string $anonymousId, string $locale): void
    {
    }

    public function afterGetAnonymous(CartApi $cartApi, Cart $cart): ?Cart
    {
        return null;
    }

    /*** getById() ****************************************************************************************************/
    public function beforeGetById(CartApi $cartApi, string $cartId, string $locale = null): void
    {
    }

    public function afterGetById(CartApi $cartApi, Cart $cart): ?Cart
    {
        return null;
    }

    /*** addToCart() **************************************************************************************************/
    public function beforeAddToCart(CartApi $cartApi, Cart $cart, LineItem $lineItem, string $locale = null): void
    {
    }

    public function afterAddToCart(CartApi $cartApi, Cart $cart): ?Cart
    {
        return null;
    }

    /*** updateLineItem() *********************************************************************************************/
    public function beforeUpdateLineItem(
        CartApi $cartApi,
        Cart $cart,
        LineItem $lineItem,
        int $count,
        ?array $custom = null,
        string $locale = null
    ): void {
    }

    public function afterUpdateLineItem(CartApi $cartApi, Cart $cart): ?Cart
    {
        return null;
    }

    /*** removeLineItem() *********************************************************************************************/
    public function beforeRemoveLineItem(CartApi $cartApi, Cart $cart, LineItem $lineItem, string $locale = null): void
    {
    }

    public function afterRemoveLineItem(CartApi $cartApi, Cart $cart): ?Cart
    {
        return null;
    }

    /*** setEmail() ***************************************************************************************************/
    public function beforeSetEmail(CartApi $cartApi, Cart $cart, string $email, string $locale = null): void
    {
    }

    public function afterSetEmail(CartApi $cartApi, Cart $cart): ?Cart
    {
        return null;
    }

    /*** setShippingMethod() ******************************************************************************************/
    public function beforeSetShippingMethod(
        CartApi $cartApi,
        Cart $cart,
        string $shippingMethod,
        string $locale = null
    ): void {
    }

    public function afterSetShippingMethod(CartApi $cartApi, Cart $cart): ?Cart
    {
        return null;
    }

    /*** setCustomField() *********************************************************************************************/
    public function beforeSetCustomField(CartApi $cartApi, Cart $cart, array $fields, string $locale = null): void
    {
    }

    public function afterSetCustomField(CartApi $cartApi, Cart $cart): ?Cart
    {
        return null;
    }

    /*** setShippingAddress() *****************************************************************************************/
    public function beforeSetShippingAddress(CartApi $cartApi, Cart $cart, Address $address, string $locale = null): void
    {
    }

    public function afterSetShippingAddress(CartApi $cartApi, Cart $cart): ?Cart
    {
        return null;
    }

    /*** setBillingAddress() ******************************************************************************************/
    public function beforeSetBillingAddress(CartApi $cartApi, Cart $cart, Address $address, string $locale = null): void
    {
    }

    public function afterSetBillingAddress(CartApi $cartApi, Cart $cart): ?Cart
    {
        return null;
    }

    /*** addPayment() *************************************************************************************************/
    public function beforeAddPayment(
        CartApi $cartApi,
        Cart $cart,
        Payment $payment,
        ?array $custom = null,
        string $locale = null
    ): void {
    }

    public function afterAddPayment(CartApi $cartApi, Cart $cart): ?Cart
    {
        return null;
    }

    /*** redeemDiscountCode() *****************************************************************************************/
    public function beforeRedeemDiscountCode(CartApi $cartApi, Cart $cart, string $code, string $locale = null): void
    {
    }

    public function afterRedeemDiscountCode(CartApi $cartApi, Cart $cart): ?Cart
    {
        return null;
    }

    /*** order() ******************************************************************************************************/
    public function beforeOrder(CartApi $cartApi, Cart $cart): void
    {
    }

    public function afterOrder(CartApi $cartApi, Order $order): ?Order
    {
        return null;
    }

    /*** getOrder() ***************************************************************************************************/
    public function beforeGetOrder(CartApi $cartApi, string $orderId): void
    {
    }

    public function afterGetOrder(CartApi $cartApi, Order $orderId): ?Order
    {
        return null;
    }

    /*** getOrders() **************************************************************************************************/
    public function beforeGetOrders(CartApi $cartApi, string $accountId): void
    {
    }

    /**
     * @param CartApi $cartApi
     * @param Order[] $orders
     * @return Order[]|null
     */
    public function afterGetOrders(CartApi $cartApi, array $orders): ?array
    {
        return null;
    }

    /*** startTransaction() *******************************************************************************************/
    public function beforeStartTransaction(CartApi $cartApi, Cart $cart): void
    {
    }
    // as this original method returns `void` it does not make sense to have an after* method here

    /*** commit() *****************************************************************************************************/
    public function beforeCommit(CartApi $cartApi, string $locale = null): void
    {
    }

    public function afterCommit(CartApi $cartApi, Cart $cart): ?Cart
    {
        return null;
    }
}
