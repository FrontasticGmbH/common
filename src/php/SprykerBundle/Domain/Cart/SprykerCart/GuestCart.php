<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart;

use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\LineItem\Variant;
use Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Mapper\GuestCartMapper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\CartItemRequestDataInterface;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\GuestCartItemRequestData;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;

class GuestCart extends AbstractSprykerCart
{
    protected const CART_MAPPER_NAME = GuestCartMapper::MAPPER_NAME;

    /**
     * @var string[]
     */
    private $guestCartIncludes;

    /**
     * GuestCart constructor.
     *
     * @param \Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface $client
     * @param \Frontastic\Common\SprykerBundle\Domain\MapperResolver $mapperResolver
     * @param \Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper $accountHelper
     * @param string[] $additionalIncludes
     */
    public function __construct(
        SprykerClientInterface $client,
        MapperResolver $mapperResolver,
        AccountHelper $accountHelper,
        array $additionalIncludes = []
    ) {
        parent::__construct($client, $mapperResolver, $accountHelper);
        $this->guestCartIncludes = array_merge(
            SprykerCartConstants::GUEST_CART_RELATIONSHIPS,
            SprykerCartConstants::COMMON_CART_RELATIONSHIPS,
            $additionalIncludes
        );
    }

    public function getById(string $cartId, string $locale = null): Cart
    {
        $response = $this->client->get(
            $this->withIncludes("/guest-carts/{cartId}", $this->guestCartIncludes),
            $this->accountHelper->getAnonymousHeader($cartId)
        );

        if ($response->document()->hasAnyPrimaryResources()) {
            return $this->mapResponseToCart($response);
        }

        return new Cart([
            'cartId' => $cartId,
            'cartVersion' => '1',
            // @TODO: Get currency from locale
            'currency' => 'EUR'
        ]);
    }

    /**
     * @param string|null $id
     *
     * @param string|null $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function getCart(?string $id = null, ?string $locale = null): Cart
    {
        $response = $this->client->get(
            $this->withIncludes('/guest-carts', $this->guestCartIncludes),
            $this->accountHelper->getAnonymousHeader($id)
        );

        if ($response->document()->hasAnyPrimaryResources()) {
            return $this->mapResponseToCart($response);
        }

        return new Cart();
    }

    /**
     * @param Cart $cart
     * @param Variant $lineItem
     *
     * @return Cart
     */
    public function addLineItemToCart(Cart $cart, Variant $lineItem): Cart
    {
        $url = $this->withIncludes('/guest-cart-items', $this->guestCartIncludes);

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
        $url = $this->withIncludes("/guest-carts/{$cart->cartId}/guest-cart-items/{$lineItem->variant->sku}", $this->guestCartIncludes);

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
            "/guest-carts/{$cart->cartId}/guest-cart-items/{$lineItem->variant->sku}",
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

        return new GuestCartItemRequestData($lineItem->variant->sku, $count);
    }
}
