<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class ValidatingSpecParser implements SpecParser
{
    /**
     * @var JsonSchemaValidator
     */
    private $validator;

    /**
     * @var string
     */
    private $schemaFile;

    public function __construct(string $schemaFile)
    {
        $this->schemaFile = $schemaFile;
        $this->validator = new JsonSchemaValidator();
    }

    public function parse(string $schema): \stdClass
    {
        $schema = $this->validator->parse(
            $schema,
            $this->schemaFile,
            ['library/common.json']
        );

        return $this->verifySchema($schema);
    }

    protected function verifySchema(\stdClass $schema): \stdClass
    {
        return $schema;
    }
}
