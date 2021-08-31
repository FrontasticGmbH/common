<?php

namespace Frontastic\Common\SpecificationBundle\Domain\Schema;

interface FieldVisitor
{
    /**
     * Note: You can, but you don't need to take care of nested "group" values, those will be visited, too!
     *
     * @param FieldConfiguration $configuration
     * @param $value
     * @return mixed Processed version of $value
     */
    public function processField(FieldConfiguration $configuration, $value);
}
