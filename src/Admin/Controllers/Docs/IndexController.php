<?php

namespace Laravelayers\Admin\Controllers\Docs;

use Laravelayers\Admin\Controllers\Controller as AdminController;

class IndexController extends AdminController
{
    /**
     * Initialize items for the admin menu bar.
     *
     * @return array
     */
    protected function initMenu()
    {
        if (class_exists($class = \App\Http\Controllers\Admin\Docs\IndexController::class)) {
            return resolve($class)->initMenu();
        }

        return class_exists($class = \Laravelayers\Docs\Controllers\IndexController::class)
            ? resolve($class)->initMenu()
            : [];
    }
}
