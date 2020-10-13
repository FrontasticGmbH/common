<?php

namespace Frontastic\Common\ProjectApiBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * @type
 */
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

    const TYPES = [
        self::TYPE_BOOLEAN,
        self::TYPE_NUMBER,
        self::TYPE_MONEY,
        self::TYPE_REFERENCE,
        self::TYPE_TEXT,
        self::TYPE_LOCALIZED_TEXT,
        self::TYPE_ENUM,
        self::TYPE_LOCALIZED_ENUM,
        self::TYPE_CATEGORY_ID,
    ];

    /**
     * @var string
     * @required
     */
    public $attributeId;

    /**
     * TYPE_*
     *
     * @var string
     * @required
     */
    public $type;

    /**
     * The labels with the locale as key and the actual label as value. `null`
     * if the label is unknown
     *
     * @var array<string, string>|null
     */
    public $label;

    /**
     * @var ?array
     */
    public $values;
}
