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
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory;
use RuntimeException;

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
        string $defaultLanguage,
        ShopwareProjectConfigApiFactory $projectConfigApiFactory
    ) {
        parent::__construct($client, $mapperResolver, $localeCreator, $defaultLanguage);

        $this->projectConfigApi = $projectConfigApiFactory->factor($client);
    }

    public function getForUser(Account $account, string $locale): Cart
    {
        // When user is authenticated, his cart can be retrieved by using his context token
        return $this->getById($account->authToken, $locale);
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
            ->post('/sales-channel-api/v2/checkout/cart', [], $requestData)
            ->then(static function ($response) {
                return $response['sw-context-token'];
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
            ->get('/sales-channel-api/v2/checkout/cart')
            ->then(function ($response) {
                return $this->mapResponse($response, CartMapper::MAPPER_NAME);
            })
            ->wait();
    }

    public function setCustomLineItemType(array $lineItemType): void
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getCustomLineItemType(): array
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        return [];
    }

    public function setTaxCategory(array $taxCategory): void
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException(__METHOD__ . ' not implemented');
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
            ->post("/sales-channel-api/v2/checkout/cart/line-item/{$id}", [], $requestData)
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
            ->patch("/sales-channel-api/v2/checkout/cart/line-item/{$id}", [], $requestData)
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
            ->delete("/sales-channel-api/v2/checkout/cart/line-item/{$lineItem->lineItemId}")
            ->then(function ($response) {
                if (isset($response['data']['errors']) && !empty($response['data']['errors'])) {
                    $this->respondWithError($response['data']['errors']);
                }

                return $this->mapResponse($response, CartMapper::MAPPER_NAME);
            })
            ->wait();
    }

    public function setEmail(Cart $cart, string $email, string $locale = null): Cart
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setShippingMethod(Cart $cart, string $shippingMethod, string $locale = null): Cart
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setCustomField(Cart $cart, array $fields, string $locale = null): Cart
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setRawApiInput(Cart $cart, string $locale = null): Cart
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setShippingAddress(Cart $cart, Address $address, string $locale = null): Cart
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        // but it could be set by calling set ShopwareAccountApi::setDefaultShippingAddress
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setBillingAddress(Cart $cart, Address $address, string $locale = null): Cart
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        // but it could be set by calling set ShopwareAccountApi::setDefaultBillingAddress
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    public function addPayment(Cart $cart, Payment $payment, ?array $custom = null, string $locale = null): Cart
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    public function updatePayment(Cart $cart, Payment $payment, string $localeString): Payment
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    public function redeemDiscountCode(Cart $cart, string $code, string $locale = null): Cart
    {
        $shopwareLocale = $this->parseLocaleString($locale);

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($cart->cartId)
            ->post("/sales-channel-api/v2/checkout/cart/code/{$code}")
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
            ->post('/sales-channel-api/v2/checkout/order')
            ->then(function ($orderResponse) {
                return $this->mapResponse($orderResponse, OrderMapper::MAPPER_NAME);
            })
            ->wait();
    }

    public function getOrder(Account $account, string $orderId, string $locale = null): Order
    {
        $result = $this->getOrdersBy(
            $account->authToken,
            [
                'orderId' => $orderId
            ],
            $locale
        );

        return $result[0];
    }

    public function getOrders(Account $account, string $locale = null): array
    {
        return $this->getOrdersBy(
            $account->authToken,
            [],
            $locale
        );
    }

    public function startTransaction(Cart $cart): void
    {
        $this->currentTransaction = $cart->cartId;
    }

    public function commit(string $locale = null): Cart
    {
        if (null === $token = $this->currentTransaction) {
            throw new RuntimeException('No transaction currently in progress');
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
        parent::configureMapper($mapper);

        if ($mapper instanceof ProjectConfigApiAwareDataMapperInterface) {
            $mapper->setProjectConfigApi($this->projectConfigApi);
        }
    }

    /**
     * @param string $token
     * @param array $parameters
     * @param string|null $locale
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\Order[]
     */
    private function getOrdersBy(string $token, array $parameters = [], ?string $locale = null): array
    {
        $requestParameters = [];
//        @TODO: could be uncommented once there will be a way to pass limit and page parameters
//        $requestParameters = [
//            'limit' => $parameters['limit'] ?? self::DEFAULT_ORDER_LIMIT,
//            'page' => $parameters['page'] ?? self::DEFAULT_ORDER_PAGE,
//        ];

        if (isset($parameters['orderId'])) {
            $requestParameters['filter[id]'] = $parameters['orderId'];
        }

        $shopwareLocale = $this->parseLocaleString($locale);

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($token)
            ->get('/sales-channel-api/v2/customer/order', $requestParameters)
            ->then(function ($response) {
                return $this->mapResponse($response, OrdersMapper::MAPPER_NAME);
            })
            ->wait();
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
