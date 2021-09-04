<?php

namespace Laravelayers\Contracts\Admin\Decorators;

use Laravelayers\Foundation\Decorators\Decorator;

/**
 * @see \Laravelayers\Admin\Menu
 */
interface Menu
{
    /**
     * Get the admin menu.
     *
     * @return array|Decorator
     */
    public function getMenu();

    /**
     * Get the menu item for the admin menu bar.
     *
     * @return array
     */
    public function getMenuItem();

    /**
     * Prepare the menu item for the admin menu bar.
     *
     * @param $item
     * @return array
     */
    public function prepareMenuItem($item);

    /**
     * Get true if the back link is activated for the admin menu or false.
     *
     * @return bool
     */
    public static function getIsBackLinkForMenuPath();

    /**
     * Activate back link for admin menu.
     *
     * @param bool $value
     * @return void
     */
    public static function setIsBackLinkForMenuPath($value);
}
