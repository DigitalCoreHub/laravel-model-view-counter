<?php

namespace DigitalCoreHub\LaravelModelViewCounter;

use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use DigitalCoreHub\LaravelModelViewCounter\Events\ModelViewed;
use DigitalCoreHub\LaravelModelViewCounter\Listeners\IncrementModelViewCount;
use DigitalCoreHub\LaravelModelViewCounter\Console\Commands\FlushModelViewCounts;

class LaravelModelViewCounter extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-model-view-counter')
            ->hasConfigFile()
            ->hasMigration('create_model_views_table')
            ->hasCommand(FlushModelViewCounts::class);
    }

    public function boot()
    {
        parent::boot();

        Event::listen(
            ModelViewed::class,
            IncrementModelViewCount::class
        );
    }
}
