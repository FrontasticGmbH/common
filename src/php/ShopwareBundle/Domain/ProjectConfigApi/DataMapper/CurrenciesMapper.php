<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareCurrency;

class CurrenciesMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'currencies';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $resource)
    {
        $currenciesData = $this->extractData($resource);

        $result = [];
        foreach ($currenciesData as $currencyData) {
            $result[] = $this->mapDataToShopwareCurrency($currencyData);
        }

        return $result;
    }

    private function mapDataToShopwareCurrency(array $currencyData): ShopwareCurrency
    {
        $currency = new ShopwareCurrency($currencyData, true);
        $currency->name = $currencyData['translated']['name'] ?? $currencyData['name'];
        $currency->shortName = $currencyData['translated']['shortName'] ?? $currencyData['shortName'];

        return $currency;
    }
}
