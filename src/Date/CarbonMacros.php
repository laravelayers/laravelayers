<?php namespace Laravelayers\Date;

use Illuminate\Support\Carbon;

trait CarbonMacros
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerCarbonDate();

        $this->registerCarbonTime();

        $this->registerCarbonDatetime();

        $this->registerCarbonDateLocalized();

        $this->registerCarbonCreationFromDefaultFormat();
    }

    /**
     * Register the "toConvertedDateString" macro on the Carbon to convert the date.
     *
     * @return void
     */
    public function registerCarbonDate()
    {
        Carbon::macro('toConvertedDateString', function () {
            return $this->format(config('date.format'));
        });
    }

    /**
     * Register the "toConvertedTimeString" macro on the Carbon to convert the date to the time format.
     *
     * @return void
     */
    public function registerCarbonTime()
    {
        Carbon::macro('toConvertedTimeString', function () {
            return $this->format(config('date.time.format'));
        });
    }

    /**
     * Register the "toConvertedDateTimeString" macro on the Carbon to convert the date to the date and time format.
     *
     * @return void
     */
    public function registerCarbonDatetime()
    {
        Carbon::macro('toConvertedDateTimeString', function () {
            return $this->format(config('date.datetime.format'));
        });
    }

    /**
     * Register the "dateLocalized" macro on the Carbon to convert the date to the format of a localized date.
     *
     * @return void
     */
    public function registerCarbonDateLocalized()
    {
        Carbon::macro('dateLocalized', function () {
            return $this->formatLocalized(config('date.formatLocalized'));
        });
    }

    /**
     * Register the "createFromDefaultFormat" macro on the Carbon to create a date from the default format.
     *
     * @return void
     */
    public function registerCarbonCreationFromDefaultFormat()
    {
        Carbon::macro('createFromDefaultFormat', function ($date) {
            return Carbon::createFromFormat(config('date.datetime.format'), $date);
        });
    }
}
