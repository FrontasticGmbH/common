<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Locale;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiInterface;

class DefaultLocaleCreator extends LocaleCreator
{
    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiInterface
     */
    private $projectConfigApi;

    public function __construct(ShopwareProjectConfigApiInterface $projectConfigApi)
    {
        $this->projectConfigApi = $projectConfigApi;
    }

    public function createLocaleFromString(string $localeString): ShopwareLocale
    {
        $projectConfig = $this->fetchProjectConfig();

        $locale = Locale::createFromPosix($localeString);

        [$language, $languageId] = $this->pickLanguageFromProjectConfig(
            $this->extractProjectConfigResource($projectConfig, ShopwareProjectConfigApiInterface::RESOURCE_LANGUAGES),
            $locale
        );
        [$country, $countryId] = $this->pickCountryFromProjectConfig(
            $this->extractProjectConfigResource($projectConfig, ShopwareProjectConfigApiInterface::RESOURCE_COUNTRIES),
            $locale
        );
        [$currency, $currencyId] = $this->pickCurrencyFromProjectConfig(
            $this->extractProjectConfigResource($projectConfig, ShopwareProjectConfigApiInterface::RESOURCE_CURRENCIES),
            $locale
        );

        return new ShopwareLocale([
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
     * @param \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareLanguage[] $projectConfigLanguages
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale $frontasticLocale
     *
     * @return array<?string, ?string>
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
     * @param \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareCountry[] $projectConfigCountries
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale $frontasticLocale
     *
     * @return array<?string, ?string>
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
     * @param \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareCurrency[] $projectConfigCurrencies
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale $frontasticLocale
     *
     * @return array<?string, ?string>
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
