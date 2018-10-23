<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use JsonSchema\Validator;
use Seld\JsonLint\JsonParser;

class TasticSpecParser
{
    public function parse(string $schema): \StdClass
    {
        $jsonParser = new JsonParser();
        if ($exception = $jsonParser->lint($schema)) {
            throw new InvalidSchemaException(
                "Failed to parse JSON.",
                $exception->getMessage()
            );
        }

        $validator = new Validator();
        $schema = json_decode($schema);
        $validator->validate(
            $schema,
            json_decode(file_get_contents(__DIR__ . '/../Resources/tasticSchema.json'))
        );

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

        return $schema;
    }
}
