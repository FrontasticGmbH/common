<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareCountry;

class CountryMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'country';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $resource)
    {
        $countryData = $this->extractData($resource, $resource);

        $country = new ShopwareCountry($countryData, true);
        $country->name = $this->resolveTranslatedValue($countryData, 'name');

        return $country;
    }
}
