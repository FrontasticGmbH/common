<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Locale;

use Frontastic\Common\ReplicatorBundle\Domain\Project;

class DefaultLocaleCreatorFactory extends LocaleCreatorFactory
{
    public function factor(Project $project): DefaultLocaleCreator
    {
        return new DefaultLocaleCreator();
    }
}
