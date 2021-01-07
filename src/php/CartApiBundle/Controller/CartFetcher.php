<?php

namespace Frontastic\Common\CartApiBundle\Controller;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class CartFetcher
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CartApi
     */
    private $cartApi;

    public function __construct(CartApi $cartApi, LoggerInterface $logger)
    {
         $this->cartApi = $cartApi;
         $this->logger = $logger;
    }

    public function fetchCart(Context $context, Request $request): Cart
    {
        if ($context->session->loggedIn) {
            return $this->cartApi->getForUser($context->session->account, $context->locale);
        } else {
            $symfonySession = $request->hasSession() ? $request->getSession() : null;

            if ($symfonySession !== null &&
                $symfonySession->has('cart_id') &&
                $symfonySession->get('cart_id') !== null
            ) {
                $cartId = $symfonySession->get('cart_id');
                try {
                    return $this->cartApi->getById($cartId, $context->locale);
                } catch (\Exception $exception) {
                    $this->get('logger')
                        ->info(
                            'Error fetching anonymous cart {cartId}, creating new one',
                            [
                                'cartId' => $cartId,
                                'exception' => $exception,
                            ]
                        );
                }
            }

            $cart = $this->cartApi->getAnonymous(session_id(), $context->locale);
            if ($symfonySession !== null) {
                $symfonySession->set('cart_id', $cart->cartId);
            }
            return $cart;
        }
    }

}
