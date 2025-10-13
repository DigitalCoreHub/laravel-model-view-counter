<?php

namespace DigitalCoreHub\LaravelModelViewCounter\Listeners;

use DigitalCoreHub\LaravelModelViewCounter\Events\ModelViewed;

class IncrementModelViewCount
{
    public function handle(ModelViewed $event): void
    {
        $model = $event->model;
        $allowedModels = (array) config('model-view-counter.models', []);

        if (in_array(get_class($model), $allowedModels, true)) {
            $model->incrementViewCount();
        }
    }
}
