<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CoreBundle\Domain\Mailer;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) Central API entry point is OK to have many public methods.
 */
class AccountService
{
    /**
     * Account gateway
     *
     * @var AccountApi|null
     */
    private $accountApi;

    /**
     * @var Mailer|null
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
            'loggedIn' => (bool)$account,
            'account' => $account,
        ]);
    }

    public function sendConfirmationMail(Account $account)
    {
        $this->mailer->sendToUser(
            $account,
            'register',
            'Willkommen (Ihr neuer Account)',
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

    public function logout(Account $account): bool
    {
        return $this->accountApi->logout($account);
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
        return $this->accountApi->updatePassword($account, $oldPassword, $newPassword);
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
