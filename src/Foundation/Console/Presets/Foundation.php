<?php

namespace Laravelayers\Foundation\Console\Presets;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Laravel\Ui\Presets\Preset;

class Foundation extends Preset
{
    /**
     * Install the preset.
     *
     * @return void
     */
    public static function install()
    {
        static::updatePackages();
        static::updateAssets();
        static::removeNodeModules();
        static::updateWebpackConfiguration();
    }

    /**
     * Update the preset assets.
     *
     * @return void
     */
    public static function updateAssets()
    {
        static::updateSass();
        static::updateJs();
        static::updateTranslations();

        Artisan::call('vendor:publish', ['--tag' => 'laravelayers-foundation']);
        Artisan::call('vendor:publish', ['--tag' => 'laravelayers-admin']);
    }

    /**
     * Update the given package array.
     *
     * @param  array  $packages
     * @return array
     */
    protected static function updatePackageArray(array $packages)
    {
        return [
                "@fortawesome/fontawesome-free" => "^5.8.2",
                'cropperjs' => '^1.5.6',
                'flatpickr' => '^4.5.7',
                'foundation-sites' => '^6.5.3',
                'jquery' => '^3.4.1',
                'jquery-ui' => '^1.12.1',
                'libphonenumber-js' => '^1.7.18',
                'quill' => '^1.3.7',
                'simplemde' => '^1.11.2',
                'validator' => '^13.6.0'
            ] + $packages;
    }

    /**
     * Update the Sass files for the application.
     *
     * @return void
     */
    protected static function updateSass()
    {
        $from = __DIR__.'/foundation-stubs/resources/sass';
        $to = resource_path('sass');

        static::updateFiles([
            'app.scss',
            'app.admin.scss',
        ], $from, $to);
        
        $default_from = "{$from}/default";
        $default_to = "{$to}/default";

        if (!File::exists($default_to)) {
            File::copyDirectory($default_from, $default_to);
        } else {
            if (!File::exists("{$default_to}/_default.scss")) {
                copy("{$default_from}/_default.scss", "{$default_to}/_default.scss");

                if (!File::exists("{$default_to}/layouts/_layouts.scss")) {
                    copy("{$default_from}/layouts/_layouts.scss", "{$default_to}/layouts/_layouts.scss");
                }

                if (!File::exists("{$default_to}/layouts/_header.scss")) {
                    copy("{$default_from}/layouts/_header.scss", "{$default_to}/layouts/_header.scss");
                }
            }

            if (!File::exists("{$default_to}/settings/_settings.scss")) {
                copy("{$from}/default/settings/_settings.scss", "{$default_to}/settings/_settings.scss");
            }
        }
    }

    /**
     * Update the Javascript files for the application.
     *
     * @return void
     */
    protected static function updateJs()
    {
        static::updateFiles([
            'app.js',
            'app.admin.js',
            'bootstrap.js',
        ], __DIR__.'/foundation-stubs/resources/js', resource_path('js'));
    }

    /**
     * Update the translations files for the application.
     *
     * @return void
     */
    protected static function updateTranslations()
    {
        if (!file_exists(resource_path('lang/ru'))) {
            File::makeDirectory(resource_path('lang/ru'), 0755, true);
        }

        static::updateFiles([
            'en/validation.php',
            'ru/auth.php',
            'ru/pagination.php',
            'ru/passwords.php',
            'ru/validation.php'
        ], __DIR__.'/foundation-stubs/resources/lang', resource_path('lang'));
    }

    /**
     * Update the Webpack configuration.
     *
     * @return void
     */
    public static function updateWebpackConfiguration()
    {
        static::updateFiles([
            'webpack.mix.js'
        ], __DIR__.'/foundation-stubs', base_path());
    }

    /**
     * Update specified files.
     *
     * @param array $files
     * @param string $from
     * @param string $to
     * @return void
     */
    protected static function updateFiles($files, $from, $to)
    {
        if (!file_exists($to)) {
            File::makeDirectory($to, 0755, true);
        }
        
        foreach($files as $file) {
            if (File::exists("{$to}/{$file}")) {
                copy("{$to}/{$file}", "{$to}/{$file}.bak");
            }

            copy("{$from}/{$file}", "{$to}/{$file}");
        }
    }
}
