<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class CellSpecParser implements SpecParser
{
    /**
     * @var JsonSchemaValidator
     */
    private $validator;

    public function __construct()
    {
        $this->validator = new JsonSchemaValidator();
    }

    public function parse(string $schema): \StdClass
    {
        return $this->validator->parse(
            $schema,
            'cellSchema.json',
            ['library/common.json']
        );
    }
}
