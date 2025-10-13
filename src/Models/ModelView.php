<?php

namespace DigitalCoreHub\LaravelModelViewCounter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ModelView extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'count',
    ];

    protected $casts = [
        'model_id' => 'int',
        'count' => 'int',
    ];

    public function modelable(): MorphTo
    {
        return $this->morphTo(null, 'model_type', 'model_id');
    }
}
