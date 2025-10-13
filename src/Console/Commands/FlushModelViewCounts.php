<?php

namespace DigitalCoreHub\LaravelModelViewCounter\Console\Commands;

use DigitalCoreHub\LaravelModelViewCounter\Support\ModelViewPersistor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FlushModelViewCounts extends Command
{
    protected $signature = 'model-view-counter:flush';

    protected $description = 'Flush cached model view counts to the database';

    public function handle(): int
    {
        $cacheKey = (string) config('model-view-counter.cache_key', 'model_view_counts');
        $counts = Cache::pull($cacheKey, []);

        foreach ($counts as $modelKey => $count) {
            if ($count <= 0) {
                continue;
            }

            [$modelType, $modelId] = explode(':', $modelKey, 2);

            ModelViewPersistor::increment($modelType, $modelId, (int) $count);
        }

        $this->info('Model view counts have been flushed to the database.');

        return self::SUCCESS;
    }
}
