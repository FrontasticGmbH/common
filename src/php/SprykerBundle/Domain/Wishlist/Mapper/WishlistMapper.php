<?php

namespace Frontastic\Common\SprykerBundle\Domain\Wishlist\Mapper;

use Frontastic\Common\WishlistApiBundle\Domain\LineItem;
use Frontastic\Common\WishlistApiBundle\Domain\Wishlist;
use Frontastic\Common\SprykerBundle\Domain\ExtendedMapperInterface;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class WishlistMapper implements ExtendedMapperInterface
{
    public const MAPPER_NAME = 'wishlist';
    private const RELATIONSHIP = 'wishlist-items';

    /**
     * @var LineItemMapper
     */
    private $lineItemMapper;

    /**
     * @param LineItemMapper $lineItemMapper
     */
    public function __construct(LineItemMapper $lineItemMapper)
    {
        $this->lineItemMapper = $lineItemMapper;
    }

    /**
     * @param ResourceObject $resource
     * @return Wishlist
     */
    public function mapResource(ResourceObject $resource): Wishlist
    {
        $wishlist = new Wishlist();

        $wishlist->wishlistId = $resource->id();
        $wishlist->name = $resource->attribute('name');

        $wishlist->lineItems = $this->mapLineItems($resource);

        $wishlist->dangerousInnerWishlist = $resource->attributes();

        return $wishlist;
    }

    /**
     * @param ResourceObject[] $resources
     * @return Wishlist[]
     */
    public function mapResourceArray(array $resources): array
    {
        $list = [];

        foreach ($resources as $primaryResource) {
            $list[] = $this->mapResource($primaryResource);
        }

        return $list;
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
    private function mapLineItems(ResourceObject $resource): array
    {
        $lineItems = [];

        if ($resource->hasRelationship(self::RELATIONSHIP)) {
            foreach ($resource->relationship(self::RELATIONSHIP)->resources() as $item) {
                $lineItems[] = $this->lineItemMapper->mapResource($item);
            }
        }

        return $lineItems;
    }
}
