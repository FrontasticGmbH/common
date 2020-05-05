<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiInterface;

trait ProjectConfigApiAwareDataMapperTrait
{
    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiInterface
     */
    private $projectConfigApi;

    public function setProjectConfigApi(ShopwareProjectConfigApiInterface $projectConfigApi): self
    {
        $this->projectConfigApi = $projectConfigApi;

        return $this;
    }

    public function getProjectConfigApi(): ShopwareProjectConfigApiInterface
    {
        return $this->projectConfigApi;
    }
}
