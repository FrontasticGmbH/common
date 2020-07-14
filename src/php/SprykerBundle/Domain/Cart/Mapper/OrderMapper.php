<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Mapper;

use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\SprykerBundle\Domain\ExtendedMapperInterface;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class OrderMapper implements ExtendedMapperInterface
{
    public const MAPPER_NAME = 'order';
    private const KEY_PACKETS = 'packets';
    private const KEY_SHIPPED_AT = 'shippedAt';
    private const KEY_TRACKING_LINK = 'trackingLink';

    /**
     * @param ResourceObject $resource
     * @return Order
     */
    public function mapResource(ResourceObject $resource): Order
    {
        $totals = $resource->attribute('totals');

        $order = new Order();

        $order->orderId = $resource->id();
        $order->dangerousInnerOrder = $resource->attributes();

        $order->sum = $totals['grandTotal'];

        $order->lineItems = $this->mapItems($resource);

        $order->custom[self::KEY_PACKETS] = $this->mapPackets($resource);

        return $order;
    }

    /**
     * @param ResourceObject[] $resources
     * @return Order[]
     */
    public function mapResourceArray(array $resources): array
    {
        $orders = [];

        foreach ($resources as $primaryResource) {
            $orders[] = $this->mapResource($primaryResource);
        }

        return $orders;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::MAPPER_NAME;
    }

    /**
     * @param ResourceObject $resource
     * @return LineItem[]
     */
    protected function mapItems(ResourceObject $resource): array
    {
        return array_map([$this, 'formatItem'], $resource->attribute('items', []));
    }

    /**
     * @param array $item
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\LineItem\Variant
     */
    protected function formatItem(array $item): LineItem\Variant
    {
        $lineItem = new LineItem\Variant();
        $lineItem->lineItemId = $item['sku'];
        $lineItem->name = $item['name'];
        $lineItem->count = $item['quantity'];
        $lineItem->price = $item['unitPrice'];
        $lineItem->discountedPrice = $item['unitPrice'];
        $lineItem->totalPrice = $item['sumPrice'];
        $lineItem->dangerousInnerItem = $item;

        return $lineItem;
    }

    /**
     * @param ResourceObject $resource
     *
     * @return array
     */
    private function mapPackets(ResourceObject $resource): array
    {
        $orderItems = $resource->attribute('items');
        $packets = [];

        if ($orderItems) {
            foreach ($orderItems as $key => $item) {
                if (
                    $item[self::KEY_SHIPPED_AT] &&
                    $item[self::KEY_TRACKING_LINK] &&
                    !$this->alreadyInPacket($item[self::KEY_SHIPPED_AT], $packets)
                ) {
                    $packets[] = [
                        self::KEY_SHIPPED_AT => $item[self::KEY_SHIPPED_AT],
                        self::KEY_TRACKING_LINK => $item[self::KEY_TRACKING_LINK]
                    ];
                }
            }
        }

        return $packets;
    }

    /**
     * @param string $shippedAt
     * @param array $packets
     *
     * @return bool
     */
    private function alreadyInPacket(string $shippedAt, array $packets): bool
    {
        foreach ($packets as $packet) {
            if ($packet[self::KEY_SHIPPED_AT] === $shippedAt) {
                return true;
            }
        }

        return false;
    }
}
