<?php

namespace Frontastic\Common\HttpClient\CircuitBreaker;

use Ackintosh\Ganesha\Storage\StorageKeys as StorageKeysBase;

class StorageKeys extends StorageKeysBase
{
    /**
     * @return string
     */
    public function prefix(): string
    {
        return sprintf(
            'circuit_breaker_%s_%s_%s_',
            getenv('customer'),
            getenv('project'),
            getenv('redis_prefix')
        );
    }
}
