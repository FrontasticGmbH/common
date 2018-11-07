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
use Frontastic\Common\AccountApiBundle\Domain\Session;
use Frontastic\Common\AccountApiBundle\Domain\AuthentificationInformation;

use Frontastic\Common\CoreBundle\Domain\ErrorResult;

class AccountController extends Controller
{
    public function indexAction(Request $request, UserInterface $user = null): JsonResponse
    {
        $userService = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');
        return new JsonResponse($userService->getSessionFor($user));
    }

    public function registerAction(Request $request): JsonResponse
    {
        $userService = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');

        $body = $this->getJsonBody($request);
        $authentificationInformation = new AuthentificationInformation($body);

        if ($userService->exists($authentificationInformation->email)) {
            return new JsonResponse(new ErrorResult(['message' => "Die E-Mail-Adresse wird bereits verwendet."]), 409);
        }

        $user = new Account();
        $user->email = $authentificationInformation->email;
        $user->displayName = substr($user->email, 0, strrpos($user->email, '@'));
        $user->setPassword($authentificationInformation->password);

        $userService->sendConfirmationMail($user);

        $user = $userService->store($user);

        $loginResponse = $this->loginUser($user, $request);
        $loginResponse->setStatusCode(201);
        return $loginResponse;
    }

    public function confirmAction(Request $request, string $token): JsonResponse
    {
        $userService = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');
        $user = $userService->getByConfirmationToken($token);
        if (!$user->isValidConfirmationToken($token)) {
            throw new AuthenticationException('Invalid confirmation token provided.');
        }

        $user->confirmed = true;
        $user->clearConfirmationToken();
        $user = $userService->store($user);

        return $this->loginUser($user, $request);
    }

    public function requestResetAction(Request $request): RedirectRoute
    {
        $userService = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');

        $body = $this->getJsonBody($request);
        $authentificationInformation = new AuthentificationInformation($body);
        $user = $userService->get($authentificationInformation->email);
        $userService->sendPasswordResetMail($user);
        $user = $userService->store($user);

        return new RedirectRoute('Frontastic.AccountBundle.Account.logout');
    }

    public function resetAction(Request $request, string $token): JsonResponse
    {
        $userService = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');
        $user = $userService->getByConfirmationToken($token);
        if (!$user->isValidConfirmationToken($token)) {
            throw new AuthenticationException('Invalid password reset token provided.');
        }

        $body = $this->getJsonBody($request);
        $authentificationInformation = new AuthentificationInformation($body);

        $user->confirmed = true;
        $user->clearConfirmationToken();
        $user->setPassword($authentificationInformation->password);
        $user = $userService->store($user);

        $body['email'] = $user->email;

        $response = $this->loginUser($user, $this->cloneRequest($request, $body));
        return $response;
    }

    public function changePasswordAction(Request $request, UserInterface $user = null): JsonResponse
    {
        if ($user === null) {
            throw new AuthenticationException('Not logged in.');
        }

        $userService = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');

        $body = $this->getJsonBody($request);
        $authentificationInformation = new AuthentificationInformation($body);

        if (!$user->confirmed ||
            !$user->isValidPassword($authentificationInformation->password)) {
            throw new \RuntimeException('Invalid login data provided.');
        }

        $user->setPassword($authentificationInformation->newPassword);
        $user = $userService->store($user);

        return $this->loginUser($user, $this->cloneRequest($request, $body));
    }

    public function updateAction(Request $request, UserInterface $user = null): JsonResponse
    {
        if ($user === null) {
            throw new AuthenticationException('Not logged in.');
        }

        $userService = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');

        $body = $this->getJsonBody($request);

        $propertiesToUpdate = [
            'displayName' => true,
            'data' => true,
        ];
        foreach (array_intersect_key($body, $propertiesToUpdate) as $key => $value) {
            $user->$key = $value;
        }
        $user = $userService->store($user);

        return new JsonResponse($user, 200);
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

    private function loginUser(Account $user, Request $request): Response
    {
        /** @var Response $loginResponse */
        return $this->get('frontastic.user.guard_handler')->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $this->get(Authenticator::class),
            'api'
        );
    }
}
