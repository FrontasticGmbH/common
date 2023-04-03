<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use Seld\JsonLint\JsonParser;

use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use Frontastic\Common\CoreBundle\Domain\Json\Json;
use Frontastic\Common\SpecificationBundle\Domain\SchemaError;

class JsonSchemaValidator
{
    private const SCHEMA_DIRECTORY = __DIR__ . '/../../../json';
    private const SCHEMA_PROPERTIES = [
        "tasticType",
        "dynamicPageType",
        "dataSourceType",
        "customStreamType",
        "customDataSourceType",
        "identifier",
    ];
    // Field properties and their corresponding patterns
    private const FIELD_PROPERTIES = [
        "dynamicFilterEndpoint" => "starts with /",
    ];

    /**
     * @return array Array of errors, empty if valid
     */
    public function validate($toValidate, string $schemaFile, array $schemaLibraryFiles = []): array
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
        $this->checkSchemaSyntax($toParse);

        $schemaToValidate = $this->checkSchemaType($toParse);

        $errors = $this->validate($schemaToValidate, $schemaFile, $schemaLibraryFiles);
        if (count($errors) > 0) {
            throw new InvalidSchemaException(
                "JSON does not follow schema.",
                implode(
                    "\n",
                    array_map(
                        function (array $error): string {
                            /** @var SchemaError $schemaError */
                            $schemaError = $this->createSchemaError($error);

                            switch ($schemaError->errorFlag) {
                                case 'invalidValueType':
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
                                    break;
                                case 'missingProperty':
                                    return sprintf(
                                        "* %s A property %s is required. Add %s property.",
                                        $schemaError->errorIndex ? $schemaError->errorIndex . ":" :
                                            "One of your properties is missing.",
                                        $schemaError->propertyName,
                                        $schemaError->propertyName
                                    );
                                    break;
                                case 'invalidFieldType':
                                    return sprintf(
                                        "* %s: Field type doesn't have a valid value. " .
                                            "Check that the field type matches the value type.",
                                        $error['property']
                                    );
                                    break;
                                case 'notTranslatableField':
                                    return sprintf(
                                        "* %s: Field type isn't translatable.",
                                        $error['property']
                                    );
                                    break;
                                case 'invalidSchemaProperty':
                                    return sprintf(
                                        "* %s is a required field. You need to input a %s.",
                                        $schemaError->propertyName,
                                        $schemaError->propertyName
                                    );
                                    break;
                                case 'invalidFieldProperty':
                                    return sprintf(
                                        "* %s is a required field. You need to input a %s that %s.",
                                        $schemaError->propertyName,
                                        $schemaError->propertyName,
                                        self::FIELD_PROPERTIES[$schemaError->propertyName]
                                    );
                                    break;
                                case 'unsupportedProperty':
                                    return $error['message'];
                                case 'stringTooLong':
                                    return '* ' . $schemaError->propertyName . ': ' . $error['message'];
                                case 'reservedFieldName':
                                    return "* You've used a reserved field name." .
                                        " Reserved field names are " .
                                        "password, token, id, sequence, locale, or is_deleted." .
                                        "You must change the field name where you've used the reserved word.";
                                    break;
                                default:
                                    return "";
                            }
                        },
                        $errors
                    )
                )
            );
        }

        // Dynamic filter response schema is an array not an object
        // Type casting is needed to avoid breaking the codebase
        return (object)$schemaToValidate;
    }

    private function checkSchemaType(string $schemaToDecode)
    {
        $schema = Json::decode($schemaToDecode);
        if (!is_object($schema) && !is_array($schema)) {
            throw new InvalidSchemaException(
                "JSON does not follow schema.",
                "JSON does not parse to object but " . gettype($schema) . " instead."
            );
        }

        return $schema;
    }

    private function checkSchemaSyntax(string $toParse)
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
    }

    // Schema Error factory
    private function createSchemaError(array $error): SchemaError
    {
        $errorFlag = "";

        if (str_contains($error['message'], "value found")) {
            $errorFlag = "invalidValueType";
        } elseif (str_contains($error['message'], "Must be at most") &&
            str_contains($error['message'], "characters long")
        ) {
            $errorFlag = "stringTooLong";
        } elseif (str_contains($error['message'], "required")) {
            $errorFlag = "missingProperty";
        } elseif (str_contains($error['message'], "enumeration")) {
            if (str_contains($error['property'], "translatable")) {
                $errorFlag = "notTranslatableField";
            } else {
                $errorFlag = "invalidFieldType";
            }
        } elseif (array_search($error['property'], self::SCHEMA_PROPERTIES) !== false) {
            $errorFlag = "invalidSchemaProperty";
        } elseif (str_contains($error['message'], "additional properties")) {
            $errorFlag = "unsupportedProperty";
        } elseif (str_contains($error['message'], "Matched a schema which it should not") &&
            strpos($this->extractPropertyName($error['property']), "fields") === false
        ) {
            $errorFlag = "reservedFieldName";
        } else {
            foreach (self::FIELD_PROPERTIES as $fileProperty => $pattern) {
                if (str_contains($error['property'], $fileProperty)) {
                    $errorFlag = "invalidFieldProperty";
                    break;
                }
            }
        }

        return new SchemaError([
            "propertyName" => $this->extractPropertyName($error['property']),
            "errorIndex" => $this->extractErrorPlaceInSchema($error['property']),
            "errorFlag" => $errorFlag
        ]);
    }

    private function extractErrorPlaceInSchema(string $errorProperty): string
    {
        $errorPlaceInSchema = join(
            ".",
            array_slice(explode('.', $errorProperty), 0, array_key_last(explode('.', $errorProperty)))
        );

        // Add the word "field" prior to the actual string. For instance, [2] -> field[2]
        return preg_replace(
            "/^(\[)\d*(\])$/",
            "field" . $errorPlaceInSchema,
            $errorPlaceInSchema
        );
    }

    private function extractPropertyName(string $errorProperty): string
    {
        return explode('.', $errorProperty)[array_key_last(explode('.', $errorProperty))];
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
