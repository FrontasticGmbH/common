<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class CommercetoolsLocale extends DataObject
{
    /**
     * @var string
     * @required
     */
    public $country;

    /**
     * @var string
     * @required
     */
    public $currency;

    /**
     * @var string
     * @required
     */
    public $language;
}
