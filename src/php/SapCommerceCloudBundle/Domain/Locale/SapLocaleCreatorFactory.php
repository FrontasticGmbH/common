<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain\Locale;

use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapClient;

abstract class SapLocaleCreatorFactory
{
    abstract public function factor(Project $project, SapClient $client): SapLocaleCreator;
}
