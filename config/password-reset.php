<?php

use Laravel\Nova\Http\Middleware\Authenticate;
use Mastani\NovaPasswordReset\Http\Middleware\Authorize;

return [
    /*
    |--------------------------------------------------------------------------
    | Min Password Size
    |--------------------------------------------------------------------------
    |
    | Determine the default password size
    | Default is `5`.
    |
    */

    'min_password_size' => 5,

    'middleware' => [
        'nova',
        Authenticate::class,
        Authorize::class,
    ],
    'api_middleware' => [
        'nova',
        Authorize::class,
    ],

];
