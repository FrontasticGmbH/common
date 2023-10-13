<?php

namespace Frontastic\Common\CoreBundle\Domain;

class Versioner
{
    public function supports(object $entity): bool
    {
        return property_exists($entity, 'versions');
    }

    public function versionSnapshot(object $entity): void
    {
        $oldEntity = clone $entity;
        $oldEntity->versions = [];

        $entity->versions = $entity->versions ?: [];
        array_unshift($entity->versions, $oldEntity);

        $entity->versions = array_slice($entity->versions, 0, 32);
    }
}
