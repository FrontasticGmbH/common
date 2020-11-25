<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class CommercetoolsLocale extends ApiDataObject
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
