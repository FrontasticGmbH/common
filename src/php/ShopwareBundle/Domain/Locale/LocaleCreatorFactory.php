<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Locale;

use Frontastic\Common\ProjectApiBundle\Domain\DefaultProjectApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class LocaleCreatorFactory
{
    /**
     * @var \Frontastic\Common\ProjectApiBundle\Domain\DefaultProjectApiFactory
     */
    private $projectApiFactory;

    public function __construct(DefaultProjectApiFactory $projectApiFactory)
    {
        $this->projectApiFactory = $projectApiFactory;
    }

    public function factor(Project $project): LocaleCreator
    {
        return new LocaleCreator(
            $this->projectApiFactory->factor($project)
        );
    }
}
