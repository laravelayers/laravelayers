<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Date Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The date locale defines the locale that will be used for the localized
    | date format in "Laravelayers\Date\DateServiceProvider::registerLocale()".
    | The value is specified as a string "en" or an array "['en','en_EN']".
    | If locale is NULL, the default locale value "app.locale" is used.
    | If no locale is specified (for example: empty string or false),
    | then the locale will not be used for the localized date format.
    |
    */

    'locale' => null,

    /*
    |--------------------------------------------------------------------------
    | Localized Date Format
    |--------------------------------------------------------------------------
    |
    | Used in the "dateLocalized" macro for the "Illuminate\Support\Carbon" class
    | in "Laravelayers\Date\CarbonMacros::registerCarbonDateLocalized()".
    |
    */

    'formatLocalized' => '%B %e, %Y',

    /*
    |--------------------------------------------------------------------------
    | Date And Time Format
    |--------------------------------------------------------------------------
    |
    | These values are used to display the date and time using the methods
    | set in the "Laravelayers\Date\CarbonMacros"
    | and "Laravelayers\Date\BladeDirectives" classes,
    | and in the date and time form element views.
    | The default value for the "datetime.format" configuration parameter
    | is "format time.format".
    |
    */

    'format' => 'Y-m-d',

    'time' => [
        'format' => 'H:i:s',
    ],

    'datetime' => [
        'format' => '',
    ],
];
