<?php

namespace DigitalCoreHub\LaravelModelViewCounter\Listeners;
use DigitalCoreHub\LaravelModelViewCounter\Events\ModelViewed;

class IncrementModelViewCount
{
    public function handle(ModelViewed $event)
    {
        $model = $event->model;

        if (in_array(get_class($model), config('model-view-counter.models'))) {
            $model->incrementViewCount();
        }
    }
}
