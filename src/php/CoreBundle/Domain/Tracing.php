<?php

namespace Frontastic\Common\CoreBundle\Domain;

use Ramsey\Uuid\Uuid;

class Tracing
{
    public const CORRELATION_ID_HEADER_KEY = 'X-Correlation-ID';

    private static ?string $traceId = null;

    public static function getCurrentTraceId()
    {
        if (self::$traceId) {
            return self::$traceId;
        }

        // The webserver *might* default this to -, so we always assume that - is a missing id
        self::$traceId = $_SERVER['HTTP_X_CLOUD_TRACE_CONTEXT'] ?? '-';

        if (self::$traceId === '-' || self::$traceId === '') {
            $uuid = Uuid::uuid4()->toString();
            self::$traceId = str_replace('-', '', $uuid);
        }

        return self::$traceId;
    }
}
