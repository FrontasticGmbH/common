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
     * Keep in sync with paas/libraries/common/src/js/translate.js!
     */
    private const IS_DEFAULT_TRANSLATABLE = [
        'string' => true,
        'text' => true,
        'markdown' => true,
        'json' => true,
    ];

    private const SPECIAL_TYPE_CLASSES = [
        'group' => GroupFieldConfiguration::class,
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

    private bool $translatable;

    final private function __construct(string $field, string $type, $default, bool $translatable = false)
    {
        $this->field = $field;
        $this->type = $type;
        $this->default = $default;
        $this->translatable = $translatable;
    }

    public static function fromSchema(array $fieldSchema): FieldConfiguration
    {
        $type = self::getSchemaString($fieldSchema, 'type', 'text');

        $schemaClass = self::class;
        if (isset(self::SPECIAL_TYPE_CLASSES[$type])) {
            $schemaClass = self::SPECIAL_TYPE_CLASSES[$type];
        }
        return $schemaClass::doCreateFromSchema($type, $fieldSchema);
    }

    protected static function doCreateFromSchema(string $type, array $fieldSchema): FieldConfiguration
    {
        return new static(
            self::getRequiredSchemaString($fieldSchema, 'field'),
            $type,
            self::getSchemaValue($fieldSchema, 'default', self::DEFAULT_VALUES[$type] ?? null),
            self::getSchemaValue($fieldSchema, 'translatable', isset(self::IS_DEFAULT_TRANSLATABLE[$type]))
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

    public function processValueIfRequired($value, FieldVisitor $fieldVisitor, array $fieldPath)
    {
        return $fieldVisitor->processField($this, $value, $fieldPath);
    }

    public function isTranslatable()
    {
        return $this->translatable;
    }

    protected static function getRequiredSchemaString(array $schema, string $key): string
    {
        $value = self::getSchemaString($schema, $key, null);
        if ($value === null) {
            throw new \InvalidArgumentException('Required schema field "' . $key . '" is missing');
        }

        return $value;
    }

    protected static function getSchemaString(array $schema, string $key, ?string $default): ?string
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

    protected static function getSchemaValue(array $schema, string $key, $default)
    {
        if (!array_key_exists($key, $schema)) {
            return $default;
        }

        return $schema[$key];
    }
}
