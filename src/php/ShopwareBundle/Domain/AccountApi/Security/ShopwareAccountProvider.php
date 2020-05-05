<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi\Security;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountService;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\ShopwareAccountApi;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ShopwareAccountProvider implements UserProviderInterface
{
    /**
     * @var \Frontastic\Common\AccountApiBundle\Domain\AccountService
     */
    private $accountService;

    /**
     * @param \Frontastic\Common\AccountApiBundle\Domain\AccountService $accountService
     */
    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername($username)
    {
        try {
            return $this->accountService->get($username);
        } catch (\OutOfBoundsException $e) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $username)
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof Account) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getToken(ShopwareAccountApi::TOKEN_TYPE));
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class)
    {
        return $class === Account::class;
    }
}
