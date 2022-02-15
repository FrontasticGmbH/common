<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi\DummyAccountApi;

class DummyAccountApiFactory implements AccountApiFactory
{

    public function factor(Project $project): AccountApi
    {
        return new DummyAccountApi();
    }
}
