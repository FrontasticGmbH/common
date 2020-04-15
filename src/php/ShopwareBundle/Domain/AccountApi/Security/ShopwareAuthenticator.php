<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi\Security;

use Frontastic\Catwalk\FrontendBundle\Security\Authenticator;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ShopwareAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var \Frontastic\Catwalk\FrontendBundle\Security\Authenticator
     */
    private $originalAuthenticator;

    /**
     * @param \Frontastic\Catwalk\FrontendBundle\Security\Authenticator $originalAuthenticator
     */
    public function __construct($originalAuthenticator)
    {
        $this->originalAuthenticator = $originalAuthenticator;
    }

    /**
     * @param Request $request The request that resulted in an AuthenticationException
     * @param AuthenticationException $authException The exception that started the authentication process
     *
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return $this->originalAuthenticator->start($request, $authException);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return $this->originalAuthenticator->supports($request);
    }

    /**
     * @param Request $request
     *
     * @return mixed Any non-null value
     *
     * @throws \UnexpectedValueException If null is returned
     */
    public function getCredentials(Request $request)
    {
        return $this->originalAuthenticator->getCredentials($request);
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface|null
     * @throws AuthenticationException
     *
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // For Shopware to get user info, we must be authenticated.
        // Just create temporary object, to hold email and password.
        // Everything is done in checkCredentials method

        $account = new Account();
        $account->email = $credentials['email'];
        $account->setPassword($credentials['password']);

        return $account;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     *
     * @throws AuthenticationException
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->originalAuthenticator->checkCredentials($credentials, $user);
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return $this->originalAuthenticator->onAuthenticationFailure($request, $exception);
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey The provider (i.e. firewall) key
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return $this->originalAuthenticator->onAuthenticationSuccess($request, $token, $providerKey);
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return $this->originalAuthenticator->supportsRememberMe();
    }

    /**
     * @param UserInterface $user
     * @param string $providerKey
     *
     * @return \Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken
     */
    public function createAuthenticatedToken(UserInterface $user, $providerKey)
    {
        return $this->originalAuthenticator->createAuthenticatedToken($user, $providerKey);
    }
}
