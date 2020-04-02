<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Locale;

use Frontastic\Common\ReplicatorBundle\Domain\Project;

class LocaleCreatorFactory
{
    public function factor(Project $project): LocaleCreator
    {
        return new LocaleCreator();
    }
}
