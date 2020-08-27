<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PSS Settings
    |--------------------------------------------------------------------------
    */

    'modules' => [
        'http' => [
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
            'timeout' => 5,
            'verify-ssl' => false,
        ],
        'process' => [
            'timeout' => 1800, # 30 min
        ]
    ],

];
