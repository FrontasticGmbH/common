<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\Contentful;

use LocaleMapper;

class NoopLocaleMapper implements LocaleMapper
{
    /**
     * @param string $frontasticLocale
     * @return string Locale to be sent to the CMS (frontastic format)
     */
    public function replaceLocale(string $frontasticLocale): string
    {
        return $frontasticLocale;
    }
}
