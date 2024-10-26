<?php

namespace DigitalCoreHub\LaravelModelViewCounter\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use DigitalCoreHub\LaravelModelViewCounter\Models\ModelView;

trait CountableView
{
    public function incrementViewCount()
    {
        if (config('model-view-counter.cache_enabled')) {
            $this->incrementViewCountWithCache();
        } else {
            $this->incrementViewCountDirectly();
        }
    }

    protected function incrementViewCountWithCache()
    {
        $cacheKey = config('model-view-counter.cache_key');
        $threshold = config('model-view-counter.cache_threshold');

        $modelKey = $this->getCacheModelKey();

        // Önceki sayımları al
        $counts = Cache::get($cacheKey, []);

        // Modelin sayımını artır
        if (isset($counts[$modelKey])) {
            $counts[$modelKey]++;
        } else {
            $counts[$modelKey] = 1;
        }

        // Güncellenmiş sayımları önbelleğe kaydet
        Cache::put($cacheKey, $counts);

        // Eğer eşik değeri aşıldıysa veritabanına kaydet
        if ($counts[$modelKey] >= $threshold) {
            $this->persistCountsToDatabase($modelKey, $counts[$modelKey]);

            // Sayımı sıfırla
            $counts[$modelKey] = 0;
            Cache::put($cacheKey, $counts);
        }
    }

    protected function incrementViewCountDirectly()
    {
        $this->persistCountsToDatabase($this->getCacheModelKey(), 1);
    }

    protected function persistCountsToDatabase($modelKey, $count)
    {
        [$modelType, $modelId] = explode(':', $modelKey);

        ModelView::updateOrCreate(
            [
                'model_type' => $modelType,
                'model_id' => $modelId,
            ],
            [
                'count' => DB::raw('count + ' . $count),
            ]
        );
    }

    protected function getCacheModelKey()
    {
        return get_class($this) . ':' . $this->getKey();
    }

    public function viewCount()
    {
        return $this->modelView ? $this->modelView->count : 0;
    }

    public function modelView()
    {
        return $this->morphOne(ModelView::class, 'modelable', 'model_type', 'model_id');
    }
}
