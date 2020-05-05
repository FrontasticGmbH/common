<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;

class CountriesMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'countries';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper\CountryMapper
     */
    private $countryMapper;

    public function __construct(CountryMapper $countryMapper)
    {
        $this->countryMapper = $countryMapper;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $countriesData = $this->extractData($resource);

        $result = [];
        foreach ($countriesData as $countryData) {
            $result[] = $this->countryMapper->map($countryData);
        }

        return $result;
    }
}
