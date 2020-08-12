<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account;

class SessionService
{
    public function getSessionId(): string
    {
        return session_id();
    }
}
