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
    public $countryId;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $currencyId;

    /**
     * @var string
     */
    public $language;

    /**
     * @var string
     */
    public $languageId;
}
