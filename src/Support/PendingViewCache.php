<?php

namespace DigitalCoreHub\LaravelModelViewCounter\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

class PendingViewCache
{
    /**
     * Execute the given callback while holding the cache lock.
     */
    public static function withLock(Closure $callback): void
    {
        $lock = Cache::lock(static::lockName(), static::lockSeconds());

        $lock->block(static::lockSeconds(), $callback);
    }

    /**
     * Retrieve all pending counts from cache.
     */
    public static function getCounts(): array
    {
        $counts = Cache::get(static::cacheKey(), []);

        return is_array($counts) ? $counts : [];
    }

    /**
     * Persist the given counts into cache respecting the configured TTL.
     */
    public static function putCounts(array $counts): void
    {
        $ttl = static::ttl();

        if ($ttl === null) {
            Cache::forever(static::cacheKey(), $counts);

            return;
        }

        Cache::put(static::cacheKey(), $counts, $ttl);
    }

    /**
     * Pull (retrieve and remove) all pending counts from cache.
     */
    public static function pullCounts(): array
    {
        $counts = Cache::pull(static::cacheKey(), []);

        return is_array($counts) ? $counts : [];
    }

    /**
     * Resolve the cache key for storing pending counts.
     */
    public static function cacheKey(): string
    {
        return (string) config('model-view-counter.cache_key', 'model_view_counts');
    }

    /**
     * Determine the TTL (in seconds) for cached counters.
     */
    public static function ttl(): ?int
    {
        $ttl = config('model-view-counter.cache_ttl');

        if ($ttl === null) {
            return null;
        }

        return (int) $ttl;
    }

    /**
     * Resolve the cache lock name.
     */
    public static function lockName(): string
    {
        return static::cacheKey() . ':lock';
    }

    /**
     * Determine the number of seconds the cache lock should be held.
     */
    public static function lockSeconds(): int
    {
        $seconds = (int) config('model-view-counter.cache_lock_seconds', 5);

        return max($seconds, 1);
    }
}
