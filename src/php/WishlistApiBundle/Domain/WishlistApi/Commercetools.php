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
    const EXPAND_VARIANTS = 'lineItems[*].variant';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Mapper
     */
    private $mapper;

    public function __construct(Client $client, Mapper $mapper)
    {
        $this->client = $client;
        $this->mapper = $mapper;
    }

    public function getWishlist(string $wishlistId): Wishlist
    {
        return $this->mapWishlist($this->client->get(
            '/shopping-lists/' . $wishlistId,
            ['expand' => self::EXPAND_VARIANTS]
        ));
    }

    public function getAnonymous(string $anonymousId): Wishlist
    {
        $result = $this->client->fetch(
            '/shopping-lists',
            [
                'where' => 'anonymousId="' . $anonymousId . '"',
                'expand' => self::EXPAND_VARIANTS,
            ]
        );

        if (!count($result->results)) {
            throw new \OutOfBoundsException("No wishlist exists yet.");
        }

        return $this->mapWishlist($result->results[0]);
    }

    public function getWishlists(string $accountId): array
    {
        $result = $this->client->fetch(
            '/shopping-lists',
            [
                'where' => 'customer(id="' . $accountId . '")',
                'expand' => self::EXPAND_VARIANTS,
            ]
        );

        return array_map(
            [$this, 'mapWishlist'],
            $result->results
        );
    }

    public function create(Wishlist $wishlist): Wishlist
    {
        return $this->mapWishlist($this->client->post(
            '/shopping-lists',
            ['expand' => self::EXPAND_VARIANTS],
            [],
            json_encode([
                'name' => $wishlist->name,
                'customer' => $wishlist->accountId ? ['typeId' => 'customer', 'id' => $wishlist->accountId] : null,
                'anonymousId' => $wishlist->anonymousId ?: null,
                'deleteDaysAfterLastModification' => $wishlist->anonymousId ? 31 : null,
            ])
        ));
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
        return $this->mapWishlist($this->client->post(
            '/shopping-lists/' . $wishlist->wishlistId,
            ['expand' => self::EXPAND_VARIANTS],
            [],
            json_encode([
                'version' => $wishlist->wishlistVersion,
                'actions' => [
                    [
                        'action' => 'addLineItem',
                        'sku' => $lineItem->variant->sku,
                        'quantity' => $lineItem->count,
                    ]
                ],
            ])
        ));
    }

    private function addCustomToWishlist(Wishlist $wishlist, LineItem $lineItem): Wishlist
    {
        return $this->mapWishlist($this->client->post(
            '/shopping-lists/' . $wishlist->wishlistId,
            ['expand' => self::EXPAND_VARIANTS],
            [],
            json_encode([
                'version' => $wishlist->wishlistVersion,
                'actions' => [
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
                        'quantity' => $lineItem->count,
                    ],
                ],
            ])
        ));
    }

    public function updateLineItem(Wishlist $wishlist, LineItem $lineItem, int $count): Wishlist
    {
        if ($lineItem instanceof LineItem\Variant) {
            return $this->mapWishlist($this->client->post(
                '/shopping-lists/' . $wishlist->wishlistId,
                ['expand' => self::EXPAND_VARIANTS],
                [],
                json_encode([
                    'version' => $wishlist->wishlistVersion,
                    'actions' => [
                        [
                            'action' => 'changeLineItemQuantity',
                            'lineItemId' => $lineItem->lineItemId,
                            'quantity' => $count,
                        ],
                    ],
                ])
            ));
        } else {
            return $this->mapWishlist($this->client->post(
                '/shopping-lists/' . $wishlist->wishlistId,
                ['expand' => self::EXPAND_VARIANTS],
                [],
                json_encode([
                    'version' => $wishlist->wishlistVersion,
                    'actions' => [
                        [
                            'action' => 'changeCustomLineItemQuantity',
                            'textLineItemId' => $lineItem->lineItemId,
                            'quantity' => $count,
                        ],
                    ],
                ])
            ));
        }
    }

    public function removeLineItem(Wishlist $wishlist, LineItem $lineItem): Wishlist
    {
        if ($lineItem instanceof LineItem\Variant) {
            return $this->mapWishlist($this->client->post(
                '/shopping-lists/' . $wishlist->wishlistId,
                ['expand' => self::EXPAND_VARIANTS],
                [],
                json_encode([
                    'version' => $wishlist->wishlistVersion,
                    'actions' => [
                        [
                            'action' => 'removeLineItem',
                            'lineItemId' => $lineItem->lineItemId,
                        ],
                    ],
                ])
            ));
        } else {
            return $this->mapWishlist($this->client->post(
                '/shopping-lists/' . $wishlist->wishlistId,
                ['expand' => self::EXPAND_VARIANTS],
                [],
                json_encode([
                    'version' => $wishlist->wishlistVersion,
                    'actions' => [
                        [
                            'action' => 'removeCustomLineItem',
                            'textLineItemId' => $lineItem->lineItemId,
                        ],
                    ],
                ])
            ));
        }
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
            'anonymousId' => $wishlist['anonymousId'] ?? null,
            'accountId' => $wishlist['customer']['id'] ?? null,
            'name' => $wishlist['name'] ?? [],
            'lineItems' => $this->mapLineItems($wishlist),
        ]);
    }

    private function mapLineItems(array $wishlist): array
    {
        debug($wishlist);
        $lineItems = array_merge(
            array_map(
                function (array $lineItem): LineItem {
                    return new LineItem\Variant([
                        'lineItemId' => $lineItem['id'],
                        'name' => reset($lineItem['name']),
                        'type' => 'variant',
                        'addedAt' => new \DateTimeImmutable($lineItem['addedAt']),
                        'variant' => $this->mapper->dataToVariant($lineItem['variant'], new Query(), new Locale()),
                        'count' => $lineItem['quantity'],
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
                        'addedAt' => new \DateTimeImmutable($lineItem['addedAt']),
                        'count' => $lineItem['quantity'],
                    ]);
                },
                $wishlist['textLineItems']
            )
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
