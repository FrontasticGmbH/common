<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class DataSourceSchemaSpecParser extends ValidatingSpecParser
{
    public function __construct()
    {
        parent::__construct('dataSourceResultSchema.json');
    }
}
