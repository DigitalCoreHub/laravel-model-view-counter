<?php

namespace DigitalCoreHub\LaravelModelViewCounter\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class ModelViewed
{
    use SerializesModels;

    public function __construct(public Model $model)
    {
    }
}
