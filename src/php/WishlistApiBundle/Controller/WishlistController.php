<?php

namespace Frontastic\Common\WishlistApiBundle\Controller;

use Frontastic\Common\WishlistApiBundle\Domain\Payment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

    public function getAction(Context $context): array
    {
        return [
            'wishlist' => $this->getWishlist($context),
        ];
    }

    public function getOrderAction(Context $context, string $order): array
    {
        $wishlistApi = $this->getWishlistApi($context);
        return [
            'order' => $wishlistApi->getOrder($order),
        ];
    }

    public function addAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $wishlistApi = $this->getWishlistApi($context);

        $wishlist = $this->getWishlist($context);
        $wishlistApi->startTransaction($wishlist);
        $wishlist = $wishlistApi->addToWishlist(
            $wishlist,
            new LineItem\Variant([
                'variant' => new Variant(['sku' => $payload['variant']['sku']]),
                'custom' => $payload['option'] ?: [],
                'count' => $payload['count']
            ])
        );
        $wishlist = $wishlistApi->commit();

        return [
            'wishlist' => $wishlist,
        ];
    }

    public function updateLineItemAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $wishlistApi = $this->getWishlistApi($context);

        $wishlist = $this->getWishlist($context);
        $wishlistApi->startTransaction($wishlist);
        $wishlist = $wishlistApi->updateLineItem(
            $wishlist,
            $this->getLineItem($wishlist, $payload['lineItemId']),
            $payload['count']
        );
        $wishlist = $wishlistApi->commit();

        return [
            'wishlist' => $wishlist,
        ];
    }

    public function removeLineItemAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $wishlistApi = $this->getWishlistApi($context);

        $wishlist = $this->getWishlist($context);
        $wishlistApi->startTransaction($wishlist);
        $wishlist = $wishlistApi->removeLineItem(
            $wishlist,
            $this->getLineItem($wishlist, $payload['lineItemId'])
        );
        $wishlist = $wishlistApi->commit();

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

    public function checkoutAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $wishlistApi = $this->getWishlistApi($context);

        // @TODO:
        // [ ] Create new user, if requested
        // [ ] Register for newsletter if requested

        $wishlist = $this->getWishlist($context);
        $wishlistApi->startTransaction($wishlist);
        $wishlist = $wishlistApi->setEmail(
            $wishlist,
            $payload['account']['email']
        );
        $wishlist = $wishlistApi->setShippingAddress(
            $wishlist,
            $payload['shipping']
        );
        $wishlist = $wishlistApi->setBillingAddress(
            $wishlist,
            $payload['billing'] ?: $payload['shipping']
        );
        $wishlist = $wishlistApi->setPayment(
            $wishlist,
            new Payment([
                'paymentProvider' => $payload['payment']['provider'],
                'paymentId' => $payload['payment']['id'],
                'amount' => $this->getWishlist($context)->sum,
                'currency' => $context->currency
            ])
        );
        $wishlist = $wishlistApi->commit();

        $order = $wishlistApi->order($wishlist);

        // @HACK: Regenerate session ID to get a "new" wishlist:
        session_regenerate_id();

        return [
            'order' => $order,
        ];
    }

    protected function getWishlistApi(Context $context): WishlistApi
    {
        if ($this->wishlistApi) {
            return $this->wishlistApi;
        }

        /** @var \Frontastic\Common\WishlistApiBundle\Domain\WishlistApiFactory $wishlistApiFactory */
        $wishlistApiFactory = $this->get('Frontastic\Common\WishlistApiBundle\Domain\WishlistApiFactory');
        return $this->wishlistApi = $wishlistApiFactory->factor($context->customer);
    }

    protected function getWishlist(Context $context): Wishlist
    {
        $wishlistApi = $this->getWishlistApi($context);
        if ($context->session->loggedIn) {
            return $wishlistApi->getForUser($context->session->account->accountId);
        } else {
            return $wishlistApi->getAnonymous(session_id());
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
