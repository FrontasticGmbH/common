<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\AccountApi;

use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareSalutation;

class SalutationHelper
{
    private const SHOPWARE_SALUTATION_FALLBACK = 'not_specified';

    /**
     * Frontastic salutation variant => Shopware salutation key
     * @var string[]
     */
    private static $salutationMap = [
        'Herr' => 'mr',
        'Mr' => 'mr',
        'Mr.' => 'mr',
        'Frau' => 'mrs',
        'Mrs' => 'mrs',
        'Mrs.' => 'mrs',
        'Firma' => 'company', // Placeholder, by default does not exist in Shopware
        'Divers' => 'divers', // Placeholder, by default does not exist in Shopware
        'Other' => 'not_specified',
    ];

    public static function resolveFrontasticSalutation(?ShopwareSalutation $shopwareSalutation): ?string
    {
        if ($shopwareSalutation === null) {
            return null;
        }

        $frontasticSalutation = array_search($shopwareSalutation->salutationKey, self::$salutationMap, true);

        return $frontasticSalutation ?: $shopwareSalutation->displayName;
    }

    public static function resolveShopwareSalutation(string $frontasticSalutation): string
    {
        $frontasticSalutation = strtolower($frontasticSalutation);
        $salutationMapLowercaseKeys = array_change_key_case(self::$salutationMap);

        return $salutationMapLowercaseKeys[$frontasticSalutation] ?? self::SHOPWARE_SALUTATION_FALLBACK;
    }
}
