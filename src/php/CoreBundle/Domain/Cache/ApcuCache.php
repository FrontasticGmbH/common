<?php

namespace Frontastic\Common\CoreBundle\Domain\Cache;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;

/**
 * Drop-in replacement for Doctrine\Common\Cache\ApcuCache, which was removed
 * together with all other implementations in doctrine/cache 2.0.
 *
 * Kept as a CacheProvider subclass so the key namespacing (and therefore any
 * warm APCu entries) stays identical to the removed class. Unlike
 * Symfony\Component\Cache\Adapter\ApcuAdapter this must never throw during
 * construction: the API client factories receiving it are instantiated
 * eagerly, also in contexts without a usable APCu (CLI, tests).
 */
class ApcuCache extends CacheProvider
{
    protected function doFetch($id)
    {
        return apcu_fetch($id);
    }

    protected function doContains($id)
    {
        return apcu_exists($id);
    }

    protected function doSave($id, $data, $lifeTime = 0)
    {
        return apcu_store($id, $data, $lifeTime);
    }

    protected function doFetchMultiple(array $keys)
    {
        return apcu_fetch($keys) ?: [];
    }

    protected function doSaveMultiple(array $keysAndValues, $lifetime = 0)
    {
        $result = apcu_store($keysAndValues, null, $lifetime);

        return empty($result);
    }

    protected function doDelete($id)
    {
        // apcu_delete() returns false if the id does not exist
        return apcu_delete($id) || !apcu_exists($id);
    }

    protected function doDeleteMultiple(array $keys)
    {
        $result = apcu_delete($keys);

        return $result !== false && count($result) !== count($keys);
    }

    protected function doFlush()
    {
        return apcu_clear_cache();
    }

    protected function doGetStats()
    {
        $info = apcu_cache_info(true);
        $sma = apcu_sma_info();

        return [
            Cache::STATS_HITS => $info['num_hits'],
            Cache::STATS_MISSES => $info['num_misses'],
            Cache::STATS_UPTIME => $info['start_time'],
            Cache::STATS_MEMORY_USAGE => $info['mem_size'],
            Cache::STATS_MEMORY_AVAILABLE => $sma['avail_mem'],
        ];
    }
}
