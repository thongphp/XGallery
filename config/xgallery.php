<?php

return [
    'adult' => [
        'cover' => env('ADULT_COVER', false),
        'download' => env('ADULT_DOWNLOAD', false)
    ],

    'flickr' => [
        'sync_google' => env('FLICKR_SYNC_GOOGLE', false)
    ]
];
