<?php

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\CartApiBase;
use Frontastic\Common\CartApiBundle\Domain\CartCheckoutService;
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
     * @var AccountApi
     */
    private $accountApi;

    /**
     * @var string|null
     */
    private $defaultLanguage;

    /**
     * @var CartCheckoutService
     */
    private $cartCheckoutService;

    public function __construct(
        ClientInterface $client,
        LocaleCreator $localeCreator,
        DataMapperResolver $mapperResolver,
        ShopwareProjectConfigApiFactory $projectConfigApiFactory,
        AccountApi $accountApi,
        ?string $defaultLanguage,
        ?CartCheckoutService $cartCheckoutService = null
    ) {
        $this->client = $client;
        $this->localeCreator = $localeCreator;
        $this->mapperResolver = $mapperResolver;
        $this->projectConfigApi = $projectConfigApiFactory->factor($client);
        $this->accountApi = $accountApi;
        $this->defaultLanguage = $defaultLanguage;
        $this->cartCheckoutService = $cartCheckoutService ?? null;
    }

    protected function getForUserImplementation(Account $account, string $locale): Cart
    {
        // When user is authenticated, his cart can be retrieved by using his context token
        return $this->getById($account->apiToken, $locale);
    }

    protected function getAnonymousImplementation(string $anonymousId, string $locale): Cart
    {
        $shopwareLocale = $this->parseLocaleString($locale);
        $mapper = $this->buildMapper(CartMapper::MAPPER_NAME, $shopwareLocale);

        $requestData = [
            'name' => self::CART_NAME_GUEST . '-' . $anonymousId,
        ];

        // When user is anonymous, the cart need to be initialized first, and then returned using the context token
        // returned by the init procedure
        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->post('/store-api/checkout/cart', [], $requestData)
            ->then(static function ($response) use ($mapper) {
                return $mapper->map($response);
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
            ->then(function ($response) use ($mapper, $token) {
                // TODO: improve request to get context in concurrency as done in ShopwareProjectConfigApi
                $context = $this->getContext($token);

                return $mapper->map(array_merge($context, $response));
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
        // Shopware links the email to the context.customer and not to the cart. In order to update the email,
        // the account should be updated through Account::update().
        if (!$this->isGuestCart($cart)) {
            throw new \DomainException(
                sprintf(
                    'To set the email "%s", you should update the account details. Current email used "%s".',
                    $email,
                    $cart->email
                )
            );
        }

        $addresses = [];

        if (!empty($cart->shippingAddress)) {
            $cart->shippingAddress->isDefaultShippingAddress = true;
            $addresses[] = $cart->shippingAddress;
        }

        if (!empty($cart->billingAddress)) {
            $cart->billingAddress->isDefaultBillingAddress = true;
            $addresses[] = $cart->billingAddress;
        }

        // For a guest account, Shopware ignores the shipping address salutation, firstName, and lastName,
        // instead it uses the guest account values.
        $account = new Account([
            'apiToken' => $cart->cartId,
            'salutation' => $cart->billingAddress->salutation ?? null,
            'firstName' => $cart->billingAddress->firstName ?? null,
            'lastName' => $cart->billingAddress->lastName ?? null,
            'email' => $email,
            'addresses' => $addresses,
        ]);

        $account = $this->accountApi->create($account, $cart, $locale);
        $cart = $this->getById($account->apiToken, $locale);

        // The token changes for a guest account, so we always update the $currentTransaction.
        $this->currentTransaction = $cart->cartId;

        return $cart;
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
        if (!$this->isGuestCart($cart)) {
            throw new \DomainException('To set the shipping address you should update the account details.');
        }

        $addresses = [];

        if (!empty($cart->shippingAddress)) {
            $cart->shippingAddress->isDefaultShippingAddress = false;
            $addresses[] = $cart->shippingAddress;
        }

        if (!empty($cart->billingAddress)) {
            $cart->billingAddress->isDefaultBillingAddress = true;
            $addresses[] = $cart->billingAddress;
        }

        $address->isDefaultShippingAddress = true;
        $addresses[] = $address;

        // For a guest account, Shopware ignores the shipping address salutation, firstName, and lastName,
        // instead it uses the guest account values.
        $account = new Account([
            'apiToken' => $cart->cartId,
            'salutation' => $cart->billingAddress->salutation ?? null,
            'firstName' => $cart->billingAddress->firstName ?? null,
            'lastName' => $cart->billingAddress->lastName ?? null,
            'email' => $cart->email ?? self::CART_NAME_GUEST . '-' . uniqid('', true) . '@frontastic.com',
            'addresses' => $addresses,
        ]);

        $account = $this->accountApi->create($account, $cart, $locale);

        $cart = $this->getById($account->apiToken, $locale);

        // For a guest account, the token changes, so we need to update $currentTransaction.
        $this->currentTransaction = $cart->cartId;

        return $cart;
    }

    protected function setBillingAddressImplementation(Cart $cart, Address $address, string $locale = null): Cart
    {
        if (!$this->isGuestCart($cart)) {
            throw new \DomainException('To set the billing address you should update the account details.');
        }

        $addresses = [];

        if (!empty($cart->shippingAddress)) {
            $cart->shippingAddress->isDefaultShippingAddress = true;
            $addresses[] = $cart->shippingAddress;
        }

        if (!empty($cart->billingAddress)) {
            $cart->billingAddress->isDefaultBillingAddress = false;
            $addresses[] = $cart->billingAddress;
        }

        $address->isDefaultBillingAddress = true;
        $addresses[] = $address;

        // For a guest account, Shopware ignores the shipping address salutation, firstName, and lastName,
        // instead it uses the guest account values.
        $account = new Account([
            'apiToken' => $cart->cartId,
            'salutation' => $address->salutation,
            'firstName' => $address->firstName,
            'lastName' => $address->lastName,
            'email' => $cart->email ?? self::CART_NAME_GUEST . '-' . uniqid('', true) . '@frontastic.com',
            'addresses' => $addresses,
        ]);

        $account = $this->accountApi->create($account, $cart, $locale);

        $cart = $this->getById($account->apiToken, $locale);

        // For a guest account, the token changes, so we need to update $currentTransaction.
        $this->currentTransaction = $cart->cartId;

        return $cart;
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

        $requestData['items'][] = [
            'type' => ShopwareCartApi::LINE_ITEM_TYPE_PROMOTION,
            'referencedId' => $code,
        ];

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

    protected function removeDiscountCodeImplementation(
        Cart $cart,
        LineItem $discountLineItem,
        string $locale = null
    ): Cart {
        return $this->removeLineItem($cart, $discountLineItem, $locale);
    }

    protected function orderImplementation(Cart $cart, string $locale = null): Order
    {
        if (!$this->isReadyForCheckout($cart)) {
            throw new \DomainException('Cart not complete yet.');
        }

        // Shopware requires an email when a shipping address is set. For guest customer we use the CART_NAME_GUEST
        // as email, but the customer needs to set a valid email before the order is created.
        if (substr($cart->email, 0, strlen(self::CART_NAME_GUEST)) === self::CART_NAME_GUEST) {
            throw new \DomainException('Cart not complete yet. Email needs to be provided');
        }

        $shopwareLocale = $this->parseLocaleString($locale);
        $mapper = $this->buildMapper(OrderMapper::MAPPER_NAME, $shopwareLocale);

        /**
         * The Prepared Payment flow in Shopware allows to make a payment before place an order. When the order
         * is processed, Shopware expects the payment data as a checkout/order request payload. To allow this,
         * we are passing the rawApiInput as a request payload. Shopware will accept any data passed since
         * this can't be defined beforehand and will validate only the following fields:
         *
         * {
         *  "customerComment": "string",
         *  "affiliateCode": "string",
         *  "campaignCode": "string"
         * }
         */

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($cart->cartId)
            ->post('/store-api/checkout/order', [], $cart->rawApiInput)
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
        $requestParameters = [
            'limit' => $parameters['limit'] ?? self::DEFAULT_ORDER_LIMIT,
            'page' => $parameters['page'] ?? self::DEFAULT_ORDER_PAGE,
            'associations' => [
                'lineItems' => [],
                'addresses' => [],
                'deliveries' => [],
            ]
        ];

        if (isset($parameters['orderId'])) {
            $requestParameters['filter[id]'] = $parameters['orderId'];
        }

        $shopwareLocale = $this->parseLocaleString($locale);
        $mapper = $this->buildMapper(OrdersMapper::MAPPER_NAME, $shopwareLocale);

        return $this->client
            ->forCurrency($shopwareLocale->currencyId)
            ->forLanguage($shopwareLocale->languageId)
            ->withContextToken($token)
            ->post('/store-api/order', [], $requestParameters)
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

    protected function getContext(string $token): array
    {
        return $this->client
            ->withContextToken($token)
            ->get('/store-api/context')
            ->then(static function ($response) {
                return $response;
            })
            ->wait();
    }

    protected function isGuestCart(Cart $cart): bool
    {
        $context = $this->getContext($cart->cartId);

        // If it's not a guest account but the email needs confirmation, the context customer will not exist.
        return ($context['customer'] === null || $context['customer']['guest'] === true);
    }

    private function isReadyForCheckout(Cart $cart): bool
    {
        if ($this->cartCheckoutService !== null) {
            return $this->cartCheckoutService->isReadyForCheckout($cart);
        }

        return $cart->isReadyForCheckout();
    }
}
