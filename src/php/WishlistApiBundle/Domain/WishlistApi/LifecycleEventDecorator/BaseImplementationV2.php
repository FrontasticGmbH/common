<?php

namespace Frontastic\Common\WishlistApiBundle\Domain\WishlistApi\LifecycleEventDecorator;

use Frontastic\Common\WishlistApiBundle\Domain\LineItem;
use Frontastic\Common\WishlistApiBundle\Domain\Wishlist;
use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

/**
 * Base implementation of the WishlistApi LifecycleDecorator, which should be used when writing own LifecycleDecorators
 * as base class for future type-safety and convenience reasons, as it will provide the needed function naming as well
 * as parameter type-hinting.
 *
 * The before* Methods will be obviously called *before* the original method is executed and will get all the parameters
 * handed over, which the original method will get called with. Overwriting this method can be useful if you want to
 * manipulate the handed over parameters by simply manipulating it.
 * These methods doesn't return anything.
 *
 * The after* Methods will be obviously called *after* the original method is executed and will get the unwrapped result
 * from the original method handed over. So if the original methods returns a Promise, the resolved value will be
 * handed over to this function here.
 * Overwriting this method could be useful if you want to manipulate the result.
 * These methods need to return null if nothing should be manipulating, thus will lead to the original result being
 * returned or they need to return the same data-type as the original method returns, otherwise you will get Type-Errors
 * at some point.
 *
 * In order to make this class available to the Lifecycle-Decorator, you will need to tag your service based on this
 * class with "wishlistApi.lifecycleEventListener": e.g. by adding the tag inside the `services.xml`
 * ```
 * <tag name="wishlistApi.lifecycleEventListener" />
 * ```
 */
abstract class BaseImplementationV2
{
    /*** getWishlist() ************************************************************************************************/
    public function beforeGetWishlist(WishlistApi $wishlistApi, string $wishlistId, string $locale): ?array
    {
        return null;
    }

    public function afterGetWishlist(WishlistApi $wishlistApi, Wishlist $wishlist): ?Wishlist
    {
        return null;
    }

    /*** getAnonymous() ***********************************************************************************************/
    public function beforeGetAnonymous(WishlistApi $wishlistApi, string $anonymousId, string $locale): ?array
    {
        return null;
    }

    public function afterGetAnonymous(WishlistApi $wishlistApi, Wishlist $wishlist): ?Wishlist
    {
        return null;
    }

    /*** getWishlists() ***********************************************************************************************/
    public function beforeGetWishlists(WishlistApi $wishlistApi, string $accountId, string $locale): ?array
    {
        return null;
    }

    /**
     * @param WishlistApi $wishlistApi
     * @param Wishlist[] $wishlists
     * @return Wishlist[]|null
     */
    public function afterGetWishlists(WishlistApi $wishlistApi, array $wishlists): ?array
    {
        return null;
    }

    /*** create() *****************************************************************************************************/
    public function beforeCreate(WishlistApi $wishlistApi, Wishlist $wishlist, string $locale): ?array
    {
        return null;
    }

    public function afterCreate(WishlistApi $wishlistApi, Wishlist $wishlist): ?Wishlist
    {
        return null;
    }

    /*** addToWishlist() **********************************************************************************************/
    public function beforeAddToWishlist(
        WishlistApi $wishlistApi,
        Wishlist $wishlist,
        LineItem $lineItem,
        string $locale
    ): ?array {
        return null;
    }

    public function afterAddToWishlist(WishlistApi $wishlistApi, Wishlist $wishlist): ?Wishlist
    {
        return null;
    }

    /*** addMultipleToWishlist() **************************************************************************************/
    public function beforeAddMultipleToWishlist(
        WishlistApi $wishlistApi,
        Wishlist $wishlist,
        array $lineItems,
        string $locale
    ): ?array {
        return null;
    }

    public function afterAddMultipleToWishlist(WishlistApi $wishlistApi, Wishlist $wishlist): ?Wishlist
    {
        return null;
    }

    /*** updateLineItem() *********************************************************************************************/
    public function beforeUpdateLineItem(
        WishlistApi $wishlistApi,
        Wishlist $wishlist,
        LineItem $lineItem,
        int $count,
        string $locale
    ): ?array {
        return null;
    }

    public function afterUpdateLineItem(WishlistApi $wishlistApi, Wishlist $wishlist): ?Wishlist
    {
        return null;
    }

    /*** removeLineItem() *********************************************************************************************/
    public function beforeRemoveLineItem(
        WishlistApi $wishlistApi,
        Wishlist $wishlist,
        LineItem $lineItem,
        string $locale
    ): ?array {
        return null;
    }

    public function afterRemoveLineItem(WishlistApi $wishlistApi, Wishlist $wishlist): ?Wishlist
    {
        return null;
    }
}
