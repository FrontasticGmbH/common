<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class CustomDataSourceSpecParser extends ValidatingSpecParser
{
    public function __construct()
    {
        parent::__construct('customDataSourceSchema.json');
    }
}
