<?php declare(strict_types=1);

namespace Frontastic\Common\ShopwareBundle\Domain\Locale;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectApi;

class LocaleCreator
{
    private const AVAILABLE_RESOURCES = [
        'languages', 'countries', 'currencies'
    ];

    /**
     * @var \Frontastic\Common\ProjectApiBundle\Domain\ProjectApi|\Frontastic\Common\ProjectApiBundle\Domain\ProjectConfigApi
     */
    private $projectConfigApi;

    private $projectConfigFetched = false;

    /**
     * @param \Frontastic\Common\ProjectApiBundle\Domain\ProjectApi|\Frontastic\Common\ProjectApiBundle\Domain\ProjectConfigApi $projectConfigApi
     */
    public function __construct(ProjectApi $projectConfigApi)
    {
        $this->projectConfigApi = $projectConfigApi;
    }

    public function createLocaleFromString(string $localeString): ShopwareLocale
    {
        $this->fetchProjectConfig();

        $locale = Locale::createFromPosix($localeString);

        // @TODO: implement pickers from project configuration
        $language = $locale->language;
        $country = $locale->country;
        $currency = $locale->currency;

        return new ShopwareLocale([
            'language' => $language,
            'country' => $country,
            'currency' => $currency,
        ]);
    }

    private function fetchProjectConfig(): void
    {
        if ($this->projectConfigFetched === true) {
            return;
        }

        $result = $this->projectConfigApi->getProjectConfig();

        foreach (self::AVAILABLE_RESOURCES as $property) {
            if (!array_key_exists($property, $result)) {
                throw new \RuntimeException('Shopware has no ' . $property . ' configured');
            }
            $values = $result[$property];
            if (!is_array($values)) {
                throw new \RuntimeException('Invalid JSON');
            }

            if (count($values) === 0) {
                throw new \RuntimeException('Shopware has no ' . $property . ' configured');
            }

            $this->$property = $values;
        }

        $this->projectConfigFetched = true;
    }
}
