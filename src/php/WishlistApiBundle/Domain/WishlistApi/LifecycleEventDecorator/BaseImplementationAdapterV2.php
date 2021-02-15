<?php

namespace Frontastic\Common\WishlistApiBundle\Domain\WishlistApi\LifecycleEventDecorator;

use Frontastic\Common\WishlistApiBundle\Domain\LineItem;
use Frontastic\Common\WishlistApiBundle\Domain\Wishlist;
use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

class BaseImplementationAdapterV2 extends BaseImplementationV2
{
    /**
     * @var BaseImplementation
     */
    private $baseImplementation;

    public function __construct(BaseImplementation $baseImplementation)
    {
        $this->baseImplementation = $baseImplementation;
    }

    public function beforeGetWishlist(WishlistApi $wishlistApi, string $wishlistId, string $locale): ?array
    {
        $this->baseImplementation->beforeGetWishlist($wishlistApi, $wishlistId, $locale);
        return [$wishlistId, $locale];
    }

    public function afterGetWishlist(WishlistApi $wishlistApi, Wishlist $wishlist): ?Wishlist
    {
        return $this->baseImplementation->afterGetWishlist($wishlistApi, $wishlist);
    }

    public function beforeGetAnonymous(WishlistApi $wishlistApi, string $anonymousId, string $locale): ?array
    {
        $this->baseImplementation->beforeGetAnonymous($wishlistApi, $anonymousId, $locale);
        return [$anonymousId, $locale];
    }

    public function afterGetAnonymous(WishlistApi $wishlistApi, Wishlist $wishlist): ?Wishlist
    {
        return $this->baseImplementation->afterGetAnonymous($wishlistApi, $wishlist);
    }

    public function beforeGetWishlists(WishlistApi $wishlistApi, string $accountId, string $locale): ?array
    {
        $this->baseImplementation->beforeGetWishlists($wishlistApi, $accountId, $locale);
        return [$accountId, $locale];
    }

    /**
     * @param WishlistApi $wishlistApi
     * @param Wishlist[] $wishlists
     * @return Wishlist[]|null
     */
    public function afterGetWishlists(WishlistApi $wishlistApi, array $wishlists): ?array
    {
        return $this->baseImplementation->afterGetWishlists($wishlistApi, $wishlists);
    }

    public function beforeCreate(WishlistApi $wishlistApi, Wishlist $wishlist, string $locale): ?array
    {
        $this->baseImplementation->beforeCreate($wishlistApi, $wishlist, $locale);
        return [$wishlist, $locale];
    }

    public function afterCreate(WishlistApi $wishlistApi, Wishlist $wishlist): ?Wishlist
    {
        return $this->baseImplementation->afterCreate($wishlistApi, $wishlist);
    }

    public function beforeAddToWishlist(
        WishlistApi $wishlistApi,
        Wishlist $wishlist,
        LineItem $lineItem,
        string $locale
    ): ?array {
        $this->baseImplementation->beforeAddToWishlist($wishlistApi, $wishlist, $lineItem, $locale);
        return [$wishlist, $lineItem,$locale];
    }

    public function afterAddToWishlist(WishlistApi $wishlistApi, Wishlist $wishlist): ?Wishlist
    {
        return $this->baseImplementation->afterAddToWishlist($wishlistApi, $wishlist);
    }

    public function beforeAddMultipleToWishlist(
        WishlistApi $wishlistApi,
        Wishlist $wishlist,
        array $lineItems,
        string $locale
    ): ?array {
        $this->baseImplementation->beforeAddMultipleToWishlist($wishlistApi, $wishlist, $lineItems, $locale);
        return [$wishlist, $lineItems, $locale];
    }

    public function afterAddMultipleToWishlist(WishlistApi $wishlistApi, Wishlist $wishlist): ?Wishlist
    {
        return $this->baseImplementation->afterAddMultipleToWishlist($wishlistApi, $wishlist);
    }

    public function beforeUpdateLineItem(
        WishlistApi $wishlistApi,
        Wishlist $wishlist,
        LineItem $lineItem,
        int $count,
        string $locale
    ): ?array {
        $this->baseImplementation->beforeUpdateLineItem($wishlistApi, $wishlist, $lineItem, $count, $locale);
        return [$wishlist, $lineItem, $count, $locale];
    }

    public function afterUpdateLineItem(WishlistApi $wishlistApi, Wishlist $wishlist): ?Wishlist
    {
        return $this->baseImplementation->afterUpdateLineItem($wishlistApi, $wishlist);
    }

    /*** removeLineItem() *********************************************************************************************/
    public function beforeRemoveLineItem(
        WishlistApi $wishlistApi,
        Wishlist $wishlist,
        LineItem $lineItem,
        string $locale
    ): ?array {
        $this->baseImplementation->beforeRemoveLineItem($wishlistApi, $wishlist, $lineItem, $locale);
        return [$wishlist, $lineItem, $locale];
    }

    public function afterRemoveLineItem(WishlistApi $wishlistApi, Wishlist $wishlist): ?Wishlist
    {
        return $this->baseImplementation->afterRemoveLineItem($wishlistApi, $wishlist);
    }
}
