<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\ReplicatorBundle\Domain\Project;

interface CartApiFactory
{
    public function factor(Project $project): CartApi;
}
