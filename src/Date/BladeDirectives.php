<?php namespace Laravelayers\Date;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;

trait BladeDirectives
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerBladeDateNow();

        $this->registerBladeDate();

        $this->registerBladeTime();

        $this->registerBladeDatetime();

        $this->registerBladeDateLocalized();

        $this->registerBladeDateDiffForHumans();
    }

    /**
     * Register the "dateNow" directive on the Blade to get the current date.
     *
     * @return void
     */
    public function registerBladeDateNow()
    {
        Blade::directive('dateNow', function ($expression) {
            $expression = preg_replace('/(^"|^\'|"$|\'$)/', '', $expression);

            return "<?php echo " . Carbon::class . "::now()" . ($expression ? "->$expression" : '' ) . "; ?>";
        });
    }

    /**
     * Register the the "date" directive on the Blade to convert the date.
     *
     * @return void
     */
    public function registerBladeDate()
    {
        Blade::directive('date', function ($expression) {
            return "<?php echo " . Carbon::class . "::parse($expression)->toConvertedDateString(); ?>";
        });
    }

    /**
     * Register the "time" directive on the Blade to convert the date to the time format.
     *
     * @return void
     */
    public function registerBladeTime()
    {
        Blade::directive('time', function ($expression) {
            return "<?php echo " . Carbon::class . "::parse($expression)->toConvertedTimeString(); ?>";
        });
    }

    /**
     * Register the "datetime" directive on the Blade to convert the date to the date and time format.
     *
     * @return void
     */
    public function registerBladeDatetime()
    {
        Blade::directive('datetime', function ($expression) {
            return "<?php echo " . Carbon::class . "::parse($expression)->toConvertedDateTimeString(); ?>";
        });
    }

    /**
     * Register the "dateLocalized" directive on the Blade to convert the date to the format of a localized date.
     *
     * @return void
     */
    public function registerBladeDateLocalized()
    {
        Blade::directive('dateLocalized', function ($expression) {
            return "<?php echo " . Carbon::class . "::parse($expression)->dateLocalized(); ?>";
        });
    }

    /**
     * Register the "dateDiffForHumans" directive on the Blade to convert the date
     * to difference in a human readable format in the current locale.
     *
     * @return void
     */
    public function registerBladeDateDiffForHumans()
    {
        Blade::directive('dateDiffForHumans', function ($expression) {
            return "<?php echo " . Carbon::class . "::parse($expression)->diffForHumans(); ?>";
        });
    }
}
