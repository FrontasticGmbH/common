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

    public function __construct(AccountApi $accountApi, Mailer $mailer)
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
        $this->mailer->sendToUser($account, 'register', 'Willkommen bei Frontastic', ['token' => $account->confirmationToken]);
        $account->eraseCredentials();
    }

    public function sendPasswordResetMail(Account $account)
    {
        $this->mailer->sendToUser($account, 'reset', 'Ihr neues Passwort', ['token' => $token]);
        $account->eraseCredentials();
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

    public function confirmEmail(string $confirmationToken): Account
    {
        return $this->accountApi->confirmEmail($confirmationToken);
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
