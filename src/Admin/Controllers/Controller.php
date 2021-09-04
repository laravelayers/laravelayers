<?php

namespace Laravelayers\Admin\Controllers;

use Laravelayers\Admin\Menu;
use Laravelayers\Contracts\Admin\Decorators\Menu as MenuContract;
use Laravelayers\Foundation\Controllers\Controller as BaseController;

/**
 * Base class for admin controllers.
 */
class Controller extends BaseController implements MenuContract
{
    use Menu;

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        return array_merge(
            parent::resourceAbilityMap(),
            ['editMultiple' => 'update'],
            ['updateMultiple' => 'update'],
            ['destroyMultiple' => 'delete']
        );
    }
}
