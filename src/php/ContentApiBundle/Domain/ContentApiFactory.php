<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Frontastic\Common\ReplicatorBundle\Domain\Project;

interface ContentApiFactory
{
    public function factor(Project $project): ContentApi;
}
