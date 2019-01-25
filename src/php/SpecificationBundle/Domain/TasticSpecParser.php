<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class TasticSpecParser implements SpecParser
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
            'tasticSchema.json',
            ['library/common.json']
        );
    }
}
