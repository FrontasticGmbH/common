<?php

namespace Frontastic\Common\FindologicBundle\Domain\ProductSearchApi;

use Kore\DataObject\DataObject;

class ValidationResult extends DataObject
{
    /**
     * @var bool
     */
    public $isSupported;

    /**
     * @var ?string
     */
    public $validationError = null;

    public static function createValid(): ValidationResult
    {
        return new self(['isSupported' => true]);
    }

    public static function createUnsupported(string $message): ValidationResult
    {
        return new self([
            'isSupported' => false,
            'validationError' => $message,
        ]);
    }
}
