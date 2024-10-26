<?php

namespace DigitalCoreHub\LaravelModelViewCounter\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use DigitalCoreHub\LaravelModelViewCounter\Models\ModelView;

class FlushModelViewCounts extends Command
{
    protected $signature = 'model-view-counter:flush';

    protected $description = 'Flush cached model view counts to the database';

    public function handle()
    {
        $cacheKey = config('model-view-counter.cache_key');
        $counts = Cache::pull($cacheKey, []);

        foreach ($counts as $modelKey => $count) {
            if ($count > 0) {
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
        }

        $this->info('Model view counts have been flushed to the database.');
    }
}