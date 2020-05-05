<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Locale;

use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\ShopwareBundle\Domain\ClientInterface;

abstract class LocaleCreatorFactory
{
    abstract public function factor(Project $project, ClientInterface $client): LocaleCreator;
}
