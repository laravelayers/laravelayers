<?php

namespace Laravelayers\Admin;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Laravelayers\Foundation\Services\Service;
use Laravelayers\Navigation\Decorators\MenuItemDecorator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laravelayers\Foundation\Decorators\Decorator;
use Laravelayers\Navigation\Decorators\MenuDecorator;
use Laravelayers\Previous\PreviousUrl;

/**
 * The admin menu.
 *
 * @package Laravelayers\Admin
 */
trait Menu
{
    /**
     * Admin menu.
     *
     * @var array|Decorator
     */
    protected static $menu = [];

    /**
     * Value of activation of the back link.
     *
     * @var null|bool
     */
    protected static $isBackLinkForMenuPath = true;

    /**
     * Get the admin menu.
     *
     * @param string $key
     * @return array|Decorator
     */
    public function getMenu($key = '')
    {
        if (!static::$menu) {
            $items = $this->getMenuCache();

            if ($path = $this->getMenuPath()) {
                $items = $items->concat($path);

                $selected = $items->last();

                if ($items) {
                    $items->put($items->keys()->last(), array_merge($selected, ['hidden' => true]));
                }
            } else {
                $selected = $this->getMenuItem();

                if ($selected) {
                    $found = $items->where('id', $selected['id'])->all();

                    if (isset($selected['item'])) {
                        unset($selected['item']);
                    }

                    if (array_diff($selected, current($found) ?: [])) {
                        Artisan::call('admin:menu-cache');

                        if ($found) {
                            $items->put(key($found), $selected);
                        } else {
                            $items->push($selected);
                        }
                    }
                } else {
                    $selected['id'] = 0;
                }
            }

            $tree = MenuDecorator::make($items)->getMenu();

            $tree->map(function($item) {
                if ((!Auth::check() || !Auth::user()->can($item['route'])) && !isset($item['hidden'])) {
                    $item->put('hidden', true);
                    $item->put('url', '');
                }

                if (isset($item['label']) && Route::has($item['route'])) {
                    $controller = Route::getRoutes()->getByName($item['route'])->getController();

                    if ($controller->getMenuItem()) {
                        $item->setMenuLabel(
                            $controller->getMenuItem()['label'],
                            $controller->getMenuItem()['class'] ?? ''
                        );
                    }
                }

                return $item;
            });

            if ($selected['id']) {
                $tree = $tree->setSelectedItems($selected['id'], 'id');
            }

            $tree = $tree ->where('name', true);

            static::$menu = Decorator::make([
                'menu' => $tree->where('hidden', false)->getTree(),
                'path' => $path = $tree->getPath($selected['id']),
                'title' => $path->getTitle(Lang::get('admin::admin.menu.name')),
            ]);

            if ($selected['id']) {
                if (!$this->addActionToMenuPath()) {
                    $this->getSubmenu();
                }
            }
        }

        return static::$menu->get($key, static::$menu);
    }

    /**
     * Get the items for the admin menu bar.
     *
     * @return array
     */
    public function getMenuItem()
    {
        return $this->prepareMenuItem($this->initMenu());
    }

    /**
     * Initialize items for the admin menu bar.
     *
     * @return array
     */
    protected function initMenu()
    {
        return [];
    }

    /**
     * Get path items for the admin menu.
     *
     * @return array
     */
    protected function getMenuPath()
    {
        $items = $this->initMenuPath();

        if (isset($items['route'])) {
            $items = [$items];
        }

        $params = request()->route()->parameters();
        $_items = [];

        foreach($items as $key => $item) {
            $parent = $item['parent'];
            $parents = explode('.', $parent);

            if (array_pop($parents) == 'edit' && $params) {
                $param = Str::singular(str_replace(['{', '}'], '', end($parents)));

                array_push($_items, [
                    'route' => $item['parent'],
                    'name' => Lang::get('admin::admin.menu.id', ['id' => $params[$param] ?? end($params)]),
                    'parent' => implode('.', $parents) . '.index'
                ], $item);
            } elseif (empty($item['hidden'])) {
                $last = Arr::last($_items);

                if ($last['route'] == $item['parent']) {
                    array_push($_items, $item);
                }
            }
        }

        if ($_items) {
            $items = $_items;
        }

        foreach($items as $key => $item) {
            $item['url'] = $item['url']
                ?? strtok(route($item['route'], $params, false), '?');

            $items[$key] = $this->prepareMenuItem($item);
        }

        return $items;
    }

    /**
     * Initialize path items for the admin menu.
     *
     * @return array
     */
    protected function initMenuPath()
    {
        return [];
    }

    /**
     * Add action method to the admin path.
     *
     * @return bool|MenuDecorator
     */
    protected function addActionToMenuPath()
    {
        $action = request()->get('action', request()->route()->getActionMethod());

        if ($_action = request()->query('action', request()->get('_action'))) {
            $action = $_action . (Str::endsWith(strtolower($action), 'multiple') ? '_multiple' : '');
        }

        if (in_array($action, ['store', 'index'])) {
            $action = request()->get('action', request()->old('preaction', $action));
        }

        if ($action != 'index') {
            $path = $this->addItemToMenuPath(
                Lang::get('admin::admin.actions.' . Str::snake($action) . '')
            );

            $this->getMenu()->put('path', $path);
        } elseif (request()->has(Service::getSearchName())) {
            $path = $this->addItemToMenuPath(
                Lang::get('admin::admin.menu.search')
            );

            $this->getMenu()->put('path', $path);
        }

        if (!empty($path)) {
            $this->getMenu()->put('title', $path->getTitle(Lang::get('admin::admin.menu.name')));
        }

        if ($backLink = $this->getBackLinkForMenuPath()) {
            $this->getMenu()->put('path', $backLink);
        }

        return $path ?? false;
    }

    /**
     * Add item to the admin path.
     *
     * @param string|array $item
     * @return \Laravelayers\Navigation\Decorators\MenuDecorator
     */
    protected function addItemToMenuPath($item)
    {
        $path = static::$menu->get('path');

        if (is_string($item)) {
            $item = ['name' => $item];
        }

        $item = array_merge([
            'id' => $path->getOriginal()->count() + 1,
            'parent' => $path->last()->getNodeId(),
            'url' => url()->current(),
        ], $item);

        $item['parent_id'] = $item['parent'];

        if (PreviousUrl::getQuery()) {
            $query = PreviousUrl::getQuery();
        }

        if ($action = request()->query('action')) {
            $query['action'] = $action;
        }

        if (!empty($query)) {
            $item['url'] .= '?' . http_build_query($query);
        }

        $item = MenuItemDecorator::make($this->prepareMenuItem($item))
            ->setIsSelected(true)
            ->setNodeParentId($item['parent_id']);

        $path = $path->addNode($item, $item->getNodeParentId());

        if (!$item['parent_id'] && ($firstId = $path->first()->getNodeId())) {
            $path->where('id', $firstId)
                ->first()
                ->setIsSelected(false)
                ->setNodeParentId($item['id'])
                ->put('parent_id', $item['id']);
        }

        return $path->getPath($path->last()->getNodeId());
    }

    /**
     * Set the name for the specified path item for the admin menu.
     *
     * @param string|null $route
     * @param string $name
     * @return $this
     */
    public function setMenuPathName($name, $route = null)
    {
        $path = $this->getMenu('path');

        if (is_null($route)) {
            $path = $path->filter(function ($value, $key) {
                return $value['route'];
            });
        }

        $item = $route
            ? $path->firstWhere('route', $route)
            : $path->last();

        if ($item) {
            $item->put('name', $name);
        }

        return $item;
    }

    /**
     * Get back link for admin path.
     *
     * @return bool|MenuDecorator
     */
    protected function getBackLinkForMenuPath()
    {
        if (!static::getIsBackLinkForMenuPath() || !PreviousUrl::getUrl()) {
            return false;
        }

        $path = static::$menu->get('path');

        $item = MenuItemDecorator::make(
            $this->prepareMenuItem([
                'id' => $path->getOriginal()->count() + 1,
                'name' => Lang::get('previous::previous.back'),
                'url' => PreviousUrl::getUrl(),
                'icon' => 'icon-angle-left',
            ])
        );

        if ($path->isNotEmpty()) {
            $firstId = $path->first()->getNodeId();

            $items = $path->getOriginal()->map(function ($item) {
                return $item->put('url', '');
            });

            $items->where('id', $firstId)
                ->first()
                ->put('parent_id', $item->getNodeId());

            $items = $items->push($item)->reloadNodes($path);
        } else {
            $items = MenuDecorator::make(collect([$item]))->getPath($item->getNodeId());
        }

        return $items->isNotEmpty() ? $items : false;
    }

    /**
     * Get items for the admin submenu.
     *
     * @return \Laravelayers\Navigation\Decorators\MenuItemDecorator|array
     */
    protected function getSubmenu()
    {
        $items = $this->initSubmenu();

        if ($items) {
            $path = static::$menu->get('path');

            foreach ($items as $key => $item) {
                $item['parent'] = $path->last()->route;

                if ((!isset($item['name']) || !isset($item['icon'])) && isset($item['route'])) {
                    if ($first = $this->getMenuCache()->where('route', $item['route'])->first()) {
                        $item['name'] = $item['name'] ?? $first['name'];
                        $item['icon'] = $item['icon'] ?? $first['icon'];
                    }
                }

                $items[$key] = MenuItemDecorator::make($this->prepareMenuItem($item));
            }

            $path = $path->getOriginal()
                ->concat($items)
                ->reloadNodes($path);

            static::$menu->put('path', $path);
        }

        return $items;
    }

    /**
     * Initialize items for the admin submenu.
     *
     * @return array
     */
    protected function initSubmenu()
    {
        return [];
    }

    /**
     * Prepare the menu item for the admin menu bar.
     *
     * @param $item
     * @return array
     */
    public function prepareMenuItem($item)
    {
        if ($item) {
            $item = array_merge([
                'id' => $item['id'] ?? ($item['route'] ?? $item['url']),
                'route' => '',
                'name' => $item['name'],
                'url' => '',
                'parent_id' => 0,
                'parent' => '',
                'icon' => '',
                'sorting' => $item['name'],
                'hidden' => null,
            ], $item);

            $item['id'] = md5($item['id']);

            if (is_array($item['parent'])) {
                $item['item'] = $this->prepareMenuItem($item['parent']);
                $item['parent'] = $item['item']['route'] ?? $item['item']['id'];
            }

            if (!$item['parent_id']) {
                $item['parent_id'] = $item['parent'] ? md5($item['parent']) : 0;
            }

            $item['url'] = $item['url'] ?: route($item['route'], [], false);

            if (!preg_match('/\.index$/i', $item['route']) && is_null($item['hidden'])) {
                $item['hidden'] = true;
            }

            $item['hidden'] = (bool) $item['hidden'];
        }

        return $item;
    }

    /**
     * Get true if the back link is activated for the admin menu or false.
     *
     * @return bool
     */
    public static function getIsBackLinkForMenuPath()
    {
        return static::$isBackLinkForMenuPath;
    }

    /**
     * Activate back link for admin menu.
     *
     * @param bool $value
     * @return void
     */
    public static function setIsBackLinkForMenuPath($value)
    {
        static::$isBackLinkForMenuPath = (bool) $value;
    }

    /**
     * Get menu cache.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getMenuCache()
    {
        $path = base_path('bootstrap/cache/adminMenu.php');

        if (!File::exists($path)) {
            Artisan::call('admin:menu-cache');
        }

        return collect(require $path);
    }
}
