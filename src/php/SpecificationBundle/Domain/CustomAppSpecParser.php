<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class CustomAppSpecParser
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
        $schema = $this->validator->parse(
            $schema,
            'appSchema.json',
            ['schema/common.json']
        );

        $fields = $this->getVerifiedFieldArray($schema);
        $this->verifyDisplayedFields($schema, $fields);
        $this->verifyIndexedFields($schema, $fields);
        return $schema;
    }

    private function getVerifiedFieldArray(\StdClass $schema): array
    {
        $fields = [];
        foreach ($schema->schema as $group) {
            foreach ($group->fields as $field) {
                if (!isset($field->field)) {
                    continue;
                }

                if (isset($fields[$field->field])) {
                    throw new InvalidSchemaException(
                        "Inconsistent field definition.",
                        "Duplicate definition of field " . $field->field
                    );
                }

                $fields[$field->field] = $field->type;
            }
        }

        return $fields;
    }

    private function verifyDisplayedFields(\StdClass $schema, array $fields)
    {
        foreach ($schema->fields as $field) {
            if (!isset($fields[$field->field])) {
                throw new InvalidSchemaException(
                    "Invalid display field reference.",
                    "The field with name " . $field->field . " does not exist."
                );
            }
        }

        return $fields;
    }

    private function verifyIndexedFields(\StdClass $schema, array $fields)
    {
        $validIndexTypes = ["string", "integer", "boolean", "decimal"];

        foreach ($schema->indexes as $index) {
            foreach ($index->fields as $field) {
                if (!isset($fields[$field])) {
                    throw new InvalidSchemaException(
                        "Invalid index field reference.",
                        "The indexed field with name " . $field . " does not exist."
                    );
                }

                if (!in_array($fields[$field], $validIndexTypes)) {
                    throw new InvalidSchemaException(
                        "Invalid index field type.",
                        "The indexed field with name " . $field . " is of an invalid index type " .
                        $fields[$field] . " - supported types are: " . implode(', ', $validIndexTypes)
                    );
                }
            }
        }

        return $fields;
    }
}
