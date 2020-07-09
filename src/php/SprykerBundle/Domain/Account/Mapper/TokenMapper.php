<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account\Mapper;

use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class TokenMapper implements MapperInterface
{
    public const MAPPER_NAME = 'token';

    /**
     * @param ResourceObject $resource
     * @return string
     */
    public function mapResource(ResourceObject $resource):string
    {
       return $resource->attribute('accessToken');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::MAPPER_NAME;
    }
}
