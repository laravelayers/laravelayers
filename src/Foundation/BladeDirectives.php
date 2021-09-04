<?php namespace Laravelayers\Foundation;

use Illuminate\Support\Facades\Blade;

trait BladeDirectives
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     * @throws \Throwable
     */
    public function boot()
    {
        $this->registerBladeIcon();
    }

    /**
     * Register the "icon" directive on the Blade.
     *
     * @return void
     * @throws \Throwable
     */
    public function registerBladeIcon()
    {
        // Get the Html code of the icon from the template
        // We replace the variable with a string,
        // the string will be replaced by the icon class from the blade directive
        $view = view('foundation::layouts.icon')->with([
            'class' => '{{ $class }}',
            'attributes' => '{{ $attributes }}'
        ])->render();

        Blade::directive('icon', function ($expression) use($view) {
            $expression = explode(',' , $expression);

            $view = "str_replace('{{ \$class }}', $expression[0], '{$view}')";
            $view = "str_replace('{{ \$attributes }}', " . ($expression[1] ?? "''") . ", $view)";

            return "<?php echo $view; ?>";
        });
    }
}
