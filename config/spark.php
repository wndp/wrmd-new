<?php

use App\Enums\Plan;
use App\Models\Team;

return [

    /*
    |--------------------------------------------------------------------------
    | Spark Path
    |--------------------------------------------------------------------------
    |
    | This configuration option determines the URI at which the Spark billing
    | portal is available. You are free to change this URI to a value that
    | you prefer. You shall link to this location from your application.
    |
    */

    'path' => 'billing',

    /*
    |--------------------------------------------------------------------------
    | Spark Middleware
    |--------------------------------------------------------------------------
    |
    | These are the middleware that requests to the Spark billing portal must
    | pass through before being accepted. Typically, the default list that
    | is defined below should be suitable for most Laravel applications.
    |
    */

    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    |
    | These configuration values allow you to customize the branding of the
    | billing portal, including the primary color and the logo that will
    | be displayed within the billing portal. This logo value must be
    | the absolute path to an SVG logo within the local filesystem.
    |
    */

    'brand' =>  [
        'logo' => realpath(__DIR__.'/../public/wrmd-logo.svg'),
        'color' => 'bg-green-600',
    ],

    /*
    |--------------------------------------------------------------------------
    | Proration Behavior
    |--------------------------------------------------------------------------
    |
    | This value determines if charges are prorated when making adjustments
    | to a plan such as incrementing or decrementing the quantity of the
    | plan. This also determines proration behavior if changing plans.
    |
    */

    'prorates' => true,

    /*
    |--------------------------------------------------------------------------
    | Spark Date Format
    |--------------------------------------------------------------------------
    |
    | This date format will be utilized by Spark to format dates in various
    | locations within the billing portal, such as while showing invoice
    | dates. You should customize the format based on your own locale.
    |
    */

    'date_format' => 'F j, Y', // 'M j, Y'

    /*
    |--------------------------------------------------------------------------
    | Spark Billables
    |--------------------------------------------------------------------------
    |
    | Below you may define billable entities supported by your Spark driven
    | application. The Paddle edition of Spark currently only supports a
    | single billable model entity (team, user, etc.) per application.
    |
    | In addition to defining your billable entity, you may also define its
    | plans and the plan's features, including a short description of it
    | as well as a "bullet point" listing of its distinctive features.
    |
    */

    'billables' => [

        'team' => [
            'model' => Team::class,

            'trial_days' => 60,

            'default_interval' => 'yearly',

            'plans' => [
                [
                    'name' => Plan::STANDARD->value,
                    'short_description' => 'The WRMD Standard plan is designed for all wildlife rehabilitators and will always be FREE. It covers all your essential needs along with numerous additional features.',
                    'yearly_id' => env('SPARK_STANDARD_YEARLY_PLAN', 'pri_01j8g823pk35whp8yd274a53wa'),
                    'features' => [
                        'Hotline',
                        'Daily Tasks (including rechecks, prescriptions, nutrition plans, ...)',
                        'Reports (including annual reports)',
                        'Patient Analytics',
                        '--File Uploads',
                        '--Patient Batch Updating',
                        '--Custom Classification Tags',
                        '--Custom Fields',
                        '--Sub Accounts',
                    ],
                    'archived' => false,
                ],
                [
                    'name' => Plan::PRO->value,
                    'short_description' => 'The WRMD Pro plan encompasses all Standard features along with a variety of advanced functionalities designed to enhance your record-keeping capabilities.',
                    'monthly_id' => env('SPARK_STANDARD_MONTHLY_PLAN', 'pri_01j8g8c590exjcx7n03gt1qrcc'),
                    'yearly_id' => env('SPARK_STANDARD_YEARLY_PLAN', 'pri_01j8g83vw50ff1n97avcmq5k2h'),
                    'features' => [
                        'All Standard Features',
                        'File Uploads',
                        'Patient Batch Updating',
                        'Custom Classification Tags',
                        'Custom Fields',
                        'Sub Accounts',
                    ],
                    'archived' => false,
                    'options' => [
                        'file_uploads' => true,
                        'custom_classification_tags' => true,
                        'custom_fields' => true,
                        'sub_accounts' => true,
                    ],
                ],
            ],

        ],

    ],

    'terms_url' => '/terms-and-conditions'
];
