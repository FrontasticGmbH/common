<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart;

use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\LineItem\Variant;
use Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Mapper\CustomerCartMapper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\CartItemRequestDataInterface;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\CustomerCartItemRequestData;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\CustomerCartRequestData;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\VoucherRedeemRequestData;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;

class CustomerCart extends AbstractSprykerCart
{
    protected const CART_MAPPER_NAME = CustomerCartMapper::MAPPER_NAME;

    /**
     * @var \Frontastic\Common\SprykerBundle\Domain\Cart\Request\CustomerCartRequestData
     */
    private $cartRequest;

    /**
     * @var string[]
     */
    private $customerCartIncludes;

    public function __construct(
        SprykerClientInterface $client,
        MapperResolver $mapperResolver,
        LocaleCreator $localeCreator,
        AccountHelper $accountHelper,
        CustomerCartRequestData $cartRequest,
        array $additionalIncludes = [],
        ?string $defaultLanguage = null
    ) {
        parent::__construct($client, $mapperResolver, $localeCreator, $accountHelper, $defaultLanguage);
        $this->cartRequest = $cartRequest;
        $this->customerCartIncludes = array_merge(
            SprykerCartConstants::CUSTOMER_CART_RELATIONSHIPS,
            SprykerCartConstants::COMMON_CART_RELATIONSHIPS,
            $additionalIncludes
        );
    }

    public function getById(string $cartId, string $locale = null): Cart
    {
        $sprykerLocale = $this->parseLocaleString($locale);

        $url = $this->withIncludes("/carts/{$cartId}", $this->customerCartIncludes);

        $response = $this->client
            ->forLanguage($sprykerLocale->language)
            ->get(
                $this->appendCurrencyToUrl($url, $sprykerLocale->currency),
                $this->getAuthHeader()
            );

        if ($response->document()->hasAnyPrimaryResources()) {
            return $this->mapResponseToCart($response);
        }

        return $this->createNewCart();
    }

    /**
     * @param string|null $id
     * @param string|null $locale
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function getCart(?string $id = null, ?string $locale = null): Cart
    {
        $sprykerLocale = $this->parseLocaleString($locale);

        $url = $this->withIncludes('/carts', $this->customerCartIncludes);

        $response = $this->client
            ->forLanguage($sprykerLocale->language)
            ->get(
                $this->appendCurrencyToUrl($url, $sprykerLocale->currency),
                $this->getAuthHeader()
            );

        if ($response->document()->hasAnyPrimaryResources()) {
            return $this->mapResponseToCart($response);
        }

        return $this->createNewCart();
    }

    /**
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function createNewCart(): Cart
    {
        $response = $this->client->post(
            $this->withIncludes('/carts', $this->customerCartIncludes),
            $this->getAuthHeader(),
            $this->cartRequest->encode()
        );

        return $this->mapResponseToCart($response);
    }

    /**
     * @param Cart $cart
     * @param Variant $lineItem
     *
     * @return Cart
     */
    public function addLineItemToCart(Cart $cart, Variant $lineItem): Cart
    {
        $url = $this->withIncludes("/carts/{$cart->cartId}/items", $this->customerCartIncludes);

        return $this->lineItemAction($url, $lineItem);
    }

    /**
     * @param Cart $cart
     * @param Variant $lineItem
     * @param int $count
     *
     * @return Cart
     */
    public function updateLineItem(Cart $cart, Variant $lineItem, int $count): Cart
    {
        $sku = $lineItem->variant->sku;
        $url = $this->withIncludes("/carts/{$cart->cartId}/items/{$sku}", $this->customerCartIncludes);

        return $this->lineItemAction($url, $lineItem, $count, 'patch');
    }

    /**
     * @param Cart $cart
     * @param Variant $lineItem
     *
     * @return Cart
     */
    public function removeLineItem(Cart $cart, Variant $lineItem): Cart
    {
        $this->client->delete(
            "/carts/{$cart->cartId}/items/{$lineItem->variant->sku}",
            $this->getAuthHeader()
        );

        return $this->getCart();
    }

    public function redeemDiscount(Cart $cart, string $code, string $locale = null): Cart
    {
        $sprykerLocale = $this->parseLocaleString($locale);

        $url = $this->withIncludes(
            "/carts/{$cart->cartId}/vouchers",
            $this->customerCartIncludes,
        );

        $request = new VoucherRedeemRequestData($code);

        $response = $this->client
            ->forLanguage($sprykerLocale->language)
            ->post(
                $this->appendCurrencyToUrl($url, $sprykerLocale->currency),
                $this->getAuthHeader(),
                $request->encode()
            );

        return $this->mapResponseToCart($response);
    }

    public function removeDiscount(Cart $cart, LineItem $discountLineItem, string $locale = null): Cart
    {
        $sprykerLocale = $this->parseLocaleString($locale);

        $url = $this->withIncludes(
            "/carts/{$cart->cartId}/vouchers/{$discountLineItem->lineItemId}",
            $this->customerCartIncludes,
        );

        $this->client
            ->forLanguage($sprykerLocale->language)
            ->delete(
                $this->appendCurrencyToUrl($url, $sprykerLocale->currency),
                $this->getAuthHeader()
            );

        return $this->getById($cart->cartId, $locale);
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem\Variant $lineItem
     * @param int|null $count
     *
     * @return \Frontastic\Common\SprykerBundle\Domain\Cart\Request\CartItemRequestDataInterface
     */
    protected function createCartItemRequestData(Variant $lineItem, ?int $count = null): CartItemRequestDataInterface
    {
        $count = $count ?? $lineItem->count;

        return new CustomerCartItemRequestData($lineItem->variant->sku, $count);
    }
}
