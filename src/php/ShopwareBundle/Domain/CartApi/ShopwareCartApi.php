<?php

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\CartApiBase;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\CartItemRequestDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\CartMapper;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\OrderMapper;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\OrdersMapper;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\ShippingMethodsMapper;
use Frontastic\Common\ShopwareBundle\Domain\ClientInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\ShopwareBundle\Domain\Locale\ShopwareLocale;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiInterface;
use RuntimeException;

class ShopwareCartApi extends CartApiBase
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
     * @var ClientInterface
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
     * @var string
     */
    private $currentTransaction;

    /**
     * @var ShopwareProjectConfigApiInterface
     */
    private $projectConfigApi;

    /**
     * @var string|null
     */
    private $defaultLanguage;

    public function __construct(
        ClientInterface $client,
        LocaleCreator $localeCreator,
        DataMapperResolver $mapperResolver,
        ShopwareProjectConfigApiFactory $projectConfigApiFactory,
        ?string $defaultLanguage
    ) {
        $this->client = $client;
        $this->localeCreator = $localeCreator;
        $this->mapperResolver = $mapperResolver;
        $this->projectConfigApi = $projectConfigApiFactory->factor($client);
        $this->defaultLanguage = $defaultLanguage;
    }

    protected function getForUserImplementation(Account $account, string $locale): Cart
    {
        // When user is authenticated, his cart can be retrieved by using his context token
        return $this->getById($account->apiToken, $locale);
    }

    protected function getAnonymousImplementation(string $anonymousId, string $locale): Cart
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
            ->post('/store-api/checkout/cart', [], $requestData)
            ->then(static function ($response) {
                return $response['headers']['sw-context-token'];
            })->then(function ($token) use ($locale) {
                return $this->getById($token, $locale);
            })
            ->wait();
    }

    protected function getByIdImplementation(string $token, string $locale = null): Cart
    {
        $shopwareLocale = $this->parseLocaleString($locale);
        $mapper = $this->buildMapper(CartMapper::MAPPER_NAME, $shopwareLocale);

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($token)
            ->get('/store-api/checkout/cart')
            ->then(function ($response) use ($mapper) {
                return $mapper->map($response);
            })
            ->wait();
    }

    protected function setCustomLineItemTypeImplementation(array $lineItemType): void
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function getCustomLineItemTypeImplementation(): array
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        return [];
    }

    protected function setTaxCategoryImplementation(array $taxCategory): void
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function getTaxCategoryImplementation(): ?array
    {
        return null;
    }

    protected function addToCartImplementation(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        $shopwareLocale = $this->parseLocaleString($locale);
        $mapper = $this->buildMapper(CartMapper::MAPPER_NAME, $shopwareLocale);
        $requestDataMapper = $this->buildMapper(CartItemRequestDataMapper::MAPPER_NAME, $shopwareLocale);

        $requestData['items'][] = $requestDataMapper->map($lineItem);

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($cart->cartId)
            ->post("/store-api/checkout/cart/line-item", [], $requestData)
            ->then(function ($response) use ($mapper) {
                if (isset($response['data']['errors']) && !empty($response['data']['errors'])) {
                    $this->respondWithError($response['data']['errors']);
                }

                return $mapper->map($response);
            })
            ->wait();
    }

    protected function updateLineItemImplementation(
        Cart $cart,
        LineItem $lineItem,
        int $count,
        ?array $custom = null,
        string $locale = null
    ): Cart {
        $shopwareLocale = $this->parseLocaleString($locale);
        $mapper = $this->buildMapper(CartMapper::MAPPER_NAME, $shopwareLocale);
        $requestDataMapper = $this->buildMapper(CartItemRequestDataMapper::MAPPER_NAME, $shopwareLocale);

        $item = $requestDataMapper->map($lineItem);
        $item['quantity'] = $count;

        $requestData['items'][] = $item;

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($cart->cartId)
            ->patch("/store-api/checkout/cart/line-item", [], $requestData)
            ->then(function ($response) use ($mapper) {
                if (isset($response['data']['errors']) && !empty($response['data']['errors'])) {
                    $this->respondWithError($response['data']['errors']);
                }

                return $mapper->map($response);
            })
            ->wait();
    }

    protected function removeLineItemImplementation(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        $shopwareLocale = $this->parseLocaleString($locale);
        $mapper = $this->buildMapper(CartMapper::MAPPER_NAME, $shopwareLocale);

        $requestData['ids'][] = $lineItem->lineItemId;

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($cart->cartId)
            ->delete("/store-api/checkout/cart/line-item", [], $requestData)
            ->then(function ($response) use ($mapper) {
                if (isset($response['data']['errors']) && !empty($response['data']['errors'])) {
                    $this->respondWithError($response['data']['errors']);
                }

                return $mapper->map($response);
            })
            ->wait();
    }

    protected function setEmailImplementation(Cart $cart, string $email, string $locale = null): Cart
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function setShippingMethodImplementation(Cart $cart, string $shippingMethod, string $locale = null): Cart
    {
        $shopwareLocale = $this->parseLocaleString($locale);
        $requestData = [
            'shippingMethodId' => $shippingMethod,
        ];

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($cart->cartId)
            ->patch("/store-api/context", [], $requestData)
            ->then(function ($response) {
                if (isset($response['data']['errors']) && !empty($response['data']['errors'])) {
                    $this->respondWithError($response['data']['errors']);
                }
                return $response['headers']['sw-context-token'];
            })->then(function ($token) use ($locale) {
                return $this->getById($token, $locale);
            })
            ->wait();
    }

    protected function setRawApiInputImplementation(Cart $cart, string $locale = null): Cart
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function setShippingAddressImplementation(Cart $cart, Address $address, string $locale = null): Cart
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        // but it could be set by calling set ShopwareAccountApi::setDefaultShippingAddress
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function setBillingAddressImplementation(Cart $cart, Address $address, string $locale = null): Cart
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        // but it could be set by calling set ShopwareAccountApi::setDefaultBillingAddress
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function addPaymentImplementation(
        Cart $cart,
        Payment $payment,
        ?array $custom = null,
        string $locale = null
    ): Cart {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function updatePaymentImplementation(Cart $cart, Payment $payment, string $localeString): Payment
    {
        // Standard Shopware6 SalesChannel API does not have an endpoint to handle this
        throw new RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function redeemDiscountCodeImplementation(Cart $cart, string $code, string $locale = null): Cart
    {
        $shopwareLocale = $this->parseLocaleString($locale);
        $mapper = $this->buildMapper(CartMapper::MAPPER_NAME, $shopwareLocale);

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($cart->cartId)
            ->post("/sales-channel-api/v2/checkout/cart/code/{$code}")
            ->then(function ($response) use ($mapper) {
                if (isset($response['data']['errors']) && !empty($response['data']['errors'])) {
                    $this->respondWithError($response['data']['errors']);
                }

                return $mapper->map($response);
            })
            ->wait();
    }

    protected function removeDiscountCodeImplementation(
        Cart $cart,
        LineItem $discountLineItem,
        string $locale = null
    ): Cart {
        return $this->removeLineItem($cart, $discountLineItem, $locale);
    }

    protected function orderImplementation(Cart $cart, string $locale = null): Order
    {
        if (!$cart->isReadyForCheckout()) {
            throw new \DomainException('Cart not complete yet.');
        }

        $shopwareLocale = $this->parseLocaleString($locale);
        $mapper = $this->buildMapper(OrderMapper::MAPPER_NAME, $shopwareLocale);

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($cart->cartId)
            ->post('/sales-channel-api/v2/checkout/order')
            ->then(function ($orderResponse) use ($mapper) {
                return $mapper->map($orderResponse);
            })
            ->wait();
    }

    protected function getOrderImplementation(Account $account, string $orderId, string $locale = null): Order
    {
        $result = $this->getOrdersBy(
            $account->apiToken,
            [
                'orderId' => $orderId
            ],
            $locale
        );

        return $result[0];
    }

    protected function getOrdersImplementation(Account $account, string $locale = null): array
    {
        return $this->getOrdersBy(
            $account->apiToken,
            [],
            $locale
        );
    }

    protected function startTransactionImplementation(Cart $cart): void
    {
        $this->currentTransaction = $cart->cartId;
    }

    protected function commitImplementation(string $locale = null): Cart
    {
        if (null === $token = $this->currentTransaction) {
            throw new RuntimeException('No transaction currently in progress');
        }

        $this->currentTransaction = null;

        return $this->getById($token, $locale);
    }

    public function getAvailableShippingMethodsImplementation(Cart $cart, string $localeString): array
    {
        $shopwareLocale = $this->parseLocaleString($localeString);
        $mapper = $this->buildMapper(ShippingMethodsMapper::MAPPER_NAME, $shopwareLocale);

        $requestData = [
            'onlyAvailable' => true,
            'associations' => [
                "prices" => []
            ]
        ];

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($cart->cartId)
            ->post('/store-api/shipping-method', [], $requestData)

            ->then(function ($response) use ($mapper) {
                return $mapper->map($response);
            })
            ->wait();
    }

    public function getShippingMethodsImplementation(string $localeString, bool $onlyMatching = false): array
    {
        $shopwareLocale = $this->parseLocaleString($localeString);
        $mapper = $this->buildMapper(ShippingMethodsMapper::MAPPER_NAME, $shopwareLocale);

        $requestData = [
            'onlyAvailable' => true,
            'associations' => [
                "prices" => []
            ]
        ];

        if ($onlyMatching) {
            $this->client
                ->forCurrency($shopwareLocale->currencyId)
                ->forLanguage($shopwareLocale->languageId);
        }

        return $this->client
            ->post('/store-api/shipping-method', [], $requestData)
            ->then(function ($response) use ($mapper) {
                return $mapper->map($response);
            })
            ->wait();
    }

    public function getDangerousInnerClient(): ClientInterface
    {
        return $this->client;
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
        $mapper = $this->buildMapper(OrdersMapper::MAPPER_NAME, $shopwareLocale);

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($token)
            ->get('/sales-channel-api/v2/customer/order', $requestParameters)
            ->then(function ($response) use ($mapper) {
                return $mapper->map($response);
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

    private function parseLocaleString(string $localeString): ShopwareLocale
    {
        return $this->localeCreator->createLocaleFromString($localeString ?? $this->defaultLanguage);
    }

    private function buildMapper(string $mapperName, ShopwareLocale $locale): DataMapperInterface
    {
        $mapper = $this->mapperResolver->getMapper($mapperName);
        if ($mapper instanceof LocaleAwareDataMapperInterface) {
            $mapper->setLocale($locale);
        }
        if ($mapper instanceof ProjectConfigApiAwareDataMapperInterface) {
            $mapper->setProjectConfigApi($this->projectConfigApi);
        }
        return $mapper;
    }
}
