<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Group Prefix
    |--------------------------------------------------------------------------
    |
    | Here you can specify the default prefix to be used to group widgets for
    | different purposes. By default, widgets are not grouped and so have no
    | prefix.
    |
    */
    'group' => null,

    /*
    |--------------------------------------------------------------------------
    | Inertia Retrieval
    |--------------------------------------------------------------------------
    |
    | Here you may configure how you would like Inertia.JS to retrieve dynamic 
    | data from the server, and pass it to your pages by default. The 
    |
    | Supported: "sync", "defer", "lazy"
    |
    */

    'inertia' => 'sync',
    
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
            'expiration' => null
        ],

        'cookie' => [
            'driver' => 'cookie',
            'expiration' => 60 * 24 * 365
        ],

        'database' => [
            'driver' => 'database',
            'connection' => null,
            'table' => 'widgets',
        ],

    ],

];