<?php

namespace Frontastic\Common\JsonSerializer;

interface ObjectEnhancer
{
    /**
     * @param object $object
     * @return array Map of properties to add to the serialization of $object
     */
    public function enhance($object): array;
}
