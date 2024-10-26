<?php

return [
    'models' => [
        /*
        *   Example Models
        *   App\Models\Post::class,
        *   App\Models\Blogs::class,
        *   App\Models\User::class,
        */
    ],
    'cache_enabled' => false, // To enable caching feature
    'cache_threshold' => 10, // Minimum count to accumulate in cache
    'cache_key' => 'model_view_counts', // Cache key
];