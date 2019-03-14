<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Symfony\Component\HttpFoundation\Request;
use QafooLabs\MVC\Exception\UnauthenticatedAccountException;

use Frontastic\Common\CoreBundle\Domain\Mailer;

use Frontastic\Common\CartApiBundle\Domain\Cart;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) Central API entry point is OK to have many public methods.
 */
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
        $this->mailer->sendToUser(
            $account,
            'register',
            'Willkommen bei Frontastic',
            ['token' => $account->confirmationToken]
        );
        $account->eraseCredentials();
    }

    public function sendPasswordResetMail(Account $account)
    {
        $account = $this->accountApi->generatePasswordResetToken($account);
        $this->mailer->sendToUser($account, 'reset', 'Ihr neues Passwort', ['token' => $account->confirmationToken]);
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

    public function login(Account $account, ?Cart $cart = null): bool
    {
        return $this->accountApi->login($account, $cart);
    }

    public function create(Account $account, ?Cart $cart = null): Account
    {
        return $this->accountApi->create($account, $cart);
    }

    public function update(Account $account): Account
    {
        return $this->accountApi->update($account);
    }

    public function updatePassword(Account $account, string $oldPassword, string $newPassword): Account
    {
        return $this->accountApi->updatePassword($account->accountId, $oldPassword, $newPassword);
    }

    public function resetPassword(string $token, string $newPassword): Account
    {
        return $this->accountApi->resetPassword($token, $newPassword);
    }

    public function remove(Account $account)
    {
        $this->accountApi->remove($account);
    }
}
