<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\SprykerBundle\BaseApi\SprykerApiBase;
use Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Mapper\CheckoutMapper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Mapper\OrderMapper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\CheckoutRequestData;
use Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\SprykerCartInterface;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;
use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;

class SprykerCartApi extends SprykerApiBase implements CartApi
{
    /**
     * @var AccountHelper
     */
    private $accountHelper;

    /**
     * @var SprykerCartInterface
     */
    private $guestCart;

    /**
     * @var SprykerCartInterface
     */
    private $customerCart;

    /**
     * @var CheckoutRequestData
     */
    protected $checkoutRequest;

    /**
     * @var string[]
     */
    protected $orderIncludes;

    /**
     * @param SprykerClientInterface $client
     * @param MapperResolver $mapperResolver
     * @param AccountHelper $accountHelper
     * @param SprykerCartInterface $guestCart
     * @param SprykerCartInterface $customerCart
     * @param LocaleCreator $localeCreator
     * @param string[] $orderIncludes
     */
    public function __construct(
        SprykerClientInterface $client,
        MapperResolver $mapperResolver,
        AccountHelper $accountHelper,
        SprykerCartInterface $guestCart,
        SprykerCartInterface $customerCart,
        LocaleCreator $localeCreator,
        array $orderIncludes = []
    ) {
        parent::__construct($client, $mapperResolver, $localeCreator);
        $this->accountHelper = $accountHelper;
        $this->guestCart = $guestCart;
        $this->customerCart = $customerCart;
        $this->checkoutRequest = new CheckoutRequestData();
        $this->orderIncludes = $orderIncludes;
    }

    /**
     * @param Account $account
     * @param string $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function getForUser(Account $account, string $locale): Cart
    {
        return $this->customerCart->getCart($account->authToken, $locale);
    }

    /**
     * @param string $anonymousId
     * @param string $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function getAnonymous(string $anonymousId, string $locale): Cart
    {
        return $this->guestCart->getCart($anonymousId, $locale);
    }

    /**
     * @param array $lineItemType
     * @fixme Is this a hard CT dependency?
     */
    public function setCustomLineItemType(array $lineItemType): void
    {
    }

    /**
     * @return array
     * @fixme Is this a hard CT dependency?
     */
    public function getCustomLineItemType(): array
    {
        return [];
    }

    /**
     * @param array $taxCategory
     */
    public function setTaxCategory(array $taxCategory): void
    {
    }

    /**
     * @return array
     */
    public function getTaxCategory(): array
    {
        return [];
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function addToCart(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        return $this->getResolvedCart()->addLineItemToCart($cart, $lineItem);
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @param int $count
     * @param array|null $custom
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function updateLineItem(Cart $cart, LineItem $lineItem, int $count, ?array $custom = null, string $locale = null): Cart
    {
        return $this->getResolvedCart()->updateLineItem($cart, $lineItem, $count);
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function removeLineItem(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        return $this->getResolvedCart()->removeLineItem($cart, $lineItem);
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param string $email
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setEmail(Cart $cart, string $email, string $locale = null): Cart
    {
        throw new \RuntimeException('Do not use this method, use "setAccount" method');
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param Account $account
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setAccount(Cart $cart, Account $account): Cart
    {
        $this->checkoutRequest->setCustomer($account);

        return $cart;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param string $shippingMethod
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setShippingMethod(Cart $cart, string $shippingMethod, string $locale = null): Cart
    {
        $this->checkoutRequest->setShipmentMethod((int)$shippingMethod);

        return $cart;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param array $fields
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setCustomField(Cart $cart, array $fields, string $locale = null): Cart
    {
        return $cart;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param Address $address
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setShippingAddress(Cart $cart, Address $address, string $locale = null): Cart
    {
        $this->checkoutRequest->setShippingAddress($address);

        return $cart;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param Address $address
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setBillingAddress(Cart $cart, Address $address, string $locale = null): Cart
    {
        $this->checkoutRequest->setBillingAddress($address);

        return $cart;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\Payment $payment
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function addPayment(Cart $cart, Payment $payment, ?array $custom = null, string $locale = null): Cart
    {
        $this->checkoutRequest->setPayment($payment);

        return $cart;
    }

    public function updatePayment(Cart $cart, Payment $payment, string $localeString): Payment
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param string $code
     * @param string|null $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function redeemDiscountCode(Cart $cart, string $code, string $locale = null): Cart
    {
        return $cart;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param string|null $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Order
     */
    public function order(Cart $cart, string $locale = null): Order
    {
        $request = $this->checkoutRequest;
        $request->setIdCart($cart->cartId);

        $response = $this->client->post('/checkout?include=orders', $this->getClientHeader(), $request->encode());

        return $this->getCheckoutMapper()->mapResource($response->document()->primaryResource());
    }

    /**
     * @param Account $account
     * @param string $orderId
     * @param string|null $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Order
     */
    public function getOrder(Account $account, string $orderId, string $locale = null): Order
    {
        $response = $this->client->get(
            $this->withIncludes("/orders/{$orderId}", $this->orderIncludes),
            $this->getAuthHeader()
        );

        return $this->mapResponseResource($response, OrderMapper::MAPPER_NAME);
    }

    /**
     * @param Account $account
     * @param string|null $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Order[]
     */
    public function getOrders(Account $account, string $locale = null): array
    {
        $response = $this->client->get(sprintf('/orders'), $this->getAuthHeader());

        $mappedOrders = $this->getOrderMapper()->mapResourceArray($response->document()->primaryResources());

        $orders = [];

        foreach ($mappedOrders as $mappedOrder) {
            $orders[] = $this->getOrder($account, $mappedOrder->orderId, $locale);
        }

        return $orders;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     */
    public function startTransaction(Cart $cart): void
    {
    }

    /**
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function commit(string $locale = null): Cart
    {
        return $this->getResolvedCart()->getCart(null, $locale);
    }

    /**
     * @return SprykerClientInterface
     */
    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    /**
     * @return bool
     */
    protected function isLoggedIn(): bool
    {
        return $this->accountHelper->isLoggedIn();
    }

    /**
     * @return SprykerCartInterface
     */
    protected function getResolvedCart(): SprykerCartInterface
    {
        return $this->isLoggedIn() ? $this->customerCart : $this->guestCart;
    }

    /**
     * @return array
     */
    protected function getClientHeader(): array
    {
        return $this->isLoggedIn() ? $this->getAuthHeader() : $this->accountHelper->getAnonymousHeader();
    }

    /**
     * @return array
     */
    private function getAuthHeader(): array
    {
        return $this->accountHelper->getAuthHeader();
    }

    /**
     * @return OrderMapper
     */
    private function getOrderMapper(): MapperInterface
    {
        return $this->mapperResolver->getMapper(OrderMapper::MAPPER_NAME);
    }

    /**
     * @return CheckoutMapper
     */
    private function getCheckoutMapper(): MapperInterface
    {
        return $this->mapperResolver->getMapper(CheckoutMapper::MAPPER_NAME);
    }

    /**
     * @param string $cartId
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     * @throws \RuntimeExcption if cart with $cartId was not found
     */
    public function getById(string $cartId, string $locale = null): Cart
    {
        return $this->getResolvedCart()->getById($cartId, $locale);
    }

    public function setRawApiInput(Cart $cart, string $locale = null): Cart
    {
        // TODO: Implement setRawApiInput() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function removeDiscountCode(Cart $cart, LineItem $discountLineItem, string $locale = null): Cart
    {
        // TODO: Implement removeDiscountCode() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }
}
