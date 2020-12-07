<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use Seld\JsonLint\JsonParser;

use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use Frontastic\Common\CoreBundle\Domain\Json\Json;

class JsonSchemaValidator
{
    const SCHEMA_DIRECTORY = __DIR__ . '/../../../json';

    /**
     * @return array Array of errors, empty if valid
     */
    public function validate(object $toValidate, string $schemaFile, array $schemaLibraryFiles = []): array
    {
        $validator =  $this->createValidator($schemaLibraryFiles);
        $validator->validate(
            $toValidate,
            Json::decode(file_get_contents($this->schemaFilePath($schemaFile)))
        );

        if ($validator->isValid()) {
            return [];
        }
        return $validator->getErrors();
    }

    public function parse(string $toParse, string $schemaFile, array $schemaLibraryFiles = []): object
    {
        $jsonParser = new JsonParser();

        if ($exception = $jsonParser->lint($toParse)) {
            throw new InvalidSchemaException(
                "Failed to lint JSON.",
                $exception->getMessage()
            );
        }

        $object = Json::decode($toParse);
        if (!is_object($object)) {
            throw new InvalidSchemaException(
                "JSON does not follow schema.",
                "JSON does not parse to object but " . gettype($object) . " instead."
            );
        }

        $errors = $this->validate($object, $schemaFile, $schemaLibraryFiles);
        if (count($errors) > 0) {
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
                        $errors
                    )
                )
            );
        }

        return $object;
    }

    private function createValidator(array $schemaLibraryFiles): Validator
    {
        $schemaStorage = new SchemaStorage();

        foreach ($schemaLibraryFiles as $schemaFile) {
            $schemaJson = Json::decode(file_get_contents(
                $this->schemaFilePath($schemaFile)
            ));

            if (!isset($schemaJson->{'$id'}) && !isset($schemaJson->id)) {
                throw new \InvalidArgumentException(
                    sprintf('Schema "%s" has no ID', $schemaFile)
                );
            }

            $schemaStorage->addSchema(
                $schemaJson->{'$id'} ?? $schemaJson->id,
                $schemaJson
            );
        }

        $validator = new Validator(new Factory($schemaStorage));
        return $validator;
    }

    private function schemaFilePath(string $fileName): string
    {
        return self::SCHEMA_DIRECTORY . '/' . $fileName;
    }
}
