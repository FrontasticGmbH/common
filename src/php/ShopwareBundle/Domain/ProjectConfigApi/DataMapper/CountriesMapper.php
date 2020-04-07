<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareCountry;

class CountriesMapper implements DataMapperInterface
{
    public const MAPPER_NAME = 'countries';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $resource)
    {
        $result = [];
        foreach ($resource as $countryData) {
            $result[] = $this->mapDataToShopwareCountry($countryData);
        }

        return $result;
    }

    private function mapDataToShopwareCountry(array $countryData): ShopwareCountry
    {
        $country = new ShopwareCountry($countryData, true);
        $country->name = $countryData['translated']['name'] ?? $countryData['name'];

        return $country;
    }
}
