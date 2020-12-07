<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Mapper;

use Frontastic\Common\CartApiBundle\Domain\Discount;
use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class DiscountMapper implements MapperInterface
{
    public const MAPPER_NAME = 'discount';

    public function mapResource(ResourceObject $resource)
    {
        return new Discount([
            'discountId' => $resource->id() ?? 'undefined',
            'code' => $resource->attribute('code', null),
            'state' => null,
            'name' => $resource->attribute('displayName', null),
            'description' => $resource->attribute('displayName', null),
            'dangerousInnerDiscount' => $resource,
        ]);
    }

    public function getName(): string
    {
        return self::MAPPER_NAME;
    }
}
