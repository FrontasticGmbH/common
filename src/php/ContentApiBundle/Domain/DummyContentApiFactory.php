<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\DummyContentApi;

class DummyContentApiFactory implements ContentApiFactory
{
    public function factor(Project $project): ContentApi
    {
        return new DummyContentApi();
    }
}
