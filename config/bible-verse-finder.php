<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the primary and fallback APIs for fetching Bible verses.
    | The addon will try APIs in order until a successful response is received.
    |
    */

    'apis' => [
        'primary' => 'bolls', // Primary API: bolls, bible-api, or scripture-api
        'timeout' => 10, // API request timeout in seconds

        // Bolls.life API (supports NKJV, KJV, WEB, etc.)
        'bolls' => [
            'base_url' => 'https://bolls.life',
            'enabled' => true,
            'versions' => [
                'NKJV' => 'NKJV',
                'KJV' => 'KJV',
                'WEB' => 'WEB',
            ],
        ],

        // Bible-API.com (supports KJV, WEB)
        'bible_api' => [
            'base_url' => 'https://bible-api.com',
            'enabled' => true,
            'versions' => [
                'KJV' => 'kjv',
                'WEB' => 'web',
            ],
        ],

        // API.Bible (requires API key, supports ESV, NIV, AMP, and 1000+ versions)
        'scripture_api' => [
            'base_url' => 'https://api.scripture.api.bible/v1',
            'api_key' => env('SCRIPTURE_API_KEY', ''),
            'enabled' => ! empty(env('SCRIPTURE_API_KEY')),
            'versions' => [
                'KJV' => 'de4e12af7f28f599-02', // King James Version
                'ESV' => '06125adad2d5898a-01', // English Standard Version
                'NIV' => '78a9f6124f344018-01', // New International Version (requires license)
                'NKJV' => '27979a461cf4e01b-01', // New King James Version
                // Add more version IDs as needed
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bible Versions
    |--------------------------------------------------------------------------
    |
    | Define the Bible versions available in the fieldtype.
    | These will appear in the dropdown in the Control Panel.
    |
    */

    'versions' => [
        'NKJV' => 'New King James Version',
        'KJV' => 'King James Version',
        'ESV' => 'English Standard Version',
        'NIV' => 'New International Version',
        'AMP' => 'Amplified Bible',
        'WEB' => 'World English Bible',
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configure where downloaded Bible JSON files are stored.
    | JSON files are optional and used for faster verse lookups.
    |
    */

    'storage' => [
        'path' => storage_path('app/bible-verses'),
        'use_local_json' => true, // Prefer local JSON over API calls
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Cache API responses to minimize external requests.
    | Cache key format: bible_verse_{version}_{book}_{chapter}_{verse}
    |
    */

    'cache' => [
        'enabled' => true,
        'ttl' => 86400 * 30, // 30 days (verses don't change)
        'driver' => env('CACHE_DRIVER', 'file'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Formatting Options
    |--------------------------------------------------------------------------
    |
    | Control how verse text is formatted when stored.
    |
    */

    'formatting' => [
        'include_verse_numbers' => true, // Include verse numbers in text
        'strip_html' => true, // Remove HTML tags from API responses
        'trim_whitespace' => true, // Normalize whitespace
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | Validation rules for verse references.
    |
    */

    'validation' => [
        'strict_mode' => true, // Validate chapter/verse existence before API call
        'allow_ranges' => true, // Allow verse ranges (e.g., 1-5)
        'max_verses_per_range' => env('BIBLE_MAX_VERSES', 20), // Maximum verses in a single range
    ],

    /*
    |--------------------------------------------------------------------------
    | Reference Parsing
    |--------------------------------------------------------------------------
    |
    | Configuration for parsing pasted Bible references.
    | Supports formats like: "John 3:16", "John 3:16-17", "John 3:16-17 NKJV"
    |
    */

    'parsing' => [
        'enabled' => true,
        'default_version' => 'NKJV', // Default if no version specified
        'patterns' => [
            // Pattern: Book Chapter:Verse-Verse Version
            '/^([\d\s\w]+)\s+(\d+):(\d+)(?:-(\d+))?\s*([A-Z]+)?$/i',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Download Sources
    |--------------------------------------------------------------------------
    |
    | URLs for downloading complete Bible versions as JSON.
    | These are optional performance optimizations.
    |
    */

    'download_sources' => [
        'NKJV' => null, // Add download URLs if available
        'KJV' => 'https://raw.githubusercontent.com/thiagobodruk/bible/master/json/en_kjv.json',
        'ESV' => null,
        'NIV' => null,
        'AMP' => null,
        'WEB' => null,
    ],
];
