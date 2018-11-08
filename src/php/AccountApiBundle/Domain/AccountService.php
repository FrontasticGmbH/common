<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Symfony\Component\HttpFoundation\Request;
use QafooLabs\MVC\Exception\UnauthenticatedAccountException;

use Frontastic\Common\CoreBundle\Domain\Mailer;

class AccountService
{
    /**
     * Account gateway
     *
     * @var AccountGateway
     */
    private $accountApi;

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(AccountApi $accountApi/*, Mailer $mailer*/)
    {
        $this->accountApi = $accountApi ?? null;
        $this->mailer = $mailer ?? null;
    }

    public function getSessionFor(Account $account = null)
    {
        return new Session([
            'loggedIn' => (bool) $account,
            'account' => $account,
        ]);
    }

    public function sendConfirmationMail(Account $account)
    {
        $token = $account->generateConfirmationToken('P14D');

        // @TODO: Remove disabled mail sending
        return;

        $this->mailer->sendToUser($account, 'register', 'Willkommen bei Frontastic', ['token' => $token]);
    }

    public function sendPasswordResetMail(Account $account)
    {
        $token = $account->generateConfirmationToken('P2D');

        // @TODO: Remove disabled mail sending
        return;

        $this->mailer->sendToUser($account, 'reset', 'Ihr neues Passwort', ['token' => $token]);
    }

    public function get(string $email): Account
    {
        return $this->accountApi->get($email);
    }

    public function exists(string $email): bool
    {
        try {
            $this->accountApi->get($email);
            return true;
        } catch (\OutOfBoundsException $e) {
            return false;
        }
    }

    public function getByConfirmationToken(string $confirmationToken): Account
    {
        return $this->accountApi->getByConfirmationToken($confirmationToken);
    }

    public function login(Account $account): bool
    {
        return $this->accountApi->login($account);
    }

    public function create(Account $account): Account
    {
        return $this->accountApi->create($account);
    }

    public function update(Account $account): Account
    {
        return $this->accountApi->update($account);
    }

    public function remove(Account $account)
    {
        $this->accountApi->remove($account);
    }
}
