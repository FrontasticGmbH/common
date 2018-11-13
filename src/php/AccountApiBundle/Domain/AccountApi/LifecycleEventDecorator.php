<?php

namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi;

use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;

class LifecycleEventDecorator implements AccountApi
{
    private $aggregate;
    private $listerners = [];

    public function __construct(AccountApi $aggregate, iterable $listerners = [])
    {
        $this->aggregate = $aggregate;

        foreach ($listerners as $listerner) {
            $this->addListener($listerner);
        }
    }

    public function addListener($listener)
    {
        $this->listerners[] = $listener;
    }

    public function get(string $email): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function confirmEmail(string $token): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function create(Account $account): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function verifyEmail(string $token): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function update(Account $account): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function login(Account $account): bool
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function getAddresses(string $accountId): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function addAddress(string $accountId, Address $address): Account
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function getDangerousInnerClient()
    {
        return $this->aggregate->getDangerousInnerClient();
    }

    private function dispatch(string $method, array $arguments)
    {
        $beforeEvent = 'before' . ucfirst($method);
        foreach ($this->listerners as $listener) {
            if (is_callable([$listener, $beforeEvent])) {
                call_user_func_array([$listener, $beforeEvent], array_merge([$this->aggregate], $arguments));
            }
        }

        $result = call_user_func_array([$this->aggregate, $method], $arguments);

        $afterEvent = 'after' . ucfirst($method);
        foreach ($this->listerners as $listener) {
            if (is_callable([$listener, $afterEvent])) {
                $listener->$afterEvent($this->aggregate, $result);
            }
        }

        return $result;
    }
}
