<?php

namespace Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

use Frontastic\Common\WishlistApiBundle\Domain\Payment;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\WishlistApiBundle\Domain\Category;
use Frontastic\Common\WishlistApiBundle\Domain\Wishlist;
use Frontastic\Common\WishlistApiBundle\Domain\LineItem;
use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

class Commercetools implements WishlistApi
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function addToWishlist(Wishlist $wishlist, LineItem $lineItem): Wishlist
    {
        if ($lineItem instanceof LineItem\Variant) {
            return $this->addVariantToWishlist($wishlist, $lineItem);
        }

        return $this->addCustomToWishlist($wishlist, $lineItem);
    }

    private function addVariantToWishlist(Wishlist $wishlist, LineItem\Variant $lineItem): Wishlist
    {
        return $this->postWishlistActions(
            $wishlist,
            [
                [
                    'action' => 'addLineItem',
                    'sku' => $lineItem->variant->sku,
                    'quantity' => $lineItem->count,
                    'custom' => !$lineItem->custom ? null : [
                        'type' => $this->getCustomLineItemType(),
                        'fields' => $lineItem->custom,
                    ],
                ],
            ]
        );
    }

    private function addCustomToWishlist(Wishlist $wishlist, LineItem $lineItem): Wishlist
    {
        return $this->postWishlistActions(
            $wishlist,
            [
                [
                    'action' => 'addCustomLineItem',
                    'name' => ['de' => $lineItem->name],
                    // Must be unique inside the entire wishlist. We do not use
                    // this for anything relevant. Random seems fine for now.
                    'slug' => md5(microtime()),
                    'taxCategory' => $this->getTaxCategory(),
                    'money' => [
                        'type' => 'centPrecision',
                        'currencyCode' => 'EUR', // @TODO: Get from context
                        'centAmount' => $lineItem->totalPrice,
                    ],
                    'custom' => !$lineItem->custom ? null : [
                        'type' => $this->getCustomLineItemType(),
                        'fields' => $lineItem->custom,
                    ],
                    'quantity' => $lineItem->count,
                ],
            ]
        );
    }

    public function updateLineItem(Wishlist $wishlist, LineItem $lineItem, int $count): Wishlist
    {
        if ($lineItem instanceof LineItem\Variant) {
            return $this->postWishlistActions(
                $wishlist,
                [
                    [
                        'action' => 'changeLineItemQuantity',
                        'lineItemId' => $lineItem->lineItemId,
                        'quantity' => $count,
                    ],
                ]
            );
        } else {
            return $this->postWishlistActions(
                $wishlist,
                [
                    [
                        'action' => 'changeCustomLineItemQuantity',
                        'customLineItemId' => $lineItem->lineItemId,
                        'quantity' => $count,
                    ],
                ]
            );
        }
    }

    public function removeLineItem(Wishlist $wishlist, LineItem $lineItem): Wishlist
    {
        if ($lineItem instanceof LineItem\Variant) {
            return $this->postWishlistActions(
                $wishlist,
                [
                    [
                        'action' => 'removeLineItem',
                        'lineItemId' => $lineItem->lineItemId,
                    ],
                ]
            );
        } else {
            return $this->postWishlistActions(
                $wishlist,
                [
                    [
                        'action' => 'removeCustomLineItem',
                        'customLineItemId' => $lineItem->lineItemId,
                    ],
                ]
            );
        }
    }

    public function getWishlist(string $wishlistId): Wishlist
    {
        return $this->mapWishlist($this->client->get(
            '/shopping-lists/' . $wishlistId
        ));
    }

    public function getWishlists(string $accountId): array
    {
        $result = $this->client->fetch(
            '/shopping-lists',
            [
                'where' => 'customer(id="' . $accountId . '")',
            ]
        );

        return array_map(
            [$this, 'mapWishlist'],
            $result->results
        );
    }

    private function mapWishlist(array $wishlist): Wishlist
    {
        /**
         * @TODO:
         *
         * [ ] Map delivery costs / properties
         * [ ] Map product discounts
         * [ ] Map discount codes
         * [ ] Map tax information
         * [ ] Map discount text locales to our scheme
         */
        return new Wishlist([
            'wishlistId' => $wishlist['id'],
            'wishlistVersion' => $wishlist['version'],
            'lineItems' => $this->mapLineItems($wishlist),
            'sum' => $wishlist['totalPrice']['centAmount'],
        ]);
    }

    private function mapLineItems(array $wishlist): array
    {
        $lineItems = array_merge(
            array_map(
                function (array $lineItem): LineItem {
                    return new LineItem\Variant([
                        'lineItemId' => $lineItem['id'],
                        'name' => reset($lineItem['name']),
                        'type' => 'variant',
                        'variant' => $this->mapper->dataToVariant($lineItem['variant'], new Query(), new Locale()),
                        'custom' => $lineItem['custom']['fields'] ?? [],
                        'count' => $lineItem['quantity'],
                        'price' => $lineItem['price']['value']['centAmount'],
                        'totalPrice' => $lineItem['totalPrice']['centAmount'],
                    ]);
                },
                $wishlist['lineItems']
            ),
            array_map(
                function (array $lineItem): LineItem {
                    return new LineItem([
                        'lineItemId' => $lineItem['id'],
                        'name' => reset($lineItem['name']),
                        'type' => $lineItem['custom']['type'] ?? $lineItem['slug'],
                        'custom' => $lineItem['custom']['fields'] ?? [],
                        'count' => $lineItem['quantity'],
                        'price' => $lineItem['money']['centAmount'],
                        'totalPrice' => $lineItem['totalPrice']['centAmount'],
                    ]);
                },
                $wishlist['customLineItems']
            )
        );

        usort(
            $lineItems,
            function (LineItem $a, LineItem $b): int {
                return ($a->custom['bundleNumber'] ?? $a->name) <=>
                    ($b->custom['bundleNumber'] ?? $b->name);
            }
        );

        return $lineItems;
    }

    /**
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client
     */
    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
