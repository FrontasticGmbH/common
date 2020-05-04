<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Controller;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Controller\CartController as CommonCartController;
use Symfony\Component\HttpFoundation\Request;

class CartController extends CommonCartController
{
    /**
     * @param \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function checkoutAction(Context $context, Request $request): array
    {
        $cartApi = $this->getCartApi($context);
        $cart = $this->getCart($context, $request);

        if (!$context->session->loggedIn) {
            $payload = $this->getJsonContent($request);
            $account = $this->createAccountModel($payload);
        }

        // @TODO: pass account model create guest order endpoint
        $order = $cartApi->order($cart, $context->locale);

        session_regenerate_id();

        return [
            'order' => $order,
        ];
    }

    /**
     * @param array $payload
     *
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    protected function createAccountModel(array $payload): Account
    {
        $accountData = [
            'email' => $payload['email'],
            'salutation' => $payload['salutation'],
            'firstName' => $payload['firstName'],
            'lastName' => $payload['lastName'],
            'data' => [
                'phonePrefix' => $payload['phonePrefix'] ?? null,
                'phone' => $payload['phone'] ?? null,
            ],
            // Billing address must be defined when creating new account
            'addresses' => [
                new Address($payload['billingAddress']),
            ]
        ];

        return new Account($accountData);
    }
}
