<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Model
    |--------------------------------------------------------------------------
    |
    | Here you will specify the model class that should be used when utilising
    | the `HasWidgets` trait on your models. It is recommended that you extend
    | the `Honed\Widget\Models\Widget` model if you intend on overriding the
    | default behaviour.
    |
    | This will only be used when using the `database` driver.
    |
    */

    'model' => Honed\Widget\Models\Widget::class,

    /*
    |--------------------------------------------------------------------------
    | Default Widget Driver
    |--------------------------------------------------------------------------
    |
    | Here you will specify the default driver that Widget should use when
    | storing and resolving widget values. Widget ships with the
    | ability to store widget values in an in-memory array or database.
    |
    | Supported: "array", "cache", "cookie", "database"
    |
    */

    'default' => env('WIDGET_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Widget Drivers
    |--------------------------------------------------------------------------
    |
    | Here you may configure each of the drivers that should be available to
    | Widget. These drivers shall be used to store resolved widget values -
    | you may configure as many as your application requires.
    |
    */

    'drivers' => [
        'array' => [
            'driver' => 'array',
        ],
        'cache' => [
            'driver' => 'cache',
            'expiration' => null,
        ],
        'cookie' => [
            'driver' => 'cookie',
            'expiration' => 60 * 24 * 365,
        ],
        'database' => [
            'driver' => 'database',
            'connection' => null,
            'table' => 'widgets',
        ],
    ],
];
