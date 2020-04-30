<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\ShopwareBundle\Domain\AbstractShopwareApi;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\CartItemRequestDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\CartMapper;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\OrderMapper;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\OrdersMapper;
use Frontastic\Common\ShopwareBundle\Domain\ClientInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory;
use function GuzzleHttp\Promise\promise_for;

class ShopwareCartApi extends AbstractShopwareApi implements CartApi
{
    public const LINE_ITEM_TYPE_CREDIT = 'credit';
    public const LINE_ITEM_TYPE_CUSTOM = 'custom';
    public const LINE_ITEM_TYPE_PRODUCT = 'product';
    public const LINE_ITEM_TYPE_PROMOTION = 'promotion';

    private const CART_NAME_GUEST = 'frontastic-guest';
    private const CART_NAME_DEFAULT = 'frontastic-default';

    private const DEFAULT_ORDER_LIMIT = 99;
    private const DEFAULT_ORDER_PAGE = 1;

    /**
     * @var string
     */
    private $currentTransaction;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiInterface
     */
    private $projectConfigApi;

    public function __construct(
        ClientInterface $client,
        DataMapperResolver $mapperResolver,
        LocaleCreator $localeCreator,
        ShopwareProjectConfigApiFactory $projectConfigApiFactory
    ) {
        parent::__construct($client, $mapperResolver, $localeCreator);

        $this->projectConfigApi = $projectConfigApiFactory->factor($client);
    }

    public function getForUser(Account $account, string $locale): Cart
    {
        // When user is authenticated, his cart can be retrieved by using his context token
        return $this->getById($account->getToken(self::TOKEN_TYPE), $locale);
    }

    public function getAnonymous(string $anonymousId, string $locale): Cart
    {
        $shopwareLocale = $this->parseLocaleString($locale);

        $requestData = [
            'name' => self::CART_NAME_GUEST,
        ];

        // When user is anonymous, the cart need to be initialized first, and then returned using the context token
        // returned by the init procedure
        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->post('/checkout/cart', [], $requestData)
            ->then(static function ($response) {
                return $response[self::KEY_CONTEXT_TOKEN];
            })->then(function ($token) use ($locale) {
                return $this->getById($token, $locale);
            })
            ->wait();
    }

    public function getById(string $token, string $locale = null): Cart
    {
        $shopwareLocale = $this->parseLocaleString($locale);

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($token)
            ->get('/checkout/cart')
            ->then(function ($response) {
                return $this->mapResponse($response, CartMapper::MAPPER_NAME);
            })
            ->wait();
    }

    public function setCustomLineItemType(array $lineItemType): void
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getCustomLineItemType(): array
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        return [];
    }

    public function setTaxCategory(array $taxCategory): void
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getTaxCategory(): array
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        return [];
    }

    public function addToCart(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        $shopwareLocale = $this->parseLocaleString($locale);

        $requestData = $this->mapRequestData($lineItem, CartItemRequestDataMapper::MAPPER_NAME);
        $id = $requestData['referencedId'];

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($cart->cartId)
            ->post("/checkout/cart/line-item/{$id}", [], $requestData)
            ->then(function ($response) {
                if (isset($response['data']['errors']) && !empty($response['data']['errors'])) {
                    $this->respondWithError($response['data']['errors']);
                }

                return $this->mapResponse($response, CartMapper::MAPPER_NAME);
            })
            ->wait();
    }

    public function updateLineItem(
        Cart $cart,
        LineItem $lineItem,
        int $count,
        ?array $custom = null,
        string $locale = null
    ): Cart {
        $shopwareLocale = $this->parseLocaleString($locale);

        $requestData = $this->mapRequestData($lineItem, CartItemRequestDataMapper::MAPPER_NAME);
        $id = $requestData['referencedId'];

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($cart->cartId)
            ->patch("/checkout/cart/line-item/{$id}", [], $requestData)
            ->then(function ($response) {
                if (isset($response['data']['errors']) && !empty($response['data']['errors'])) {
                    $this->respondWithError($response['data']['errors']);
                }

                return $this->mapResponse($response, CartMapper::MAPPER_NAME);
            })
            ->wait();
    }

    public function removeLineItem(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        $shopwareLocale = $this->parseLocaleString($locale);

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($cart->cartId)
            ->delete("/checkout/cart/line-item/{$lineItem->lineItemId}")
            ->then(function ($response) {
                if (isset($response['data']['errors']) && !empty($response['data']['errors'])) {
                    $this->respondWithError($response['data']['errors']);
                }

                return $this->mapResponse($response, CartMapper::MAPPER_NAME);
            })
            ->wait();
    }

    public function setAccount(Cart $cart, Account $account): Cart
    {
        return $cart;
    }

    public function setEmail(Cart $cart, string $email, string $locale = null): Cart
    {
        return $cart;
    }

    public function setShippingMethod(Cart $cart, string $shippingMethod, string $locale = null): Cart
    {
        return $cart;
    }

    public function setCustomField(Cart $cart, array $fields, string $locale = null): Cart
    {
        return $cart;
    }

    public function setShippingAddress(Cart $cart, Address $address, string $locale = null): Cart
    {
        // @TODO: call setDefaultShippingAddress from account api?
        return $cart;
    }

    public function setBillingAddress(Cart $cart, Address $address, string $locale = null): Cart
    {
        // @TODO: call setDefaultBillingAddress from account api?
        return $cart;
    }

    public function addPayment(Cart $cart, Payment $payment, ?array $custom = null, string $locale = null): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function redeemDiscountCode(Cart $cart, string $code, string $locale = null): Cart
    {
        $shopwareLocale = $this->parseLocaleString($locale);

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($cart->cartId)
            ->post("/checkout/cart/code/{$code}")
            ->then(function ($response) {
                if (isset($response['data']['errors']) && !empty($response['data']['errors'])) {
                    $this->respondWithError($response['data']['errors']);
                }

                return $this->mapResponse($response, CartMapper::MAPPER_NAME);
            })
            ->wait();
    }

    public function removeDiscountCode(Cart $cart, LineItem $discountLineItem, string $locale = null): Cart
    {
        return $this->removeLineItem($cart, $discountLineItem, $locale);
    }

    public function order(Cart $cart, string $locale = null): Order
    {
        $shopwareLocale = $this->parseLocaleString($locale);

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($cart->cartId)
            ->post('/checkout/order')
            ->then(function ($orderResponse) {
                return $this->mapResponse($orderResponse, OrderMapper::MAPPER_NAME);
            })
            // Make the payment, this is a separate request in Shopware6
            ->then(function (Order $order) use ($shopwareLocale) {
                $paymentResponse = $this->client
                    ->forCurrency($shopwareLocale->currencyId)
                    ->forLanguage($shopwareLocale->languageId)
                    ->withContextToken($order->cartId)
                    ->post("/checkout/order/{$order->cartId}/pay")
                    ->wait();

                return [$order, $paymentResponse];
            })
            ->then(function ($response) use ($cart) {
                [$order, $paymentResponse] = $response;

                // Fetch order again with latest data
                return $this->getOrder($order->cartId, [
                    'token' => $cart->cartId
                ]);
            })
            ->wait();
    }

    public function getOrder(string $orderId, array $parameters = []): Order
    {
        if (!isset($parameters['token'])) {
            throw new \RuntimeException(__METHOD__ . ' can not be used without token');
        }

        return $this->getOrders(
            new Account(['authTokens' => [self::TOKEN_TYPE => $parameters['token']]]), [
            'orderId' => $orderId
        ])[0];
    }

    public function getOrders(Account $account, array $parameters = []): array
    {
        $requestParameters = [
            'limit' => $parameters['limit'] ?? self::DEFAULT_ORDER_LIMIT,
            'page' => $parameters['page'] ?? self::DEFAULT_ORDER_PAGE,
        ];

        if (isset($parameters['orderId'])) {
            $parameters['filter[id]'] = $parameters['orderId'];
        }

        return $this->client
            ->withContextToken($account->getToken(self::TOKEN_TYPE))
            ->get('/customer/order?associations[lineItems][]', $requestParameters)
            ->then(function ($response) {
                return $this->mapResponse($response, OrdersMapper::MAPPER_NAME);
            })
            ->wait();
    }

    public function startTransaction(Cart $cart): void
    {
        $this->currentTransaction = $cart->cartId;
    }

    public function commit(string $locale = null): Cart
    {
        if (null === $token = $this->currentTransaction) {
            throw new \RuntimeException('No transaction currently in progress');
        }

        $this->currentTransaction = null;

        return $this->getById($token, $locale);
    }

    public function getDangerousInnerClient(): ClientInterface
    {
        return $this->client;
    }

    protected function configureMapper(DataMapperInterface $mapper): void
    {
        if ($mapper instanceof ProjectConfigApiAwareDataMapperInterface) {
            $mapper->setProjectConfigApi($this->projectConfigApi);
        }

        if ($mapper instanceof LocaleAwareDataMapperInterface) {
            $mapper->setLocale($this->locale);
        }
    }

    private function respondWithError(array $errors)
    {
        foreach ($errors as $error) {
            switch ($error['messageKey']) {
                case 'product-not-found':
                    throw new CartApi\Exception\RequestException(
                        sprintf($error['message'], $error['id'])
                    );
            }
        }
    }
}
