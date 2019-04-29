<?php
namespace Frontastic\Common;

/**
 * ALERT: Class as a namespace for utility functions ;->
 */
final class Functions
{
    public static function array_merge_recursive(array $array1, array $array2): array
    {
        $merged = $array1;
        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::array_merge_recursive($merged[$key], $value);
            } elseif (is_int($key)) {
                $merged[] = $value;
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
}
