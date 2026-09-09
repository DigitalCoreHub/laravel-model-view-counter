<?php

namespace DigitalCoreHub\LaravelModelViewCounter\Traits;

use DigitalCoreHub\LaravelModelViewCounter\Models\ModelView;
use DigitalCoreHub\LaravelModelViewCounter\Support\ModelViewPersistor;
use DigitalCoreHub\LaravelModelViewCounter\Support\PendingViewCache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait CountableView
{
    /**
     * Increment the view counter for the model.
     */
    public function incrementViewCount(int $amount = 1): void
    {
        if ($amount <= 0 || $this->getKey() === null) {
            return;
        }

        if ($this->isCacheEnabled()) {
            $this->incrementViewCountWithCache($amount);

            return;
        }

        $this->incrementViewCountDirectly($amount);
    }

    /**
     * Increment the view counter using the configured cache backend.
     */
    protected function incrementViewCountWithCache(int $amount): void
    {
        try {
            PendingViewCache::withLock(function () use ($amount): void {
                $counts = PendingViewCache::getCounts();
                $modelKey = $this->getCacheModelKey();
                $threshold = (int) config('model-view-counter.cache_threshold', 0);
                $current = ($counts[$modelKey] ?? 0) + $amount;

                if ($threshold > 0 && $current >= $threshold) {
                    $this->persistCountsToDatabase($current);
                    $current = 0;
                }

                if ($current > 0) {
                    $counts[$modelKey] = $current;
                } else {
                    unset($counts[$modelKey]);
                }

                PendingViewCache::putCounts($counts);
            });
        } catch (LockTimeoutException $exception) {
            // Fall back to direct persistence when the cache lock cannot be acquired.
            $this->incrementViewCountDirectly($amount);
        }
    }

    /**
     * Increment the view counter directly in the database.
     */
    protected function incrementViewCountDirectly(int $amount): void
    {
        $this->persistCountsToDatabase($amount);
    }

    /**
     * Persist the cached view counts to the database.
     */
    protected function persistCountsToDatabase(int $amount): void
    {
        $modelKey = $this->getCacheModelKey();
        [$modelType, $modelId] = explode(':', $modelKey, 2);

        ModelViewPersistor::increment($modelType, $modelId, $amount);
    }

    /**
     * Retrieve the view count for the model including cached values.
     */
    public function viewCount(): int
    {
        $count = (int) optional($this->modelView)->count;

        if (! $this->isCacheEnabled()) {
            return $count;
        }

        $modelKey = $this->getCacheModelKey();
        $cachedCount = PendingViewCache::getCounts();

        return $count + (int) ($cachedCount[$modelKey] ?? 0);
    }

    /**
     * Model relation for the persisted view counter.
     */
    public function modelView(): MorphOne
    {
        return $this->morphOne(ModelView::class, 'modelable', 'model_type', 'model_id');
    }

    /**
     * Determine whether cache support is enabled.
     */
    protected function isCacheEnabled(): bool
    {
        return (bool) config('model-view-counter.cache_enabled', false);
    }

    /**
     * Resolve the cache key for the underlying model instance.
     */
    protected function getCacheModelKey(): string
    {
        return get_class($this) . ':' . $this->getKey();
    }

    /**
     * Resolve the cache storage key for all cached counters.
     */
    protected function cacheStorageKey(): string
    {
        return PendingViewCache::cacheKey();
    }
}
