<?php

namespace Laravelayers\Navigation\Decorators;

use Laravelayers\Contracts\Navigation\MenuItem as MenuItemContract;
use Laravelayers\Foundation\Decorators\DataDecorator;

class MenuItemDecorator extends DataDecorator implements MenuItemContract
{
    use MenuItem;

    /**
     * Get the primary key for the data.
     *
     * @return string
     */
    public function getKeyName()
    {
        return static::class == self::class ? 'id' : parent::getKeyName();
    }

    /**
     * Get the menu item name.
     *
     * @return string
     */
    public function getMenuName()
    {

        return static::class == self::class ? $this->get('name') : parent::getMenuName();
    }

    /**
     * Get the menu item url.
     *
     * @return string
     */
    public function getMenuUrl()
    {
        return static::class == self::class ? $this->get('url') : parent::getMenuUrl();
    }

    /**
     * Get the value of the HTML attribute of the class of the menu item icon.
     *
     * @return string
     */
    public function getMenuIcon()
    {
        return static::class == self::class ? $this->get('icon') : '';
    }

    /**
     * Get the sort value of the menu item.
     *
     * @return int
     */
    public function getMenuSorting()
    {
        return static::class == self::class ? $this->get('sorting') : 0;
    }

    /**
     * Get the value of the HTML attribute of the class of the menu item.
     *
     * @return string
     */
    public function getMenuClass()
    {
        return static::class == self::class ? $this->get('class') : '';
    }

    /**
     * Get the parent menu item ID.
     *
     * @return int|string
     */
    public function getMenuParentId()
    {
        return static::class == self::class ? $this->get('parent_id', 0) : 0;
    }
}
