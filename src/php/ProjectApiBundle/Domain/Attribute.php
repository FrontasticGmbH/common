<?php

namespace Frontastic\Common\ProjectApiBundle\Domain;

use Kore\DataObject\DataObject;

class Attribute extends DataObject
{
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_NUMBER = 'number';
    const TYPE_MONEY = 'money';
    const TYPE_REFERENCE = 'reference';
    const TYPE_TEXT = 'text';
    const TYPE_LOCALIZED_TEXT = 'localizedText';
    const TYPE_ENUM = 'enum';
    const TYPE_LOCALIZED_ENUM = 'localizedEnum';
    const TYPE_CATEGORY_ID = 'categoryId';

    /**
     * @var string
     */
    public $attributeId;

    /**
     * @var string TYPE_*
     */
    public $type;

    /**
     * @var array<string, string>|null The labels with the locale as key and the actual label as value. `null` if the
     *     label is unknown
     */
    public $label;

    /**
     * @var array|null
     */
    public $values;
}
