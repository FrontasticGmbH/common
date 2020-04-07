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

    public function getProjectConfig(): array;
}
