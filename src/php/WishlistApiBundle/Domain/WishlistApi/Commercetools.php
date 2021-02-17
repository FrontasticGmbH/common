<?php

namespace Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\WishlistApiBundle\Domain\Payment;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\WishlistApiBundle\Domain\Category;
use Frontastic\Common\WishlistApiBundle\Domain\Wishlist;
use Frontastic\Common\WishlistApiBundle\Domain\LineItem;
use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;
use Frontastic\Common\CoreBundle\Domain\Json\Json;

class Commercetools implements WishlistApi
{
    const EXPAND_VARIANTS = 'lineItems[*].variant';

    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client
     */
    private $client;

    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi
     */
    private $productApi;

    /**
     * Commercetools constructor.
     *
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client $client
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi $productApi
     */
    public function __construct(Client $client, ProductApi $productApi)
    {
        $this->client = $client;
        $this->productApi = $productApi;
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
        $result = $this->client
            ->fetchAsync(
                '/shopping-lists',
                [
                    'where' => 'anonymousId="' . $anonymousId . '"',
                    'expand' => self::EXPAND_VARIANTS,
                ]
            )
            ->wait();

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
        $result = $this->client
            ->fetchAsync(
                '/shopping-lists',
                [
                    'where' => 'customer(id="' . $accountId . '")',
                    'expand' => self::EXPAND_VARIANTS,
                ]
            )
            ->wait();

        $locale = Locale::createFromPosix($locale);

        return array_map(
            function ($wishlist) use ($locale) {
                return $this->mapWishlist($wishlist, $locale);
            },
            $result->results
        );
    }

    /**
     * You can send all fields which are part of the ShoppingList specification of Commercetools
     * as $wishlist->rawApiInput.
     * @see https://docs.commercetools.com/api/projects/shoppingLists#create-shoppinglist
     *
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function create(Wishlist $wishlist, string $locale): Wishlist
    {
        return $this->mapWishlist($this->client->post(
            '/shopping-lists',
            ['expand' => self::EXPAND_VARIANTS],
            [],
            Json::encode(
                array_merge(
                    (array)$wishlist->rawApiInput,
                    [
                        'name' => $wishlist->name,
                        'customer' => $wishlist->accountId
                            ? ['typeId' => 'customer', 'id' => $wishlist->accountId]
                            : null,
                        'anonymousId' => $wishlist->anonymousId ?: null,
                        'deleteDaysAfterLastModification' => $wishlist->anonymousId ? 31 : null,
                    ]
                )
            )
        ), Locale::createFromPosix($locale));
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function addToWishlist(Wishlist $wishlist, LineItem $lineItem, string $locale): Wishlist
    {
        return $this->addMultipleToWishlist($wishlist, [$lineItem], $locale);
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param array $lineItems
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function addMultipleToWishlist(Wishlist $wishlist, array $lineItems, string $locale): Wishlist
    {
        $actions = [];
        foreach ($lineItems as $lineItem) {
            $actions[] = ($lineItem instanceof LineItem\Variant) ?
                $this->addVariantToWishlist($lineItem, $locale) :
                $this->addCustomToWishlist($lineItem, $locale);
        }

        return $this->mapWishlist(
            $this->client->post(
                '/shopping-lists/' . $wishlist->wishlistId,
                ['expand' => self::EXPAND_VARIANTS],
                [],
                Json::encode([
                    'version' => $wishlist->wishlistVersion,
                    'actions' => $actions,
                ])
            ),
            Locale::createFromPosix($locale)
        );
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem\Variant $lineItem
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     */
    private function addVariantToWishlist(LineItem\Variant $lineItem, string $locale): array
    {
        return [
            'action' => 'addLineItem',
            'sku' => $lineItem->variant->sku,
            'quantity' => $lineItem->count,
        ];
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     */
    private function addCustomToWishlist(LineItem $lineItem, string $locale): array
    {
        return [
            'action' => 'addCustomLineItem',
            'name' => ['de' => $lineItem->name],
            // Must be unique inside the entire wishlist. We do not use
            // this for anything relevant. Random seems fine for now.
            'slug' => md5(microtime()),
            'money' => [
                'type' => 'centPrecision',
                'currencyCode' => 'EUR', // @TODO: Get from context
                'centAmount' => $lineItem->totalPrice,
            ],
            'quantity' => $lineItem->count,
        ];
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @param int $count
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function updateLineItem(Wishlist $wishlist, LineItem $lineItem, int $count, string $locale): Wishlist
    {
        if ($lineItem instanceof LineItem\Variant) {
            return $this->mapWishlist($this->client->post(
                '/shopping-lists/' . $wishlist->wishlistId,
                ['expand' => self::EXPAND_VARIANTS],
                [],
                Json::encode([
                    'version' => $wishlist->wishlistVersion,
                    'actions' => [
                        [
                            'action' => 'changeLineItemQuantity',
                            'lineItemId' => $lineItem->lineItemId,
                            'quantity' => $count,
                        ],
                    ],
                ])
            ), Locale::createFromPosix($locale));
        } else {
            return $this->mapWishlist($this->client->post(
                '/shopping-lists/' . $wishlist->wishlistId,
                ['expand' => self::EXPAND_VARIANTS],
                [],
                Json::encode([
                    'version' => $wishlist->wishlistVersion,
                    'actions' => [
                        [
                            'action' => 'changeCustomLineItemQuantity',
                            'textLineItemId' => $lineItem->lineItemId,
                            'quantity' => $count,
                        ],
                    ],
                ])
            ), Locale::createFromPosix($locale));
        }
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function removeLineItem(Wishlist $wishlist, LineItem $lineItem, string $locale): Wishlist
    {
        if ($lineItem instanceof LineItem\Variant) {
            return $this->mapWishlist($this->client->post(
                '/shopping-lists/' . $wishlist->wishlistId,
                ['expand' => self::EXPAND_VARIANTS],
                [],
                Json::encode([
                    'version' => $wishlist->wishlistVersion,
                    'actions' => [
                        [
                            'action' => 'removeLineItem',
                            'lineItemId' => $lineItem->lineItemId,
                        ],
                    ],
                ])
            ), Locale::createFromPosix($locale));
        } else {
            return $this->mapWishlist($this->client->post(
                '/shopping-lists/' . $wishlist->wishlistId,
                ['expand' => self::EXPAND_VARIANTS],
                [],
                Json::encode([
                    'version' => $wishlist->wishlistVersion,
                    'actions' => [
                        [
                            'action' => 'removeCustomLineItem',
                            'textLineItemId' => $lineItem->lineItemId,
                        ],
                    ],
                ])
            ), Locale::createFromPosix($locale));
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
        $wishlistVariantMap = $this->fetchWishlistVariantMap($wishlist, $locale);

        $lineItems = array_values(array_filter(
            array_map(
                function (array $lineItem) use ($wishlistVariantMap): LineItem {
                    return new LineItem\Variant([
                        'lineItemId' => $lineItem['id'],
                        'name' => reset($lineItem['name']),
                        'type' => 'variant',
                        'addedAt' => new \DateTimeImmutable($lineItem['addedAt']),
                        'variant' => !empty($lineItem['variant'])
                            ? ($wishlistVariantMap[$lineItem['variant']['sku']] ?? null)
                            : null,
                        'count' => $lineItem['quantity'],
                        'dangerousInnerItem' => $lineItem,
                    ]);
                },
                $wishlist['lineItems']
            ),
            function (LineItem $lineItem): bool {
                return (bool) $lineItem->variant;
            }
        ));

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

    /**
     * @param array $wishlist
     * @param Locale $locale
     * @return array
     */
    private function fetchWishlistVariantMap(array $wishlist, Locale $locale): array
    {
        $wishlistVariantSkus = [];
        foreach ($wishlist['lineItems'] as $rawLineItem) {
            if (isset($rawLineItem['variant']) && isset($rawLineItem['variant']['sku'])) {
                $wishlistVariantSkus[] = $rawLineItem['variant']['sku'];
            }
        }
        $query = new Query\ProductQuery(['locale' => $locale->original]);
        $query->skus = array_unique($wishlistVariantSkus);

        $wishlistProducts = $this->productApi->query($query);

        $wishlistVariantMap = [];
        /* @var Product $wishlistProduct */
        foreach ($wishlistProducts->items as $wishlistProduct) {
            foreach ($wishlistProduct->variants as $wishlistVariant) {
                if (in_array($wishlistVariant->sku, $wishlistVariantSkus)) {
                    $wishlistVariantMap[$wishlistVariant->sku] = $wishlistVariant;
                }
            }
        }
        return $wishlistVariantMap;
    }
}
