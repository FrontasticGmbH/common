<?php

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

interface ShopwareProjectConfigApiInterface
{
    public const RESOURCE_COUNTRIES = 'countries';
    public const RESOURCE_CURRENCIES = 'currencies';
    public const RESOURCE_LANGUAGES = 'languages';
    public const RESOURCE_PAYMENT_METHODS = 'payment-methods';
    public const RESOURCE_SALUTATIONS = 'salutations';

    /**
     * @param string $criteria - can be ISO2 country code, ISO3 country code, or country name
     *
     * @return \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareCountry|null
     */
    public function getCountryByCriteria(string $criteria): ?ShopwareCountry;

    public function getCurrency(string $currencyId): ?ShopwareCurrency;

    /**
     * @return \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwarePaymentMethod[]
     */
    public function getPaymentMethods(): array;

    public function getProjectConfig(): array;

    public function getSalutation(string $criteria): ?ShopwareSalutation;

    /**
     * @return \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareSalutation
     */
    public function getSalutations(?string $criteria = null, ?string $locale = null): array;
}
