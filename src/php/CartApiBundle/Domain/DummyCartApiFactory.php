<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\CartApiBundle\Domain\CartApi\DummyCartApi;

class DummyCartApiFactory implements CartApiFactory
{
    public function factor(Project $project): CartApi
    {
        return new DummyCartApi();
    }
}
