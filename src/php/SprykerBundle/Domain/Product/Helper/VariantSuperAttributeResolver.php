<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Helper;

class VariantSuperAttributeResolver
{
    /**
     * @param string $sku
     * @param array $variantAttributeMap
     *
     * @return array
     */
    public static function resolveMapAttributes(string $sku, array $variantAttributeMap): array
    {
        $variantData = self::getVariantData($sku, $variantAttributeMap);
        $superAttributes = [];

        foreach ($variantAttributeMap['super_attributes'] as $key => $values) {
            $superAttributes[$key] = self::resolveSuperAttributeValue($key, $values, $variantData);
        }

        return $superAttributes;
    }

    /**
     * @param string $sku
     * @param array $variantAttributeMap
     *
     * @return array
     */
    private static function getVariantData(string $sku, array $variantAttributeMap): array
    {
        $variantData = [];
        foreach ($variantAttributeMap['attribute_variants'] as $id => $data) {
            $flatKey = self::flattenVariantData($sku, $data, $id);

            if ($flatKey) {
                $variantData[] = $flatKey;
            }
        }

        return $variantData;
    }

    /**
     * @param string $sku
     * @param array $data
     * @param string $prefix
     *
     * @return string|null
     */
    private static function flattenVariantData(string $sku, array $data, string $prefix): ?string
    {
        if (isset($data['id_product_concrete']) && (string)$data['id_product_concrete'] === $sku) {
            return $prefix;
        }

        foreach ($data as $key => $subData) {
            if (!is_array($subData)) {
                continue;
            }

            $result = self::flattenVariantData($sku, $subData, "{$prefix}|{$key}");

            if ($result) {
                return  $result;
            }
        }

        return null;
    }

    /**
     * @param string $key
     * @param array $values
     * @param array $variantData
     *
     * @return mixed|null
     */
    private static function resolveSuperAttributeValue(string $key, array $values, array $variantData)
    {
        foreach ($values as $value) {
            foreach ($variantData as $identifier) {
                if (strpos($identifier, "{$key}:{$value}") !== false) {
                    return $value;
                }
            }
        }

        return null;
    }
}
