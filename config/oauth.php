<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OAuth2 client app credentials
    |--------------------------------------------------------------------------
    */
    'client' => [
        'id' => env('OAUTH_CLIENT_ID'),
        'secret' => env('OAUTH_CLIENT_SECRET'),
        'scopes' => [
            'authenticate-user',
            'confirm-email',
            'reset-password',
            'validate-email',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | OAuth2 server base URI
    |--------------------------------------------------------------------------
    */
    'base_uri' => env('OAUTH_BASE_URI'),

    /*
    |--------------------------------------------------------------------------
    | OAuth2 server token URI
    |--------------------------------------------------------------------------
    */
    'token_uri' => env('OAUTH_TOKEN_URI'),

];
