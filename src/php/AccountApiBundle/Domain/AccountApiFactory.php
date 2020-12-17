<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\ReplicatorBundle\Domain\Project;

interface AccountApiFactory
{
    public function factor(Project $project): AccountApi;
}
