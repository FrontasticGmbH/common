<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class DynamicPageSpecParser extends ValidatingSpecParser
{
    public function __construct()
    {
        parent::__construct('dynamicPageSchema.json');
    }
}
