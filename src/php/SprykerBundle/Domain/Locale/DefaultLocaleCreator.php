<?php

namespace Frontastic\Common\SprykerBundle\Domain\Locale;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\SprykerBundle\Domain\ProjectConfig\SprykerProjectConfigApi;

class DefaultLocaleCreator extends LocaleCreator
{
    /**
     * @var SprykerProjectConfigApi
     */
    private $projectConfigApi;

    /**
     * @var bool
     */
    private $projectConfigFetched = false;

    /**
     * @var string[]
     */
    private $locales = null;

    /**
     * @var string[]
     */
    private $countries = null;

    /**
     * @var string[]
     */
    private $currencies = null;

    public function __construct(SprykerProjectConfigApi $projectConfigApi)
    {
        $this->projectConfigApi = $projectConfigApi;
    }

    public function createLocaleFromString(string $localeString): SprykerLocale
    {
        $this->fetchProjectConfig();

        $locale = Locale::createFromPosix($localeString);

        $language = $this->pickLanguage($locale);
        $country = $this->pickCountry($locale);
        $currency= $this->pickCurrency($locale);

        return new SprykerLocale([
            'language' => $language,
            'country' => $country,
            'currency' => $currency,
        ]);
    }

    private function fetchProjectConfig(): void
    {
        if ($this->projectConfigFetched) {
            return;
        }

        $result = $this->projectConfigApi->getProjectConfig();

        foreach (['locales', 'countries', 'currencies'] as $property) {
            if (!array_key_exists($property, $result)) {
                throw new \RuntimeException('Spryker has no ' . $property . ' configured');
            }
            $values = $result[$property];
            if (!is_array($values)) {
                throw new \RuntimeException('Invalid JSON');
            }
            if (empty($values)) {
                throw new \RuntimeException('Spryker has no ' . $property . ' configured');
            }

            $this->$property = $values;
        }

        $this->projectConfigFetched = true;
    }

    private function pickLanguage(Locale $frontasticLocale): string
    {
        $locale = sprintf('%s_%s', $frontasticLocale->language, $frontasticLocale->territory);

        foreach ($this->locales as $projectConfigLanguage) {
            if ($projectConfigLanguage['name'] === $locale) {
                return $projectConfigLanguage['code'];
            }
        }

        foreach ($this->locales as $projectConfigLanguage) {
            if ($projectConfigLanguage['code'] === $frontasticLocale->language) {
                return $projectConfigLanguage['code'];
            }
        }

        return $this->locales[0]['code'];
    }

    private function pickCountry(Locale $frontasticLocale): string
    {
        foreach ($this->countries as $projectConfigCountry) {
            if ($projectConfigCountry['iso2Code'] === $frontasticLocale->territory) {
                return $projectConfigCountry['iso2Code'];
            }
        }

        return $this->countries[0]['iso2Code'];
    }

    private function pickCurrency(Locale $frontasticLocale): string
    {
        foreach ($this->currencies as $projectConfigCurrency) {
            if ($projectConfigCurrency['code'] === $frontasticLocale->currency) {
                return $projectConfigCurrency['code'];
            }
        }

        return $this->currencies[0]['code'];
    }
}
