<?php namespace Laravelayers\Foundation;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait CollectionMacros
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerCollectionToDto();
    }

    /**
     * Register the "ToDto" macro on the Collection to convert the DTO format.
     *
     * @see \Laravelayers\Foundation\Dto\Dto
     * @return void
     */
    public function registerCollectionToDto()
    {
        if (!Collection::hasMacro('toDto')) {
            Collection::macro('toDto', function () {
                $items = Collection::make($this);

                foreach ($items as $key => $value) {
                    if ($value instanceof Model) {
                        $items[$key] = $value->toDto();
                    }
                }

                return $items;
            });
        }
    }
}
