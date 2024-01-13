<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database connection name
    |--------------------------------------------------------------------------
    |
    | The name of the database connection if not the default
    |
    */
    'connection_name' => null,

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | The name of the database table that will hold UTM data
    |
    */
    'table_name' => 'visits',

    /*
    |--------------------------------------------------------------------------
    | Model
    |--------------------------------------------------------------------------
    |
    | The model to track attribution events for.
    |
    */
    'model' => App\Domains\V1\Auth\Models\User::class,
    // 'model' => App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Guard
    |--------------------------------------------------------------------------
    |
    | The authentication guard to use.
    |
    */
    'guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Relationship Column Name
    |--------------------------------------------------------------------------
    |
    | The column that defines the relation between tracked vists and the model.
    |
    */
    'column_name' => 'user_id',

    /*
    |--------------------------------------------------------------------------
    | Cookie Name
    |--------------------------------------------------------------------------
    |
    | The name of the cookie that is set to keep track of attributions.
    |
    */
    'cookie_name' => 'fingerprints',

    /*
    |--------------------------------------------------------------------------
    | Tracking Filter
    |--------------------------------------------------------------------------
    |
    | This class is responsible for determining if a request should be logged
    | or not. Setting this to another implementation makes it possible to
    | customize the logic. Note that the class must implement the
    | TrackingFilterInterface.
    |
    */
    'tracking_filter' => \MasudZaman\Fingerprints\TrackingFilter::class,

    /*
    |--------------------------------------------------------------------------
    | Tracking Logger
    |--------------------------------------------------------------------------
    |
    | This class is responsible for logging the request. Setting this to
    | another implementation makes it possible to customize the logic. Note
    | that the class must implement the TrackingLoggerInterface.
    |
    */
    'tracking_logger' => \MasudZaman\Fingerprints\TrackingLogger::class,

    /*
    |--------------------------------------------------------------------------
    | Fingerprinter
    |--------------------------------------------------------------------------
    |
    | This class is responsible for generatin a digital fingerprint (string) of
    | the request. The default implementation will check for the presence of
    | a fingerprint cookie and return this. If no cookie is found then it returns
    | the requests fingerprint - se Laravel Docs for this method.
    |
    */
    'fingerprinter' => \MasudZaman\Fingerprints\Fingerprinter::class,

    /*
    |--------------------------------------------------------------------------
    | Fingerprinting uniqueness
    |--------------------------------------------------------------------------
    |
    | If this setting is disabled then a semi-unique fingerprint will be generated
    | for the request. The purpose of this is to anable tracking accross,
    | browsers or where cookies might be blocked.
    |
    | Note that enabling this could cause request from different users using
    | the same ip to be matched.
    |
    */
    'uniqueness' => true,

    /*
    |--------------------------------------------------------------------------
    | Attribution Duration
    |--------------------------------------------------------------------------
    |
    | How long since the initial visit should an attribution last for.
    |
    */
    'attribution_duration' => 2628000,

    /*
    |--------------------------------------------------------------------------
    | Ip logging
    |--------------------------------------------------------------------------
    |
    | Determine if the users IP address should be loged or not.
    |
    */
    'attribution_ip' => false,

    /*
    |--------------------------------------------------------------------------
    | Custom tracking parameter
    |--------------------------------------------------------------------------
    |
    |
    */
    'custom_parameters' => [],

    /*
    |--------------------------------------------------------------------------
    | Tracking settings
    |--------------------------------------------------------------------------
    |
    | Robots tracking are for instance search engine. Since they will never
    | register, it might be interesting to not track them to save space.
    |
    */
    'disable_on_authentication' => true,
    'disable_internal_links' => true, // * true: track only redirected with UTM parameter. * false: track all visits
    'disable_robots_tracking' => false,

    /*
    |--------------------------------------------------------------------------
    | Disable Routes
    |--------------------------------------------------------------------------
    |
    |
    */
    'landing_page_blacklist' => [
        'genealabs/laravel-caffeine/drip',
        '_debugbar/assets/javascript',
        '_debugbar/assets/stylesheets',
        'admin',
        'log-viewer',
        'log-viewer/logs',
        'log-viewer/logs/2024-01-13',
        'log-viewer/logs/2024-01-13/info',
        'log-viewer/logs/' . date("Y-m-d") . '/info'
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookie domain
    |--------------------------------------------------------------------------
    |
    | If you want to use with more subdomain
    | you have to set this to .yourdomain.com
    |
    */
    'cookie_domain' => config('session.domain'),

    /*
    |--------------------------------------------------------------------------
    | Async
    |--------------------------------------------------------------------------
    |
    | This function will use the laravel queue.
    | Make sure your setup is correct.
    |
    */
    'async' => false,
];
