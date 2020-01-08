<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale;

use Kore\DataObject\DataObject;

class CommercetoolsLocale extends DataObject
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
