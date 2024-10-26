<?php

return [
    'models' => [
        /*
            Example:
            App\Models\Post::class,
            App\Models\Blogs::class,
        */
    ],
    'cache_enabled' => true, // To enable caching feature
    'cache_threshold' => 10, // Minimum count to accumulate in cache
    'cache_key' => 'model_view_counts', // Cache key
];