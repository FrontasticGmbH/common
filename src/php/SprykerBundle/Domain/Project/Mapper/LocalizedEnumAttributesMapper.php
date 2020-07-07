<?php

namespace Frontastic\Common\SprykerBundle\Domain\Project\Mapper;

use Frontastic\Common\ProjectApiBundle\Domain\Attribute;

class LocalizedEnumAttributesMapper
{
    private const DEFAULT_LOCALE = 'de_DE';

    public function process(Attribute $attribute): void
    {
        if ($attribute->type !== Attribute::TYPE_LOCALIZED_ENUM) {
            return;
        }

        $this->processLabel($attribute);
        $this->processValues($attribute);
    }

    /**
     * @param \Frontastic\Common\ProjectApiBundle\Domain\Attribute $attribute
     *
     * @return void
     */
    private function processLabel(Attribute $attribute): void
    {
        if ($attribute->label && !is_array($attribute->label)) {
            $attribute->label = $this->formatLabel((string)$attribute->label);
        }
    }

    /**
     * @param string $value
     *
     * @return array
     */
    private function formatLabel(string $value): array
    {
        return [
            self::DEFAULT_LOCALE => $value,
        ];
    }

    /**
     * @param \Frontastic\Common\ProjectApiBundle\Domain\Attribute $attribute
     *
     * @return void
     */
    private function processValues(Attribute $attribute):void
    {
        $attribute->values = array_map([$this, 'formatValue'], $attribute->values);
    }

    /**
     * @param string|null $value
     *
     * @return array
     */
    private function formatValue(?string $value): array
    {
        return [
            'key' => $value,
            'label' => $this->formatLabel($value),
        ];
    }
}
