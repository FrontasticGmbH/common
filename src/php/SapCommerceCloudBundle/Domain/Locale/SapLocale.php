<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain\Locale;

use Kore\DataObject\DataObject;

class SapLocale extends DataObject
{
    /**
     * @var string ISO code
     */
    public $languageCode;

    /** @var string ISO code */
    public $currencyCode;

    public function toQueryParameters(): array
    {
        return [
            'lang' => $this->languageCode,
            'curr' => $this->currencyCode,
        ];
    }
}
