<?php

namespace Frontastic\Common\ProjectApiBundle\Domain;

use Frontastic\Common\ReplicatorBundle\Domain\Project;

interface ProjectApiFactory
{
    public function factor(Project $project): ProjectApi;
}
