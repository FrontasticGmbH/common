<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain\Locale;

use Kore\DataObject\DataObject;

class SapLocale extends DataObject
{
    /**
     * ISO code
     *
     * @var string
     */
    public $languageCode;

    /**
     * ISO code
     *
     * @var string
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
