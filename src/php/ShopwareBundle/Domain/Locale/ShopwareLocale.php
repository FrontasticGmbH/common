<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Locale;

use Kore\DataObject\DataObject;

class ShopwareLocale extends DataObject
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
