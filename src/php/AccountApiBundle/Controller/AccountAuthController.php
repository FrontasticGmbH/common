<?php

namespace Frontastic\Common\AccountApiBundle\Controller;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountService;
use Frontastic\Common\CoreBundle\Domain\ErrorResult;
use QafooLabs\MVC\RedirectRoute;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

class AccountAuthController extends Controller
{
    public function indexAction(Request $request, UserInterface $account = null): JsonResponse
    {
        return new JsonResponse($this->getAccountService()->getSessionFor($account));
    }

    public function registerAction(Request $request, Context $context): JsonResponse
    {
        $body = $this->getJsonBody($request);
        $account = new Account([
            'email' => $body['email'],
            'salutation' => $body['salutation'],
            'firstName' => $body['firstName'],
            'lastName' => $body['lastName'],
            'birthday' => isset($body['birthdayYear']) ?
                new \DateTimeImmutable(
                    $body['birthdayYear'] .
                    '-' . ($body['birthdayMonth'] ?? 1) .
                    '-' . ($body['birthdayDay'] ?? 1) .
                    'T12:00'
                ) : null,
            'data' => [
                'phonePrefix' => $body['phonePrefix'] ?? null,
                'phone' => $body['phone'] ?? null,
            ],
        ]);
        $account->setPassword($body['password']);

        if ($this->getAccountService()->exists($account->email)) {
            return new JsonResponse(new ErrorResult(['message' => "Die E-Mail-Adresse wird bereits verwendet."]), 409);
        }

        $account = $this->getAccountService()->create(
            $account,
            $this->get('frontastic.catwalk.cart_api')->getAnonymous(session_id(), $context->locale)
        );

        if ($account->confirmationToken !== null) {
            $this->getAccountService()->sendConfirmationMail($account);
        }

        return $this->loginAccount($account, $request);
    }

    public function confirmAction(Request $request, string $token): JsonResponse
    {
        $account = $this->getAccountService()->confirmEmail($token);

        return $this->loginAccount($account, $request);
    }

    public function requestResetAction(Request $request): RedirectRoute
    {
        $body = $this->getJsonBody($request);
        $account = $this->getAccountService()->get($body['email']);
        $this->getAccountService()->sendPasswordResetMail($account);

        return new RedirectRoute('Frontastic.AccountBundle.Account.logout');
    }

    public function resetAction(Request $request, string $token): JsonResponse
    {
        $body = $this->getJsonBody($request);
        $account = $this->getAccountService()->resetPassword($token, $body['newPassword']);

        return $this->loginAccount($account, $request);
    }

    public function changePasswordAction(Request $request, Context $context): JsonResponse
    {
        $this->assertIsAuthenticated($context);

        $account = $this->getAccountService()->get($context->session->account->email);

        $body = $this->getJsonBody($request);
        $account = $this->getAccountService()->updatePassword($account, $body['oldPassword'], $body['newPassword']);
        return new JsonResponse($account, 201);
    }

    public function updateAction(Request $request, Context $context): JsonResponse
    {
        $this->assertIsAuthenticated($context);

        $account = $this->getAccountService()->get($context->session->account->email);

        $body = $this->getJsonBody($request);
        $account->salutation = $body['salutation'];
        $account->firstName = $body['firstName'];
        $account->lastName = $body['lastName'];
        $account->birthday = new \DateTimeImmutable($body['birthdayYear'] .
            '-' . $body['birthdayMonth'] .
            '-' . $body['birthdayDay'] .
            'T12:00');
        $account->data = [
            'phonePrefix' => $body['phonePrefix'],
            'phone' => $body['phone'],
        ];
        $account = $this->getAccountService()->update($account);

        return new JsonResponse($account, 200);
    }

    /**
     * @param \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context
     */
    protected function assertIsAuthenticated(Context $context): void
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }
    }

    /**
     * @return \Frontastic\Common\AccountApiBundle\Domain\AccountApi
     */
    protected function getAccountService(): AccountService
    {
        return $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');
    }

    protected function getJsonBody(Request $request): array
    {
        if (!$request->getContent() ||
            !($body = json_decode($request->getContent(), true))) {
            throw new \InvalidArgumentException('Invalid data passed: ' . $request->getContent());
        }

        return $body;
    }

    protected function loginAccount(Account $account, Request $request): Response
    {
        return $this->get('frontastic.user.guard_handler')->authenticateUserAndHandleSuccess(
            $account,
            $request,
            $this->get('Frontastic\Catwalk\FrontendBundle\Security\Authenticator'),
            'api'
        );
    }
}
