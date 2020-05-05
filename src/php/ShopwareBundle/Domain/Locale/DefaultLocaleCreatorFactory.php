<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Locale;

use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\ShopwareBundle\Domain\ClientInterface;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory;

class DefaultLocaleCreatorFactory extends LocaleCreatorFactory
{
    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory
     */
    private $projectConfigApiFactory;

    public function __construct(ShopwareProjectConfigApiFactory $projectConfigApiFactory)
    {
        $this->projectConfigApiFactory = $projectConfigApiFactory;
    }

    public function factor(Project $project, ClientInterface $client): LocaleCreator
    {
        return new DefaultLocaleCreator(
            $this->projectConfigApiFactory->factor($client)
        );
    }
}
