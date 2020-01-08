<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class ProjectSpecParser extends ValidatingSpecParser
{

    public function __construct()
    {
        parent::__construct('projectSchema.json');
    }
}
