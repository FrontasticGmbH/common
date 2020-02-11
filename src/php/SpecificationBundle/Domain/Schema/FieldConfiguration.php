<?php

namespace Frontastic\Common\SpecificationBundle\Domain\Schema;

class FieldConfiguration
{
    private const DEFAULT_VALUES = [
        'group' => [],
        'decimal' => 0,
        'integer' => 0,
        'float' => 0,
        'number' => 0,
        'string' => '',
        'text' => '',
        'markdown' => '',
        'json' => '{}',
        'boolean' => false,
    ];

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $type;

    private $default;

    public function __construct(string $field, string $type, $default)
    {
        $this->field = $field;
        $this->type = $type;
        $this->default = $default;
    }

    public static function fromSchema(array $fieldSchema): FieldConfiguration
    {
        $type = self::getSchemaString($fieldSchema, 'type', 'text');

        return new FieldConfiguration(
            self::getRequiredSchemaString($fieldSchema, 'field'),
            $type,
            self::getSchemaValue($fieldSchema, 'default', self::DEFAULT_VALUES[$type] ?? null)
        );
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDefault()
    {
        return $this->default;
    }

    private static function getRequiredSchemaString(array $schema, string $key): string
    {
        $value = self::getSchemaString($schema, $key, null);
        if ($value === null) {
            throw new \InvalidArgumentException('Required schema field "' . $key . '" is missing');
        }

        return $value;
    }

    private static function getSchemaString(array $schema, string $key, ?string $default): ?string
    {
        if (!array_key_exists($key, $schema)) {
            return $default;
        }

        $value = $schema[$key];
        if (!is_string($value)) {
            throw new \InvalidArgumentException('"' . $key . '" needs to be a string');
        }

        return $value;
    }

    private static function getSchemaValue(array $schema, string $key, $default)
    {
        if (!array_key_exists($key, $schema)) {
            return $default;
        }

        return $schema[$key];
    }
}
