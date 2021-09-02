<?php

namespace Frontastic\Common\SpecificationBundle\Domain\Schema;

interface FieldVisitor
{
    /**
     * Note: You can, but you don't need to take care of nested "group" values,
     * those will be visited, too! Note that nested values are visited first,
     * then the group itself.
     *
     * @param FieldConfiguration $configuration
     * @param $value
     * @param array $fieldPath Path of the field nesting e.g. ['groupField', 2] if this is the 3nd element in a group
     * @return mixed Processed version of $value
     */
    public function processField(FieldConfiguration $configuration, $value, array $fieldPath);
}
