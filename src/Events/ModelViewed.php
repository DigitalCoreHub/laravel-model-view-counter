<?php

namespace DigitalCoreHub\LaravelModelViewCounter\Events;
use Illuminate\Queue\SerializesModels;

class ModelViewed
{
    use SerializesModels;
    public $model;

    public function __construct($model)
    {
        $this->model = $model;
    }
}
