<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sitemap Cache Duration
    |--------------------------------------------------------------------------
    |
    | The duration in seconds that sitemap data should be cached.
    | Default is 3600 seconds (1 hour).
    |
    */

    'cache_duration' => env('SITEMAP_CACHE_DURATION', 3600),

    /*
    |--------------------------------------------------------------------------
    | Sitemap URL Limit
    |--------------------------------------------------------------------------
    |
    | Maximum number of URLs per sitemap file.
    | If a sitemap exceeds this limit, it should be split into multiple files.
    | Default is 50000 (Google's recommended limit).
    |
    */

    'url_limit' => env('SITEMAP_URL_LIMIT', 50000),

    /*
    |--------------------------------------------------------------------------
    | Sitemap Priority Settings
    |--------------------------------------------------------------------------
    |
    | Default priority values for different content types.
    | Values should be between 0.0 and 1.0
    |
    */

    'priorities' => [
        'home' => '1.0',
        'listing' => '0.9',
        'content' => '0.8',
        'cast' => '0.6',
        'episode' => '0.7',
        'static' => '0.5',
    ],

    /*
    |--------------------------------------------------------------------------
    | Change Frequency Settings
    |--------------------------------------------------------------------------
    |
    | Default change frequency values for different content types.
    | Valid values: always, hourly, daily, weekly, monthly, yearly, never
    |
    */

    'changefreq' => [
        'home' => 'daily',
        'listing' => 'daily',
        'content' => 'weekly',
        'cast' => 'monthly',
        'episode' => 'weekly',
        'static' => 'monthly',
    ],
];

