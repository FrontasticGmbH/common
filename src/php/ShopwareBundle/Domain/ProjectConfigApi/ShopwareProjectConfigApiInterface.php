<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

interface ShopwareProjectConfigApiInterface
{
    public const RESOURCE_COUNTRIES = 'countries';
    public const RESOURCE_CURRENCIES = 'currencies';
    public const RESOURCE_LANGUAGES = 'languages';
    public const RESOURCE_PAYMENT_METHODS = 'payment-methods';
    public const RESOURCE_SALUTATIONS = 'salutations';
    public const RESOURCE_SHIPPING_METHODS = 'shipping-methods';

    /**
     * @param string $criteria - can be ISO2 country code, ISO3 country code, or country name
     *
     * @return \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareCountry
     */
    public function getCountryByCriteria(string $criteria): ShopwareCountry;

    /**
     * @return \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwarePaymentMethod[]
     */
    public function getPaymentMethods(): array;

    public function getProjectConfig(): array;

    public function getSalutation(string $salutationKey): ?ShopwareSalutation;

    /**
     * @return \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareSalutation
     */
    public function getSalutations(?string $salutationKey = null): array;

    /**
     * @return \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareShippingMethod[]
     */
    public function getShippingMethods(): array;
}
