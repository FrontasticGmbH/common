<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class MasterPageSchemaSpecParser extends ValidatingSpecParser
{
    public function __construct()
    {
        parent::__construct('masterPageSchemaSchema.json');
    }
}
