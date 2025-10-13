<?php

namespace DigitalCoreHub\LaravelModelViewCounter\Console\Commands;

use DigitalCoreHub\LaravelModelViewCounter\Support\ModelViewPersistor;
use DigitalCoreHub\LaravelModelViewCounter\Support\PendingViewCache;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\LockTimeoutException;

class FlushModelViewCounts extends Command
{
    protected $signature = 'model-view-counter:flush';

    protected $description = 'Flush cached model view counts to the database';

    public function handle(): int
    {
        $counts = [];

        try {
            PendingViewCache::withLock(function () use (&$counts): void {
                $counts = PendingViewCache::pullCounts();
            });
        } catch (LockTimeoutException $exception) {
            $this->warn('Unable to acquire cache lock. Flushing pending counts without locking.');
            $counts = PendingViewCache::pullCounts();
        }

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
