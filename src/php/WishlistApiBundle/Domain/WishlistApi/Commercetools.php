<?php

namespace Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

use Frontastic\Common\WishlistApiBundle\Domain\Payment;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\WishlistApiBundle\Domain\Category;
use Frontastic\Common\WishlistApiBundle\Domain\Wishlist;
use Frontastic\Common\WishlistApiBundle\Domain\LineItem;
use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

class Commercetools implements WishlistApi
{
    const EXPAND_VARIANTS = 'lineItems[*].variant';

    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client
     */
    private $client;

    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper
     */
    private $mapper;

    /**
     * Commercetools constructor.
     *
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client $client
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper $mapper
     */
    public function __construct(Client $client, Mapper $mapper)
    {
        $this->client = $client;
        $this->mapper = $mapper;
    }

    /**
     * @param string $wishlistId
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function getWishlist(string $wishlistId, string $locale): Wishlist
    {
        return $this->mapWishlist(
            $this->client->get(
                '/shopping-lists/' . $wishlistId,
                ['expand' => self::EXPAND_VARIANTS]
            ),
            Locale::createFromPosix($locale)
        );
    }

    /**
     * @param string $anonymousId
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function getAnonymous(string $anonymousId, string $locale): Wishlist
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

        return $this->mapWishlist(
            $result->results[0],
            Locale::createFromPosix($locale)
        );
    }

    /**
     * @param string $accountId
     * @param string $locale
     * @return array
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function getWishlists(string $accountId, string $locale): array
    {
        $result = $this->client->fetch(
            '/shopping-lists',
            [
                'where' => 'customer(id="' . $accountId . '")',
                'expand' => self::EXPAND_VARIANTS,
            ]
        );

        $locale = Locale::createFromPosix($locale);

        return array_map(
            function ($wishlist) use ($locale) {
                return $this->mapWishlist($wishlist, $locale);
            },
            $result->results
        );
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
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

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function addToWishlist(Wishlist $wishlist, LineItem $lineItem): Wishlist
    {
        if ($lineItem instanceof LineItem\Variant) {
            return $this->addVariantToWishlist($wishlist, $lineItem);
        }

        return $this->addCustomToWishlist($wishlist, $lineItem);
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem\Variant $lineItem
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     */
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

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     */
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

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @param int $count
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
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

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
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

    /**
     * @param array $wishlist
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    private function mapWishlist(array $wishlist, Locale $locale): Wishlist
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
            'name' => reset($wishlist['name']),
            'lineItems' => $this->mapLineItems($wishlist, $locale),
            'dangerousInnerWishlist' => $wishlist,
        ]);
    }

    /**
     * @param array $wishlist
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\LineItem[]
     */
    private function mapLineItems(array $wishlist, Locale $locale): array
    {
        $lineItems = array_merge(
            array_map(
                function (array $lineItem) use ($locale): LineItem {
                    return new LineItem\Variant([
                        'lineItemId' => $lineItem['id'],
                        'name' => reset($lineItem['name']),
                        'type' => 'variant',
                        'addedAt' => new \DateTimeImmutable($lineItem['addedAt']),
                        'variant' => $this->mapper->dataToVariant($lineItem['variant'], new Query(), $locale),
                        'count' => $lineItem['quantity'],
                        'dangerousInnerItem' => $lineItem,
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
                        'dangerousInnerItem' => $lineItem,
                    ]);
                },
                $wishlist['textLineItems']
            )
        );

        return $lineItems;
    }

    /**
     * Get *dangerous* inner client
     *
     * This method exists to enable you to use features which are not yet part
     * of the abstraction layer.
     *
     * Be aware that any usage of this method might seriously hurt backwards
     * compatibility and the future abstractions might differ a lot from the
     * vendor provided abstraction.
     *
     * Use this with care for features necessary in your customer and talk with
     * Frontastic about provising an abstraction.
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client
     */
    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
