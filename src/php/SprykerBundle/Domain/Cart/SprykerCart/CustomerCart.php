<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart;

use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\LineItem\Variant;
use Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Mapper\CustomerCartMapper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\CartItemRequestDataInterface;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\CustomerCartItemRequestData;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\CustomerCartRequestData;
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

    /**
     * CustomerCart constructor.
     *
     * @param \Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface $client
     * @param \Frontastic\Common\SprykerBundle\Domain\MapperResolver $mapperResolver
     * @param \Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper $accountHelper
     * @param \Frontastic\Common\SprykerBundle\Domain\Cart\Request\CustomerCartRequestData $cartRequest
     * @param string[] $additionalIncludes
     */
    public function __construct(
        SprykerClientInterface $client,
        MapperResolver $mapperResolver,
        AccountHelper $accountHelper,
        CustomerCartRequestData $cartRequest,
        array $additionalIncludes = []
    ) {
        parent::__construct($client, $mapperResolver, $accountHelper);
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
                $this->withCurrency($url, $sprykerLocale->currency),
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
                $this->withCurrency($url, $sprykerLocale->currency),
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
