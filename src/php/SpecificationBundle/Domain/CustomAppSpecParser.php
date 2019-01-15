<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use JsonSchema\Validator;
use Seld\JsonLint\JsonParser;

class CustomAppSpecParser
{
    const SCHEMA_FILE = __DIR__ . '/../../../json/appSchema.json';

    public function parse(string $schema): \StdClass
    {
        $jsonParser = new JsonParser();
        if ($exception = $jsonParser->lint($schema)) {
            throw new InvalidSchemaException(
                "Failed to parse JSON.",
                $exception->getMessage()
            );
        }

        $jsonSchema = file_get_contents(self::SCHEMA_FILE);
        if ($exception = $jsonParser->lint($jsonSchema)) {
            throw new InvalidSchemaException(
                "Failed to parse JSON Schema.",
                $exception->getMessage()
            );
        }

        $validator = new Validator();
        $schema = json_decode($schema);
        $validator->validate($schema, json_decode($jsonSchema));

        if (!$validator->isValid()) {
            throw new InvalidSchemaException(
                "JSON does not follow schema.",
                implode(
                    "\n",
                    array_map(
                        function (array $error): string {
                            return sprintf(
                                "* %s: %s",
                                $error['property'],
                                $error['message']
                            );
                        },
                        $validator->getErrors()
                    )
                )
            );
        }

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
