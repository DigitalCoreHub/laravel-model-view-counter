<?php

namespace DigitalCoreHub\LaravelModelViewCounter\Traits;

use DigitalCoreHub\LaravelModelViewCounter\Models\ModelView;
use DigitalCoreHub\LaravelModelViewCounter\Support\ModelViewPersistor;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Cache;

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
        $cacheKey = $this->cacheStorageKey();
        $threshold = (int) config('model-view-counter.cache_threshold', 0);

        $counts = Cache::get($cacheKey, []);
        $modelKey = $this->getCacheModelKey();
        $counts[$modelKey] = ($counts[$modelKey] ?? 0) + $amount;

        if ($threshold > 0 && $counts[$modelKey] >= $threshold) {
            $this->persistCountsToDatabase($counts[$modelKey]);
            $counts[$modelKey] = 0;
        }

        Cache::put($cacheKey, $counts, $this->cacheTtlSeconds());
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

        $cachedCounts = Cache::get($this->cacheStorageKey(), []);
        $modelKey = $this->getCacheModelKey();

        return $count + (int) ($cachedCounts[$modelKey] ?? 0);
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
        return (string) config('model-view-counter.cache_key', 'model_view_counts');
    }

    /**
     * Determine the TTL for cached counters.
     */
    protected function cacheTtlSeconds(): ?int
    {
        $ttl = config('model-view-counter.cache_ttl');

        if ($ttl === null) {
            return null;
        }

        return (int) $ttl;
    }
}
