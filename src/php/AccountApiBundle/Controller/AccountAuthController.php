<?php

namespace Frontastic\Common\AccountApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

use QafooLabs\MVC\RedirectRoute;

use Frontastic\Common\AccountApiBundle\Security\Authenticator;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\AccountApiBundle\Domain\Session;
use Frontastic\Common\AccountApiBundle\Domain\AuthentificationInformation;

use Frontastic\Common\CoreBundle\Domain\ErrorResult;
use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;

class AccountAuthController extends Controller
{
    public function indexAction(Request $request, UserInterface $account = null): JsonResponse
    {
        $accountService = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');
        return new JsonResponse($accountService->getSessionFor($account));
    }

    public function registerAction(Request $request): JsonResponse
    {
        $accountService = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');

        $body = $this->getJsonBody($request);
        $account = new Account([
            'email' => $body['email'],
            'salutation' => $body['salutation'],
            'firstName' => $body['firstName'],
            'lastName' => $body['lastName'],
            'birthday' => new \DateTimeImmutable($body['birthdayYear'] . '-' . $body['birthdayMonth'] . '-' . $body['birthdayDay'] . 'T12:00'),
            'data' => [
                'phonePrefix' => $body['phonePrefix'],
                'phone' => $body['phone'],
            ],
        ]);
        $account->setPassword($body['password']);

        if ($accountService->exists($account->email)) {
            return new JsonResponse(new ErrorResult(['message' => "Die E-Mail-Adresse wird bereits verwendet."]), 409);
        }

        $account = $accountService->create($account);
        $accountService->sendConfirmationMail($account);

        return new JsonResponse($accountService->getSessionFor($account));
    }

    public function confirmAction(Request $request, string $token): JsonResponse
    {
        $accountService = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');
        $account = $accountService->confirmEmail($token);

        return $this->loginAccount($account, $request);
    }

    public function requestResetAction(Request $request): RedirectRoute
    {
        $accountService = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');

        $body = $this->getJsonBody($request);
        $authentificationInformation = new AuthentificationInformation($body);
        $account = $accountService->get($authentificationInformation->email);
        $accountService->sendPasswordResetMail($account);
        $account = $accountService->store($account);

        return new RedirectRoute('Frontastic.AccountBundle.Account.logout');
    }

    public function resetAction(Request $request, string $token): JsonResponse
    {
        $accountService = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');
        $account = $accountService->getByConfirmationToken($token);
        if (!$account->isValidConfirmationToken($token)) {
            throw new AuthenticationException('Invalid password reset token provided.');
        }

        $body = $this->getJsonBody($request);
        $authentificationInformation = new AuthentificationInformation($body);

        $account->confirmed = true;
        $account->clearConfirmationToken();
        $account->setPassword($authentificationInformation->password);
        $account = $accountService->store($account);

        $body['email'] = $account->email;

        $response = $this->loginAccount($account, $this->cloneRequest($request, $body));
        return $response;
    }

    public function changePasswordAction(Request $request, Context $context): JsonResponse
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }

        $accountService = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');
        $account = $accountService->get($context->session->account->email);

        $body = $this->getJsonBody($request);
        $account = $accountService->updatePassword($account, $body['oldPassword'], $body['newPassword']);
        return new JsonResponse($account, 201);
    }

    public function updateAction(Request $request, Context $context): JsonResponse
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }

        $accountService = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');
        $account = $accountService->get($context->session->account->email);

        $body = $this->getJsonBody($request);
        $account->salutation = $body['salutation'];
        $account->firstName = $body['firstName'];
        $account->lastName = $body['lastName'];
        $account->birthday = new \DateTimeImmutable($body['birthdayYear'] . '-' . $body['birthdayMonth'] . '-' . $body['birthdayDay'] . 'T12:00');
        $account->data = [
            'phonePrefix' => $body['phonePrefix'],
            'phone' => $body['phone'],
        ];
        $account = $accountService->update($account);

        return new JsonResponse($account, 200);
    }

    private function getJsonBody(Request $request): array
    {
        if (!$request->getContent() ||
            !($body = json_decode($request->getContent(), true))) {
            throw new \InvalidArgumentException("Invalid data passed: " . $request->getContent());
        }

        return $body;
    }

    private function cloneRequest(Request $request, $newBody): Request
    {
        // Dirty hack to override the body
        return Request::create(
            $request->getUri(),
            $request->getMethod(),
            $request->request->all(),
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all(),
            json_encode($newBody)
        );
    }

    private function loginAccount(Account $account, Request $request): Response
    {
        /** @var Response $loginResponse */
        return $this->get('frontastic.user.guard_handler')->authenticateUserAndHandleSuccess(
            $account,
            $request,
            $this->get(Authenticator::class),
            'api'
        );
    }
}
