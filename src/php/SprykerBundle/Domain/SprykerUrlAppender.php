<?php

namespace Frontastic\Common\SprykerBundle\Domain;

class SprykerUrlAppender
{
    public function getSeparator(string $url): string
    {
        return (strpos($url, '?') === false) ? '?' : '&';
    }

    public function withCurrency(string $url, string $currency): string
    {
        $separator = $this->getSeparator($url);

        return "{$url}{$separator}currency={$currency}";
    }

    /**
     * @param string $url
     * @param array $includes
     *
     * @return string
     */
    public function withIncludes(string $url, array $includes = []): string
    {
        if (empty($includes)) {
            return $url;
        }

        $separator = $this->getSeparator($url);
        $includesString = implode(',', $includes);

        return "{$url}{$separator}include={$includesString}";
    }
}
