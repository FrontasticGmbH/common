<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiInterface;

interface ProjectConfigApiAwareDataMapperInterface
{
    public function setProjectConfigApi(ShopwareProjectConfigApiInterface $projectConfigApi);

    public function getProjectConfigApi(): ShopwareProjectConfigApiInterface;
}
