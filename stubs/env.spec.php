<?php

return [
    'common' => [
        'APP_ENV' => [
            'description' => 'Application environment',
            'default' => 'production',
            'rules' => [
                'required' => true,
                'enum' => [
                    'strict' => true,
                    'cases' => ['local', 'production'],
                ],
            ],
        ],
        'APP_DEBUG' => [
            'description' => 'Application debug mode.',
            'default' => 'true',
            'rules' => [
                'required' => true,
                'enum' => [
                    'strict' => true,
                    'cases' => ['true', 'false'],
                ],
            ],
        ],
        'LOG_CHANNEL' => [
            'description' => 'Log channel.',
            'default' => 'stack',
            'rules' => [
                'required' => true,
                'enum' => [
                    'strict' => true,
                    'cases' => ['stack', 'daily'],
                ],
            ],
        ],
    ],
    'local' => [

    ],
    'production' => [
        'APP_DEBUG' => [
            'rules' => [
                'equals' => 'false',
            ],
        ],
    ],
];
