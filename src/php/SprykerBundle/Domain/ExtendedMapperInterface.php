<?php

namespace Frontastic\Common\SprykerBundle\Domain;

interface ExtendedMapperInterface extends MapperInterface
{
    /**
     * @param array $resources
     * @return mixed
     */
    public function mapResourceArray(array $resources);
}
