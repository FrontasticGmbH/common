<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\AccountApi\SalutationHelper;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\ShopwareCartApi;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;

class CartItemRequestDataMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'cart-item-request';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     *
     * @return string[]
     */
    public function map($lineItem)
    {
        return [
            'type' => ShopwareCartApi::LINE_ITEM_TYPE_PRODUCT,
            'quantity' => $lineItem->count,
            'stackable' => true,
            'removable' => true,
            'label' => $lineItem->name,
            'coverId' => null,
            'referencedId' => $lineItem->variant->id ?? $lineItem->lineItemId,
        ];
    }

    private function resolveSalutationId(?string $frontasticSalutation): ?string
    {
        if ($frontasticSalutation === null) {
            return null;
        }

        $shopwareSalutation = $this->getProjectConfigApi()->getSalutation(
            SalutationHelper::resolveShopwareSalutation($frontasticSalutation)
        );

        return $shopwareSalutation ? $shopwareSalutation->id : null;
    }
}
