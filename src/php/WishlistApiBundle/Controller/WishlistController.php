<?php

namespace Frontastic\Common\WishlistApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use Frontastic\Common\CoreBundle\Controller\CrudController;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;
use Frontastic\Common\WishlistApiBundle\Domain\Wishlist;
use Frontastic\Common\WishlistApiBundle\Domain\LineItem;
use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;

class WishlistController extends CrudController
{
    /**
     * @var WishlistApi
     */
    protected $wishlistApi;

    public function getAction(Context $context, Request $request): array
    {
        return [
            'wishlist' => $this->getWishlist($context, $request->get('wishlist', null)),
        ];
    }

    public function addAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $wishlistApi = $this->getWishlistApi($context);

        $wishlist = $this->getWishlist($context, $request->get('wishlist', null));
        $wishlist = $wishlistApi->addToWishlist(
            $wishlist,
            new LineItem\Variant([
                'variant' => new Variant(['sku' => $payload['variant']['sku']]),
                'count' => $payload['count']
            ]),
            $context->locale
        );

        return [
            'wishlist' => $wishlist,
        ];
    }

    public function addMultipleAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);

        if (!isset($payload['lineItems']) || !is_array($payload['lineItems'])) {
            throw new BadRequestHttpException('Parameter "lineItems" in payload is not an array.');
        }

        $wishlistApi = $this->getWishlistApi($context);

        $wishlist = $this->getWishlist($context, $request->get('wishlist', null));

        $wishlistApi->startTransaction($wishlist);
        foreach (($payload['lineItems'] ?? []) as $lineItemData) {
            $wishlistApi->addToWishlist(
                $wishlist,
                new LineItem\Variant([
                    'variant' => new Variant(['sku' => $lineItemData['variant']['sku']]),
                    'count' => $lineItemData['count'] ?? 1,
                ])
            );
        }
        $wishlist = $wishlistApi->commit();

        return [
            'wishlist' => $wishlist,
        ];
    }

    public function createAction(Context $context, Request $request): Wishlist
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }

        $payload = $this->getJsonContent($request);
        $wishlistApi = $this->getWishlistApi($context);
        return $wishlistApi->create(new Wishlist([
            'name' => ['de' => $payload['name']],
            'accountId' => $context->session->account->accountId,
        ]), $context->locale);
    }

    public function updateLineItemAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $wishlistApi = $this->getWishlistApi($context);

        $wishlist = $this->getWishlist($context, $request->get('wishlist', null));
        $wishlist = $wishlistApi->updateLineItem(
            $wishlist,
            $this->getLineItem($wishlist, $payload['lineItemId']),
            $payload['count'],
            $context->locale
        );

        return [
            'wishlist' => $wishlist,
        ];
    }

    public function removeLineItemAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $wishlistApi = $this->getWishlistApi($context);

        $wishlist = $this->getWishlist($context, $request->get('wishlist', null));
        $wishlist = $wishlistApi->removeLineItem(
            $wishlist,
            $this->getLineItem($wishlist, $payload['lineItemId']),
            $context->locale
        );

        return [
            'wishlist' => $wishlist,
        ];
    }

    private function getLineItem(Wishlist $wishlist, string $lineItemId): LineItem
    {
        foreach ($wishlist->lineItems as $lineItem) {
            if ($lineItem->lineItemId === $lineItemId) {
                return $lineItem;
            }
        }

        throw new \OutOfBoundsException("Could not find line item with ID $lineItemId");
    }

    protected function getWishlistApi(Context $context): WishlistApi
    {
        if ($this->wishlistApi) {
            return $this->wishlistApi;
        }

        /** @var \Frontastic\Common\WishlistApiBundle\Domain\WishlistApiFactory $wishlistApiFactory */
        $wishlistApiFactory = $this->get('Frontastic\Common\WishlistApiBundle\Domain\WishlistApiFactory');
        return $this->wishlistApi = $wishlistApiFactory->factor($context->project);
    }

    /**
     * The idea behind this method is:
     *
     * * If a wishlist has been selected (passed $wishlistId), use that
     *
     * * If no wishlist is explicitely selected (no $wishlistId) then:
     *
     *   * If the user does not have a wishlist yet, create a default wishlist
     *
     *     * This means creating an anonymous wishlist for customers who are
     *       not logged in
     *
     *   * Select the first wishlist, if one exists
     */
    protected function getWishlist(Context $context, ?string $wishlistId): Wishlist
    {
        $wishlistApi = $this->getWishlistApi($context);

        if ($wishlistId) {
            return $wishlistApi->getWishlist($wishlistId, $context->locale);
        }

        if ($context->session->loggedIn) {
            $wishlists = $wishlistApi->getWishlists(
                $context->session->account->accountId,
                $context->locale
            );

            if (count($wishlists)) {
                return reset($wishlists);
            }

            return $wishlistApi->create(new Wishlist([
                'accountId' => $context->session->account->accountId,
                'name' => [
                    // @TODO: Use language code from locale and
                    // provide translation map
                    'de' => 'Wunschzettel',
                ],
            ]), $context->locale);
        } else {
            try {
                return $wishlistApi->getAnonymous(
                    $context->session->account->accountId,
                    $context->locale
                );
            } catch (\OutOfBoundsException $e) {
                return $wishlistApi->create(new Wishlist([
                    'anonymousId' => $context->session->account->accountId,
                    'name' => [
                        // @TODO: Use language code from locale and
                        // provide translation map
                        'de' => 'Wunschzettel',
                    ],
                ]), $context->locale);
            }
        }
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    protected function getJsonContent(Request $request)
    {
        if (!$request->getContent() ||
            !($body = json_decode($request->getContent(), true))) {
            throw new \InvalidArgumentException("Invalid data passed: " . $request->getContent());
        }

        return $body;
    }
}
