<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class NodeSpecParser
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
            'nodeSchema.json',
            ['schema/common.json']
        );
    }
}
