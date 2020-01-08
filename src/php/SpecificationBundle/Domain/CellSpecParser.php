<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class CellSpecParser extends ValidatingSpecParser
{
    public function __construct()
    {
        parent::__construct('cellSchema.json');
    }
}
