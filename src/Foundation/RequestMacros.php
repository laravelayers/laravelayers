<?php namespace Laravelayers\Foundation;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Laravelayers\Form\Decorators\FormDecorator;

trait RequestMacros
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerGetByPrefixName();
    }

    /**
     * Register the "ToDto" macro on the Collection to convert the DTO format.
     *
     * @see \Laravelayers\Foundation\Dto\Dto
     * @return void
     */
    public function registerGetByPrefixName()
    {
        if (!Request::hasMacro('getFormElements')) {
            Request::macro('getFormElements', function () {
                $prefix = resolve(FormDecorator::class)->getElementsPrefixName();

                if ($prefix) {
                    $elements = $this->get($prefix, $this->old($prefix, []));
                } else {
                    $elements = $this->all() ?: $this->old();
                }

                return $elements;
            });
        }
    }
}
