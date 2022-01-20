<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;

class OrdersMapper extends AbstractDataMapper implements ProjectConfigApiAwareDataMapperInterface
{
    use ProjectConfigApiAwareDataMapperTrait;

    public const MAPPER_NAME = 'orders';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\OrderMapper
     */
    private $orderMapper;

    public function __construct(OrderMapper $orderMapper)
    {
        $this->orderMapper = $orderMapper;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        if (!key_exists(self::MAPPER_NAME, $resource)) {
            return [];
        }

        $ordersData = $this->extractElements($resource[self::MAPPER_NAME], $resource[self::MAPPER_NAME]);

        $result = [];
        foreach ($ordersData as $item) {
            $result[] = $this->getOrderMapper()->map($item);
        }

        return $result;
    }

    private function getOrderMapper(): OrderMapper
    {
        return $this->orderMapper->setProjectConfigApi($this->getProjectConfigApi());
    }
}
