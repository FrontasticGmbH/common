<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain\Locale;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class SapLocale extends DataObject
{
    /**
     * ISO code
     *
     * @var string
     * @required
     */
    public $languageCode;

    /**
     * ISO code
     *
     * @var string
     * @required
     */
    public $currencyCode;

    public function toQueryParameters(): array
    {
        return [
            'lang' => $this->languageCode,
            'curr' => $this->currencyCode,
        ];
    }
}
