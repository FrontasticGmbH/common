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
    private const SCHEMA_PROPERTIES = [
        "tasticType",
        "dynamicPageType",
        "dataSourceType",
        "customStreamType",
        "customDataSourceType",
        "identifier"
    ];
    
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
                sprintf(
                    "There's a syntax error %s. Check your syntax on that line.",
                    str_replace(
                        ":",
                        ".",
                        substr(
                            $exception->getMessage(),
                            strpos($exception->getMessage(), "on line"),
                            strpos($exception->getMessage(), ":") - strpos($exception->getMessage(), "on line")
                        )
                    )
                )
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
                    array_filter(
                        array_map(
                            function (array $error): string {
                                if (str_contains($error['message'], "value") &&
                                    str_contains($error['message'], "enumeration") == false) {
                                    return sprintf(
                                        "* %s: The %s value type doesn't match the correct type. " .
                                        "You have inputted %s value but you need to input %s.",
                                        $error['property'],
                                        explode(
                                            '.',
                                            $error['property']
                                        )[array_key_last(explode('.', $error['property']))],
                                        strtolower(explode(" ", $error['message'])[0]),
                                        explode(" ", $error['message'])[5]
                                    );
                                } elseif (str_contains($error['message'], "required")) {
                                    $errorMessage = sprintf(
                                        "* %s: A property %s is required. Add %s property.",
                                        join(
                                            ".",
                                            array_slice(
                                                explode(
                                                    '.',
                                                    $error['property']
                                                ),
                                                0,
                                                array_key_last(explode('.', $error['property']))
                                            )
                                        )?:"One of your properties is missing",
                                        explode(
                                            '.',
                                            $error['property']
                                        )[array_key_last(explode('.', $error['property']))],
                                        explode(
                                            '.',
                                            $error['property']
                                        )[array_key_last(explode('.', $error['property']))]
                                    );

                                    if (str_contains($errorMessage, "missing")) {
                                        return str_replace(':', ".", $errorMessage);
                                    } else {
                                        return $errorMessage;
                                    }
                                } elseif (str_contains($error['message'], "enumeration")) {
                                    return sprintf(
                                        "* %s: Field type doesn't have a valid value. " .
                                        "Check that the field type matches the value type.",
                                        $error['property']
                                    );
                                } elseif (array_search(
                                    $error['property'],
                                    JsonSchemaValidator::SCHEMA_PROPERTIES
                                ) !== false) {
                                    return sprintf(
                                        "* %s is a required field. You need to input a %s.",
                                        $error['property'],
                                        $error['property']
                                    );
                                } else {
                                    return "";
                                }
                            },
                            $errors
                        )
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
