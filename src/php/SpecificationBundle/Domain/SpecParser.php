<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

interface SpecParser
{
    /**
     * @throws InvalidSchemaException
     */
    public function parse(string $schema): \stdClass;
}
