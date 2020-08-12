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

    public function __construct(SprykerProjectConfigApi $projectConfigApi)
    {
        $this->projectConfigApi = $projectConfigApi;
    }

    public function createLocaleFromString(string $localeString): SprykerLocale
    {
        $projectConfig = $this->fetchProjectConfig();

        $locale = Locale::createFromPosix($localeString);

        [$language, $languageId] = $this->pickLanguageFromProjectConfig(
            $this->extractProjectConfigResource(
                $projectConfig,
                SprykerProjectConfigApi::RESOURCE_LANGUAGES
            ),
            $locale
        );
        [$country, $countryId] = $this->pickCountryFromProjectConfig(
            $this->extractProjectConfigResource(
                $projectConfig,
                SprykerProjectConfigApi::RESOURCE_COUNTRIES
            ),
            $locale
        );
        [$currency, $currencyId] = $this->pickCurrencyFromProjectConfig(
            $this->extractProjectConfigResource(
                $projectConfig,
                SprykerProjectConfigApi::RESOURCE_CURRENCIES
            ),
            $locale
        );

        return new SprykerLocale([
            'language' => $language,
            'languageId' => $languageId,
            'country' => $country,
            'countryId' => $countryId,
            'currency' => $currency,
            'currencyId' => $currencyId,
        ]);
    }

    private function extractProjectConfigResource(array $projectConfig, string $resourceName): array
    {
        return $projectConfig[$resourceName] ?? [];
    }

    private function fetchProjectConfig(): array
    {
        return $this->projectConfigApi->getProjectConfig();
    }

    /**
     * @param \Frontastic\Common\SprykerBundle\Domain\ProjectConfigApi\SprykerLanguage[] $projectConfigLanguages
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale $frontasticLocale
     *
     * @return [?string, ?string]
     */
    private function pickLanguageFromProjectConfig(array $projectConfigLanguages, Locale $frontasticLocale): array
    {
        $locale = sprintf('%s-%s', $frontasticLocale->language, $frontasticLocale->territory);

        foreach ($projectConfigLanguages as $projectConfigLanguage) {
            if ($projectConfigLanguage->localeCode === $locale) {
                return [$projectConfigLanguage->localeCode, $projectConfigLanguage->id];
            }
        }

        return [null, null];
    }

    /**
     * @param \Frontastic\Common\SprykerBundle\Domain\ProjectConfigApi\SprykerCountry[] $projectConfigCountries
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale $frontasticLocale
     *
     * @return [?string, ?string]
     */
    private function pickCountryFromProjectConfig(array $projectConfigCountries, Locale $frontasticLocale): array
    {
        foreach ($projectConfigCountries as $projectConfigCountry) {
            if ($projectConfigCountry->iso === $frontasticLocale->territory) {
                return [$projectConfigCountry->name, $projectConfigCountry->id];
            }
        }

        return [null, null];
    }

    /**
     * @param \Frontastic\Common\SprykerBundle\Domain\ProjectConfigApi\SprykerCurrency[] $projectConfigCurrencies
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale $frontasticLocale
     *
     * @return [?string, ?string]
     */
    private function pickCurrencyFromProjectConfig(array $projectConfigCurrencies, Locale $frontasticLocale): array
    {
        foreach ($projectConfigCurrencies as $projectConfigCurrency) {
            if ($projectConfigCurrency->isoCode === $frontasticLocale->currency) {
                return [$projectConfigCurrency->isoCode, $projectConfigCurrency->id];
            }
        }

        return [null, null];
    }
}
