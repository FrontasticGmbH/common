<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class DynamicFiltersResponseSpecParser extends ValidatingSpecParser
{
    public function __construct()
    {
        parent::__construct('dynamicFiltersResponseSchema.json');
    }
}
