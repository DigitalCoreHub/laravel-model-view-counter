<?php

return [
    /*
     * Register the models that should track view counts.
     */
    'models' => [
        /*
         * Example Models
         * App\Models\Post::class,
         * App\Models\Blog::class,
         * App\Models\User::class,
         */
    ],

    /*
     * Toggle caching for model view increments.
     */
    'cache_enabled' => false,

    /*
     * Minimum number of views that should be accumulated in cache
     * before persisting to the database.
     */
    'cache_threshold' => 10,

    /*
     * Cache key used to store the pending view counts.
     */
    'cache_key' => 'model_view_counts',

    /*
     * Optional time-to-live (in seconds) for cached view counts.
     * Set to null to store indefinitely.
     */
    'cache_ttl' => 86400,
];
