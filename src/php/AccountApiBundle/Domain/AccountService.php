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

    public function sendPasswordResetMail(string $email)
    {
        $token = $this->accountApi->generatePasswordResetToken($email);

        if ($token->confirmationToken !== null) {
            $this->mailer->sendToUser(
                new Account([
                    'email' => $token->email,
                ]),
                'reset',
                'Ihr neues Passwort',
                ['token' => $token->confirmationToken]
            );
        }
    }

    public function confirmEmail(string $confirmationToken, string $locale = null): Account
    {
        return $this->accountApi->confirmEmail($confirmationToken, $locale);
    }

    public function login(Account $account, ?Cart $cart = null, string $locale = null): ?Account
    {
        return $this->accountApi->login($account, $cart, $locale);
    }

    public function refresh(Account $account, string $locale = null): Account
    {
        return $this->accountApi->refreshAccount($account, $locale);
    }

    public function create(Account $account, ?Cart $cart = null, string $locale = null): Account
    {
        return $this->accountApi->create($account, $cart, $locale);
    }

    public function update(Account $account, string $locale = null): Account
    {
        return $this->accountApi->update($account, $locale);
    }

    public function updatePassword(
        Account $account,
        string $oldPassword,
        string $newPassword,
        string $locale = null
    ): Account {
        return $this->accountApi->updatePassword($account, $oldPassword, $newPassword, $locale);
    }

    public function resetPassword(string $token, string $newPassword, string $locale = null): Account
    {
        return $this->accountApi->resetPassword($token, $newPassword, $locale);
    }
}
