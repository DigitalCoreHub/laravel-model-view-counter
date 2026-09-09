<?php

namespace DigitalCoreHub\LaravelModelViewCounter\Support;

use DigitalCoreHub\LaravelModelViewCounter\Models\ModelView;

class ModelViewPersistor
{
    /**
     * Persist the given model view increment in the database.
     */
    public static function increment(string $modelType, int|string $modelId, int $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        $modelId = is_numeric($modelId) ? (int) $modelId : $modelId;

        $updatedRows = ModelView::query()
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->increment('count', $amount);

        if ($updatedRows === 0) {
            ModelView::query()->create([
                'model_type' => $modelType,
                'model_id' => $modelId,
                'count' => $amount,
            ]);
        }
    }
}
