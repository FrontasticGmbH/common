<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Util;

class HandleParser
{
    private const HANDLE_SEPARATOR = '#';

    /**
     * Returns array were:
     *  - first element contains field name
     *  - second element contains definition
     *  - third element contains id
     *
     * @param string $handle
     *
     * @return string[]
     */
    public static function parseFacetHandle(string $handle): array
    {
        [$id, $name, $field, $definition] = array_pad(
            explode(self::HANDLE_SEPARATOR, $handle),
            4,
            null
        );

        if ($name === null) {
            $field = $id;
            $definition = $id;
            $id = null;
        } elseif ($field === null) {
            $field = $id;
            $definition = $name;
            $id = null;
        }

        return [$field, $definition, $id];
    }
}
