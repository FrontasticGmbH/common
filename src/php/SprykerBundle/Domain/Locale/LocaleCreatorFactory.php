<?php

namespace Frontastic\Common\SprykerBundle\Domain\Locale;

use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;

abstract class LocaleCreatorFactory
{
    abstract public function factor(Project $project, SprykerClientInterface $client): LocaleCreator;
}
