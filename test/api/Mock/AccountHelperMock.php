<?php

namespace Frontastic\Common\ApiTests\Mock;

use Frontastic\Common\AccountApiBundle\Domain\Account;

class AccountHelperMock
{
    public function getAccount(): Account
    {
        return new Account();
    }
}
