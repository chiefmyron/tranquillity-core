<?php

// Profiling engine settings

return [
    'enabled' => env('PROFILER_ENABLED', false),

    // Profiler storage mechanism
    'storage_type' => env('PROFILER_STORAGE_TYPE', 'file'),

    // Additional profiler parameters
    'storage_options' => [
        // Profiler options
        'path' => env('PROFILER_STORAGE_TYPE_FILE_PATH', '../var/profiler'),
        'index_filename' => env('PROFILER_STORAGE_TYPE_INDEX_FILENAME', 'index.csv')
    ]
];
