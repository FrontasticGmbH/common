<?php

namespace Frontastic\Common\SprykerBundle\Domain\Locale;

use Kore\DataObject\DataObject;

class SprykerLocale extends DataObject
{
    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $language;
}
