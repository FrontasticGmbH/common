<?php

namespace Frontastic\Common\SprykerBundle\Domain;

use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

interface MapperInterface
{
    /**
     * @param ResourceObject $resource
     * @return mixed
     */
    public function mapResource(ResourceObject $resource);

    /**
     * @return string
     */
    public function getName(): string;
}
