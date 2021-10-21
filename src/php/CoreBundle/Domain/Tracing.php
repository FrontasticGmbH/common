<?php

namespace Frontastic\Common\CoreBundle\Domain;

use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;

class Tracing
{
    public const CORRELATION_ID_KEY = '__correlation_id';
    public const CORRELATION_ID_HEADER_KEY = 'X-Correlation-ID';

    public static function fetchTraceIdFromRequest(Request $request)
    {
        // The webserver *might* default this to -, so we always assume that - is a missing id
        $traceId = $request->headers->get('X-Cloud-Trace-Context', '-');

        if ($traceId === '-' || $traceId === '') {
            $uuid = Uuid::uuid4()->toString();
            $traceId = str_replace('-', '', $uuid);
        }

        $GLOBALS[self::CORRELATION_ID_KEY] = $traceId;

        return $traceId;
    }
}
