<?php

namespace Frontastic\Common\AccountApiBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Gateway\AccountGateway;

class AccountProvider implements UserProviderInterface
{
    private $userGateway;

    public function __construct($userGateway = null)
    {
        $this->userGateway = $userGateway;
    }

    public function loadUserByUsername($username)
    {
        try {
            return $this->userGateway->get($username);
        } catch (\OutOfBoundsException $e) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $username)
            );
        }
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof Account) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === Account::class;
    }
}
