<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class NodeSpecParser extends ValidatingSpecParser
{
    public function __construct()
    {
        parent::__construct('nodeSchema.json');
    }
}
