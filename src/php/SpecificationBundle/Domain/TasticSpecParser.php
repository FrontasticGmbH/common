<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class TasticSpecParser extends ValidatingSpecParser
{
    public function __construct()
    {
        parent::__construct('tasticSchema.json');
    }
}
