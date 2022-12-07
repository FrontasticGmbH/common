<?php

namespace Frontastic\Common\ShopifyBundle\Domain\Mapper;

class ShopifyIdMapper
{
    public static function mapDataToId(?string $id): ?string
    {
        return $id !== null ? base64_encode($id) : null;
    }

    public static function mapIdToData(?string $id): ?string
    {
        return $id !== null ? base64_decode($id) : null;
    }

    public static function mapIdsToData(array $ids): array
    {
        return array_map(fn($id) => self::mapIdToData($id), $ids);
    }
}
