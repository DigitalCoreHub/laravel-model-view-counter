<?php

namespace DigitalCoreHub\LaravelModelViewCounter\Models;
use Illuminate\Database\Eloquent\Model;

class ModelView extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'count',
    ];

    public function modelable()
    {
        return $this->morphTo(null, 'model_type', 'model_id');
    }
}
