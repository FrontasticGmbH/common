<?php

namespace Frontastic\Common\CoreBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\PaginatedQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;

class PaginationAdapter
{
    const OFFSET_TAG = 'offset:';

    public static function queryCursorToOffset(PaginatedQuery $query): PaginatedQuery
    {
        if (substr($query->cursor, 0, strlen(self::OFFSET_TAG)) === self::OFFSET_TAG) {
            $query->offset = (int) substr($query->cursor, strlen(self::OFFSET_TAG));
        }

        return $query;
    }

    public static function resultOffsetToCursor(Result $result): Result
    {
        if ($result->nextCursor === null && $result->count > 0 && ($result->offset + $result->count) < $result->total) {
            $result->nextCursor = self::OFFSET_TAG . (string) ($result->offset + $result->count);
        }

        if ($result->previousCursor === null && $result->count > 0 && ($result->offset - $result->count) >= 0) {
            $result->previousCursor = self::OFFSET_TAG . (string) ($result->offset - $result->count);
        }

        return $result;
    }
}
