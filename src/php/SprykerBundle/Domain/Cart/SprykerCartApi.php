<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApiBase;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Mapper\CheckoutMapper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Mapper\OrderMapper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Mapper\ShipmentMethodsMapper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\CheckoutRequestData;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\CheckoutDataRequestData;
use Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\SprykerCartInterface;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\SprykerBundle\Domain\Locale\SprykerLocale;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;
use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;
use Frontastic\Common\SprykerBundle\Domain\SprykerUrlAppender;
use WoohooLabs\Yang\JsonApi\Response\JsonApiResponse;

class SprykerCartApi extends CartApiBase
{
    /**
     * @var SprykerClientInterface
     */
    protected $client;

    /**
     * @var LocaleCreator
     */
    protected $localeCreator;

    /**
     * @var DataMapperResolver
     */
    protected $mapperResolver;

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
     * @var SprykerUrlAppender
     */
    private $urlAppender;

    /**
     * @var string[]
     */
    protected $orderIncludes;

    /**
     * @var string|null
     */
    private $defaultLanguage;

    /**
     * @param SprykerClientInterface $client
     * @param MapperResolver $mapperResolver
     * @param AccountHelper $accountHelper
     * @param SprykerCartInterface $guestCart
     * @param SprykerCartInterface $customerCart
     * @param LocaleCreator $localeCreator
     * @param SprykerUrlAppender $urlAppender
     * @param string[] $orderIncludes
     * @param string|null $defaultLanguage
     */
    public function __construct(
        SprykerClientInterface $client,
        MapperResolver $mapperResolver,
        AccountHelper $accountHelper,
        SprykerCartInterface $guestCart,
        SprykerCartInterface $customerCart,
        LocaleCreator $localeCreator,
        SprykerUrlAppender $urlAppender,
        array $orderIncludes = [],
        ?string $defaultLanguage = null
    ) {
        $this->client = $client;
        $this->mapperResolver = $mapperResolver;
        $this->localeCreator = $localeCreator;
        $this->urlAppender = $urlAppender;
        $this->accountHelper = $accountHelper;
        $this->guestCart = $guestCart;
        $this->customerCart = $customerCart;
        $this->checkoutRequest = new CheckoutRequestData();
        $this->orderIncludes = $orderIncludes;
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * @param Account $account
     * @param string $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function getForUserImplementation(Account $account, string $locale): Cart
    {
        return $this->customerCart->getCart($account->authToken, $locale);
    }

    /**
     * @param string $anonymousId
     * @param string $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function getAnonymousImplementation(string $anonymousId, string $locale): Cart
    {
        return $this->guestCart->getCart($anonymousId, $locale);
    }

    /**
     * @param string $cartId
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     * @throws \RuntimeExcption if cart with $cartId was not found
     */
    protected function getByIdImplementation(string $cartId, string $locale = null): Cart
    {
        return $this->getResolvedCart()->getById($cartId, $locale);
    }

    /**
     * @param array $lineItemType
     * @fixme Is this a hard CT dependency?
     */
    protected function setCustomLineItemTypeImplementation(array $lineItemType): void
    {
    }

    /**
     * @return array
     * @fixme Is this a hard CT dependency?
     */
    protected function getCustomLineItemTypeImplementation(): array
    {
        return [];
    }

    /**
     * @param array $taxCategory
     */
    protected function setTaxCategoryImplementation(array $taxCategory): void
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    /**
     * @return array
     */
    protected function getTaxCategoryImplementation(): ?array
    {
        return null;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function addToCartImplementation(Cart $cart, LineItem $lineItem, string $locale = null): Cart
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
    protected function updateLineItemImplementation(
        Cart $cart,
        LineItem $lineItem,
        int $count,
        ?array $custom = null,
        string $locale = null
    ): Cart {
        return $this->getResolvedCart()->updateLineItem($cart, $lineItem, $count);
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function removeLineItemImplementation(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        return $this->getResolvedCart()->removeLineItem($cart, $lineItem);
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param string $email
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function setEmailImplementation(Cart $cart, string $email, string $locale = null): Cart
    {
        // Spryker only allows set email when the order is been placed
        $this->checkoutRequest->setCustomer(new Account(['email' => $email]));
        $cart->email = $email;

        return $cart;
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
    protected function setShippingMethodImplementation(Cart $cart, string $shippingMethod, string $locale = null): Cart
    {
        $this->checkoutRequest->setShipmentMethod((int)$shippingMethod);

        return $cart;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param array $fields
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function setCustomFieldImplementation(Cart $cart, array $fields, string $locale = null): Cart
    {
        return $cart;
    }

    protected function setRawApiInputImplementation(Cart $cart, string $locale = null): Cart
    {
        return $cart;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param Address $address
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function setShippingAddressImplementation(Cart $cart, Address $address, string $locale = null): Cart
    {
        // Spryker only allows set Shipping Address when the order is been placed
        $this->checkoutRequest->setShippingAddress($address);
        $cart->shippingAddress = $address;

        return $cart;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param Address $address
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function setBillingAddressImplementation(Cart $cart, Address $address, string $locale = null): Cart
    {
        // Spryker only allows set Billing Address when the order is been placed
        $this->checkoutRequest->setBillingAddress($address);
        $cart->billingAddress = $address;

        return $cart;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\Payment $payment
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function addPaymentImplementation(
        Cart $cart,
        Payment $payment,
        ?array $custom = null,
        string $locale = null
    ): Cart {
        $this->checkoutRequest->setPayment($payment);

        return $cart;
    }

    protected function updatePaymentImplementation(Cart $cart, Payment $payment, string $localeString): Payment
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param string $code
     * @param string|null $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function redeemDiscountCodeImplementation(Cart $cart, string $code, string $locale = null): Cart
    {
        return $this->getResolvedCart()->redeemDiscount($cart, $code, $locale);
    }

    protected function removeDiscountCodeImplementation(
        Cart $cart,
        LineItem $discountLineItem,
        string $locale = null
    ): Cart {
        return $this->getResolvedCart()->removeDiscount($cart, $discountLineItem, $locale);
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param string|null $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Order
     */
    protected function orderImplementation(Cart $cart, string $locale = null): Order
    {
        $sprykerLocale = $this->parseLocaleString($locale);

        $request = $this->checkoutRequest;
        $request->setIdCart($cart->cartId);

        $response = $this->client
            ->forLanguage($sprykerLocale->language)
            ->post('/checkout?include=orders', $this->getClientHeader(), $request->encode());

        return $this->getCheckoutMapper()->mapResource($response->document()->primaryResource());
    }

    /**
     * @param Account $account
     * @param string $orderId
     * @param string|null $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Order
     */
    protected function getOrderImplementation(Account $account, string $orderId, string $locale = null): Order
    {
        $sprykerLocale = $this->parseLocaleString($locale);

        $response = $this->client
            ->forLanguage($sprykerLocale->language)
            ->get(
                $this->urlAppender->withIncludes("/orders/{$orderId}", $this->orderIncludes),
                $this->getAuthHeader()
            );

        return $this->mapResponseResource($response, OrderMapper::MAPPER_NAME);
    }

    /**
     * @param Account $account
     * @param string|null $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Order[]
     */
    protected function getOrdersImplementation(Account $account, string $locale = null): array
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
    protected function startTransactionImplementation(Cart $cart): void
    {
    }

    /**
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function commitImplementation(string $locale = null): Cart
    {
        return $this->getResolvedCart()->getCart(null, $locale);
    }

    public function getAvailableShippingMethodsImplementation(Cart $cart, string $localeString): array
    {
        // If this is a guest cart, Spryker only generates a cart id when the first line item is added
        if (!$cart->cartId) {
            return [];
        }

        $sprykerLocale = $this->parseLocaleString($localeString);
        $request = new CheckoutDataRequestData($cart->cartId);

        $response = $this->client
            ->forLanguage($sprykerLocale->language)
            ->post(
                $this->urlAppender->withIncludes("/checkout-data?include=shipment-methods", []),
                $this->getClientHeader(),
                $request->encode()
            );

        return $this->mapResponseResource($response, ShipmentMethodsMapper::MAPPER_NAME);
    }

    public function getShippingMethodsImplementation(string $localeString, bool $onlyMatching = false): array
    {
        // TODO: Implement getShippingMethods() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
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

    private function parseLocaleString(string $localeString): SprykerLocale
    {
        return $this->localeCreator->createLocaleFromString($localeString ?? $this->defaultLanguage);
    }

    /**
     * @param JsonApiResponse $response
     * @param string $mapperName
     *
     * @return mixed
     */
    protected function mapResponseResource(JsonApiResponse $response, string $mapperName)
    {
        $document = $response->document();
        $mapper = $this->mapperResolver->getMapper($mapperName);

        if ($document->isSingleResourceDocument()) {
            return $mapper->mapResource($document->primaryResource());
        }

        return $mapper->mapResource($document->primaryResources()[0]);
    }
}
