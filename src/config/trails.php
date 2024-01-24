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
    'cookie_name' => 'trails',

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
    'tracking_filter' => \MasudZaman\Trails\TrackingFilter::class,

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
    'tracking_logger' => \MasudZaman\Trails\TrackingLogger::class,

    /*
    |--------------------------------------------------------------------------
    | Trailer
    |--------------------------------------------------------------------------
    |
    | This class is responsible for generatin a digital trail (string) of
    | the request. The default implementation will check for the presence of
    | a trail cookie and return this. If no cookie is found then it returns
    | the requests trail - se Laravel Docs for this method.
    |
    */
    'trailer' => \MasudZaman\Trails\Trailer::class,

    /*
    |--------------------------------------------------------------------------
    | Trailing uniqueness
    |--------------------------------------------------------------------------
    |
    | If this setting is disabled then a semi-unique trail will be generated
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
    | Campaign tracking parameter
    |--------------------------------------------------------------------------
    |
    |
    */
    'campaign_prefix' => 'campaign_',
    'campaign_parameters' => [
        'name' => 'matomo_campaign,mtm_cpn,utm_campaign',
        'keyword' => 'mtm_keyword,matomo_kwd,mtm_kwd,utm_term',
        'source' => 'mtm_source,utm_source',
        'medium' => 'mtm_medium,utm_medium',
        'content' => 'mtm_content,utm_content',
        'id' => 'mtm_cid,utm_id,mtm_clid',
        'group' => 'mtm_group',
        'placement' => 'mtm_placement',
    ],

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
    'disable_on_authentication' => false,
    'disable_internal_links' => true, // * true: track only redirected with UTM parameter. * false: track all visits
    'disable_robots_tracking' => true,

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
