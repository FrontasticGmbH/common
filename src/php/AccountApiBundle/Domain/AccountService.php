<?php

namespace Frontastic\Backstage\UserBundle\Domain;

use Symfony\Component\HttpFoundation\Request;
use QafooLabs\MVC\Exception\UnauthenticatedUserException;

use Frontastic\Backstage\UserBundle\Gateway\UserGateway;
use Frontastic\Common\CoreBundle\Domain\Mailer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class UserService
{
    /**
     * User gateway
     *
     * @var UserGateway
     */
    private $userGateway;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(UserGateway $userGateway, Mailer $mailer, TokenStorage $tokenStorage)
    {
        $this->userGateway = $userGateway;
        $this->mailer = $mailer;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Request $request Deprecated, is not required
     * @return Session
     */
    public function getSession(Request $request): Session
    {
        try {
            $token = $this->tokenStorage->getToken();

            if ($token === null) {
                return new Session([
                    'loggedIn' => false,
                    'user' => null,
                ]);
            }

            $user = $token->getUser();

            if (!($user instanceof User)) {
                $user = null;
            }

            return $this->getSessionFor($user);
        } catch (UnauthenticatedUserException $e) {
            return new Session([
                'loggedIn' => false,
                'message' => $e->getMessage() ? ('Unauthenticated: ' . $e->getMessage()) : null,
            ]);
        }
    }

    public function getSessionFor(User $user = null)
    {
        return new Session([
            'loggedIn' => (bool) $user,
            'user' => $user,
        ]);
    }

    public function getSystemSession(): Session
    {
        return new Session([
            'loggedIn' => true,
            'user' => $this->getSystemUser()
        ]);
    }

    public function getSystemUser(): User
    {
        return new User([
            'email' => 'system@frontastic.cloud',
            'displayName' => 'System',
        ]);
    }

    public function sendConfirmationMail(User $user)
    {
        $token = $user->generateConfirmationToken('P14D');

        $this->mailer->sendToUser($user, 'register', 'Willkommen bei Frontastic', ['token' => $token]);
    }

    public function sendPasswordResetMail(User $user)
    {
        $token = $user->generateConfirmationToken('P2D');

        $this->mailer->sendToUser($user, 'reset', 'Ihr neues Passwort', ['token' => $token]);
    }

    public function get(string $email): User
    {
        return $this->userGateway->get($email);
    }

    public function exists(string $email): bool
    {
        try {
            $this->userGateway->get($email);
            return true;
        } catch (\OutOfBoundsException $e) {
            return false;
        }
    }

    public function getByConfirmationToken(string $confirmationToken): User
    {
        return $this->userGateway->getByConfirmationToken($confirmationToken);
    }

    public function store(User $user): User
    {
        return $this->userGateway->store($user);
    }

    public function remove(User $user)
    {
        $this->userGateway->remove($user);
    }
}
