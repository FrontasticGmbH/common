<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Catwalk\ApiCoreBundle\Domain\ContextService;
use Frontastic\Common\AccountApiBundle\Domain\Account;

class AccountHelper
{
    public const TOKEN_TYPE = 'spryker';

    /**
     * @var ContextService
     */
    private $contextService;

//    public function __construct(ContextService $contextService)
    public function __construct()
    {
        $this->contextService = null;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->getContext()->session->account;
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->getContext()->session->loggedIn;
    }

    /**
     * @param string|null $token
     *
     * @return array
     */
    public function getAuthHeader(?string $token = null): array
    {
        if (!$token && !$this->isLoggedIn()) {
            throw new \RuntimeException('User not logged in');
        }

        if (!$token) {
            $token = $this->getAccount()->getToken(self::TOKEN_TYPE);
        }

        return ['Authorization' => sprintf('Bearer %s', $token)];
    }

    /**
     * @param string|null $id
     *
     * @return array
     */
    public function getAnonymousHeader(?string $id = null): array
    {
        if (!$id) {
            $id = $this->getAccount()->accountId;
        }

        return ['X-Anonymous-Customer-Unique-Id' => $id];
    }

    /**
     * @return Context
     */
    private function getContext(): Context
    {
        return $this->contextService->createContextFromRequest();
    }

    /**
     * @param string|null $id
     * @param string|null $token
     *
     * @return array
     */
    public function getAutoHeader(?string $id = null, ?string $token = null): array
    {
//        if ($token || $this->isLoggedIn()) {
//            return $this->getAuthHeader($token);
//        }

        return $this->getAnonymousHeader($id);
    }
}
