<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Controller;

use DateTimeImmutable;
use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Common\AccountApiBundle\Controller\AccountAuthController;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use QafooLabs\MVC\RedirectRoute;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

class ShopwareAccountAuthController extends AccountAuthController
{
    public function indexAction(Request $request, UserInterface $account = null): JsonResponse
    {
        $r = $this->get('frontastic.catwalk.account_api')->getAddresses($account);
//        $r = this->getAccountService()->getSessionFor($account)
        return new JsonResponse($r);
    }

    public function testAction(Request $request, UserInterface $account = null)
    {
        $result = $this->get('frontastic.catwalk.account_api')->getAddresses($account);
    }

    public function loginAction(Request $request, UserInterface $account = null): JsonResponse
    {
        if ($account === null) {
            throw new BadRequestHttpException('Can\'t proceed with login, user missing');
        }

        $this->loginAccount($account);

        return new JsonResponse($this->getAccountService()->getSessionFor($account));
    }

    public function logoutAction(Request $request, UserInterface $account = null): JsonResponse
    {
        if ($account === null) {
            throw new BadRequestHttpException('Can\'t proceed with logout, user missing');
        }

        $this->getAccountService()->logout($account);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function registerAction(Request $request, Context $context): JsonResponse
    {
        $body = $this->getJsonBody($request);
        $accountData = [
            'email' => $body['email'],
            'salutation' => $body['salutation'],
            'firstName' => $body['firstName'],
            'lastName' => $body['lastName'],
            'data' => [
                'phonePrefix' => $body['phonePrefix'] ?? null,
                'phone' => $body['phone'] ?? null,
            ],
            // Billing address must be defined when creating new account
            'addresses' => [
                new Address($body['billingAddress']),
            ]
        ];

        if (isset($body['differentShippingAddress']) && $body['differentShippingAddress']) {
            $accountData['addresses'][] = new Address($body['shippingAddress']);
        }

        if (isset($body['birthdayYear'], $body['birthdayMonth'], $body['birthdayDay'])) {
            $accountData['birthday'] = new DateTimeImmutable(
                sprintf('%s-%s-%sT12:00', $body['birthdayYear'], $body['birthdayMonth'], $body['birthdayDay'])
            );
        }

        $account = new Account($accountData);
        $account->setPassword($body['password']);

        $cart = $this->getAnonymousCart($context->locale);

        $account = $this->getAccountService()->create($account, $cart);

        return $this->loginAccount($account, $request, $cart);
    }

    public function requestResetAction(Request $request): RedirectRoute
    {
        $body = $this->getJsonBody($request);

        $account = new Account();
        $account->email = $body['email'];

        $this->getAccountService()->generatePasswordResetToken($account);

        return new RedirectRoute('Frontastic.Frontend.Master.Account.index');
    }

    public function changePasswordAction(Request $request, Context $context): JsonResponse
    {
        $this->assertIsAuthenticated($context);

        $account = $context->session->account;
        $body = $this->getJsonBody($request);

        $account = $this->getAccountService()->updatePassword(
            $account,
            $body['password'],
            $body['passwordNew']
        );

        return new JsonResponse($account, Response::HTTP_CREATED);
    }

    public function updateAction(Request $request, Context $context): JsonResponse
    {
        $this->assertIsAuthenticated($context);

        $body = $this->getJsonBody($request);

        // In case password field is incorporated into same profile form
        if (isset($body['passwordNew'])) {
            return $this->changePasswordAction($request, $context);
        }

        $account = $context->session->account;
        $account->salutation = $body['salutation'];
        $account->firstName = $body['firstName'];
        $account->lastName = $body['lastName'];

        if (isset($body['birthdayYear'], $body['birthdayMonth'], $body['birthdayDay'])) {
            $account->birthday = new DateTimeImmutable(
                sprintf('%s-%s-%sT12:00', $body['birthdayYear'], $body['birthdayMonth'], $body['birthdayDay'])
            );
        }

        $account->data = [
            'phonePrefix' => $body['phonePrefix'] ?? null,
            'phone' => $body['phone'] ?? null,
        ];

        $updatedAccount = $this->getAccountService()->update($account);

        return new JsonResponse($updatedAccount);
    }

    protected function loginAccount(Account $account, ?Request $request = null, ?Cart $cart = null): Response
    {
        if ($request !== null) {
            $body = $this->getJsonBody($request);
            $account->setPassword($body['password']);
        }

        $this->getAccountService()->login($account, $cart);

        return parent::loginAccount($account, $request);
    }

    /**
     * @param string $locale
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    private function getAnonymousCart(string $locale): Cart
    {
        return $this->get('frontastic.catwalk.cart_api')->getAnonymous(session_id(), $locale);
    }
}
