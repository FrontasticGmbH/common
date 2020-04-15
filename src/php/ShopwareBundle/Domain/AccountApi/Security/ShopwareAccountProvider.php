<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi\Security;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ShopwareAccountProvider implements UserProviderInterface
{
    private const TOKEN_TYPE = 'shopware';

    /**
     * @var \Symfony\Component\Security\Core\User\UserProviderInterface
     */
    private $originalUserProvider;

    /**
     * @param \Symfony\Component\Security\Core\User\UserProviderInterface $originalUserProvider
     */
    public function __construct(UserProviderInterface $originalUserProvider)
    {
        $this->originalUserProvider = $originalUserProvider;
    }

    /**
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        return $this->originalUserProvider->loadUserByUsername($username);
    }

    /**
     * @return UserInterface
     *
     * @throws UnsupportedUserException  if the user is not supported
     * @throws UsernameNotFoundException if the user is not found
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof Account) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getToken(self::TOKEN_TYPE));
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $this->originalUserProvider->supportsClass($class);
    }
}
