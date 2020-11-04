<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Expander;

use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

trait IncludedResourceExpanderTrait
{
    /**
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject[] $includes
     * @param string $type
     * @param string $resourceId
     *
     * @return \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject|null
     */
    private function getResourceInclude(array $includes, string $type, string $resourceId): ?ResourceObject
    {
        foreach ($includes as $include) {
            if ($include->type() === $type && $include->id() === $resourceId) {
                return $include;
            }
        }

        return null;
    }

    /**
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject[] $includes
     * @param string $type
     *
     * @return \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject[]
     */
    private function getMappedResources(array $includes, string $type): array
    {
        $map = [];

        foreach ($includes as $resource) {
            if ($resource->type() === $type) {
                $map[$resource->id()] = $resource;
            }
        }

        return $map;
    }

    /**
     * @param array $includes
     * @param string $type
     *
     * @return \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject|null
     */
    private function getResourceIncludeByType(array $includes, string $type): ?ResourceObject
    {
        foreach ($includes as $include) {
            if ($include->type() === $type) {
                return $include;
            }
        }

        return null;
    }

    private function getResourceIncludeByAttributeKey(
        array $includes,
        string $type,
        string $attributeKey,
        string $resourceId
    ): ?ResourceObject {
        foreach ($includes as $include) {
            if ($include->type() === $type && $include->attributes()[$attributeKey] === $resourceId) {
                return $include;
            }
        }

        return null;
    }
}
