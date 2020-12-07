<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class CustomStreamSpecParser extends ValidatingSpecParser
{
    public function __construct()
    {
        parent::__construct('customStreamSchema.json');
    }
}
