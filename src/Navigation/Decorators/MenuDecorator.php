<?php

namespace Laravelayers\Navigation\Decorators;

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Laravelayers\Contracts\Navigation\Tree as TreeContract;
use Laravelayers\Contracts\Navigation\MenuItem as MenuItemContract;
use Laravelayers\Foundation\Decorators\CollectionDecorator;

/**
 * The decorator for getting a tree from a collection of elements that have a parent identifier.
 *
 * The parent ID can be the name of the parent.
 *
 * @package Laravelayers\Menu\Decorators
 */
class MenuDecorator extends CollectionDecorator
{
    /**
     * Original collection of tree items.
     *
     * @var MenuDecorator|null
     */
    protected $original;

    /**
     * The method of the tree.
     *
     * @var array
     */
    protected $treeMethod = [];

    /**
     * Get menu items.
     *
     * @return MenuDecorator|mixed
     */
    public function getMenu()
    {
        return MenuItemDecorator::make($this);
    }

    /**
     * Get the tree of items for the specified node ID and the specified max level.
     *
     * @param mixed $id
     * @param null|int $maxLevel
     * @return MenuDecorator
     */
    public function getTree($id = 0, $maxLevel = null)
    {
        return $this->decorateTree(
            resolve(TreeContract::class)->getTree($this->getOriginal(), $id, $maxLevel)
        )->setTreeMethod(__METHOD__, func_get_args());
    }

    /**
     * Get the siblings for the specified node ID.
     *
     * @param mixed $id
     * @return MenuDecorator
     */
    public function getSiblings($id)
    {
        return $this->getTree($id, 0)->setTreeMethod(__METHOD__, func_get_args());
    }

    /**
     * Get the sequence of the parent nodes for the specified node ID and the specified max level.
     *
     * @param mixed $id
     * @param null|int $maxLevel
     * @return MenuDecorator
     */
    public function getPath($id, $maxLevel = null)
    {
        return $this->decorateTree(
            resolve(TreeContract::class)->getPath($this->getOriginal(), $id, $maxLevel)
        )->setTreeMethod(__METHOD__, func_get_args());
    }

    /**
     * Get the parent node for the specified node ID.
     *
     * @param mixed $id
     * @return MenuDecorator
     */
    public function getParent($id)
    {
        return $this->getPath($id, 1)
            ->forget(1)
            ->setTreeMethod(__METHOD__, func_get_args());
    }

    /**
     * Get the node for the specified node ID and siblings for the specified max level.
     *
     * @param mixed $id
     * @param null|int $maxLevel
     * @return MenuDecorator
     */
    public function getNode($id, $maxLevel = null)
    {
        return $this->decorateTree(
            resolve(TreeContract::class)->getNode($this->getOriginal(), $id, $maxLevel)
        )->setTreeMethod(__METHOD__, func_get_args());
    }

    /**
     * Get HTML title.
     *
     * @param mixed $id
     * @param null|int $maxLevel
     * @param string $postfix
     * @param string $separator
     * @return string
     */
    public function getTitle($id = null, $maxLevel = null, $postfix = '', $separator = ' / ')
    {
        if (in_array('getPath', $this->getOriginal()->getTreeMethod())) {
            $path = $this;

            $postfix = $id;

            if ($maxLevel) {
                $separator = $maxLevel;
            }
        } else {
            $path = $this->getPath($id, $maxLevel);
        }

        $title = '';

        if ($path->isNotEmpty()) {
            $path = $path->sortKeysDesc();

            foreach($path as $item) {
                $title = ($title ? "{$title}{$separator}" : '') . $item->getMenuName();
            }
        }

        if ($postfix) {
            $title .= ($title ? $separator : '') . $postfix;
        }

        return $title;
    }

    /**
     * Decorate the tree.
     *
     * @param array|\Traversable $data
     * @return $this|array|static
     */
    protected function decorateTree($data)
    {
        return static::make($data)->setOriginal($this->getOriginal());
    }

    /**
     * Get the original collection of items.
     *
     * @return MenuDecorator
     */
    public function getOriginal()
    {
        return $this->original ?: $this;
    }

    /**
     * Determine if there an original collection of items.
     *
     * @return bool
     */
    public function hasOriginal()
    {
        return isset($this->original);
    }

    /**
     * Set the original collection of items.
     *
     * @param MenuDecorator $original
     * @return mixed
     */
    public function setOriginal(MenuDecorator $original)
    {
        $this->original = clone $original;

        foreach ($this as $key => $item) {
            $item->setOriginal($this->getOriginal());
        }

        return $this;
    }

    /**
     * Add nodes for the specified parent node ID and before the specified node key.
     *
     * @param array|\Traversable $items
     * @param int $parentId
     * @param null|string|int $key
     * @return MenuDecorator
     */
    public function addNodes($items, $parentId = 0, $key = null)
    {
        foreach($items as $itemKey => $item) {
            $this->addNode($item, $parentId, $key);

            $last = $this->getOriginal()->last();

            $sort = empty($sort) ? $last->getNodeSorting() : $sort + 0.001;

            $last->setNodeSorting($sort);
        }

        return $this->hasOriginal() ? $this->reloadNodes() : $this->getOriginal();
    }

    /**
     * Add node for the specified parent node ID and before the specified node key.
     *
     * @param MenuItemContract $item
     * @param int $parentId
     * @param null|string|int $key
     * @return MenuDecorator
     */
    public function addNode(MenuItemContract $item, $parentId = 0, $key = null)
    {
        $sublevel = $this->getSiblings($parentId)->getOriginal();

        if ($sublevel->isNotEmpty()) {
            if ($sublevel->has($key)) {
                $current = $sublevel->get($key)->getNodeSorting();

                $sort = $current >= 1 ? $current - 1 : 0;
            } else {
                if ($last = $sublevel->last()) {
                    $sort = $last->getNodeSorting();
                }
            }
        }

        $item->setNodeParentId($parentId);

        if (!is_null($key) || !$item->getNodeSorting()) {
            $item->setNodeSorting(($sort ?? 0) + 0.001);
        }

        $this->getOriginal()->push($item);

        return $this->getOriginal();
    }

    /**
     * Reload the nodes of the tree.
     *
     * @param self|null $tree
     * @return $this
     */
    public function reloadNodes(MenuDecorator $tree = null)
    {
        $treeMethod = $tree ? $tree->getTreeMethod() : $this->getTreeMethod();

        return $this->{$treeMethod[0]}(...$treeMethod[1]);
    }

    /**
     * Get the tree method or specified.
     *
     * @param string $name
     * @return array
     */
    public function getTreeMethod($name = '')
    {
        if ($name) {
            $name = Str::camel('get_' . Str::after($name, 'get'));

            if (current($this->getTreeMethod()) != $name) {
                return [];
            }
        }

        return $this->getOriginal()->treeMethod;
    }

    /**
     * Set the method of the tree.
     *
     * @param string $method
     * @param array $args
     * @return $this
     */
    public function setTreeMethod($method, $args = [])
    {
        if ($method) {
            if (is_array($method)) {
                $args = $method[1];
                $method = $method[0];
            }

            $method = explode('::', $method, 2);

            $method = [
                !empty($method[1]) ? $method[1] : $method[0],
                $args,
            ];
        }

        $this->getOriginal()->treeMethod = $method;

        return $this;
    }

    /**
     * Render the tree.
     *
     * @param string $view
     * @return \Illuminate\Support\HtmlString
     * @throws \Throwable
     */
    public function render($view = '')
    {
        if (method_exists($this->getDataKey(), 'render')) {
            return $this->getDataKey()->render();
        }

        if ($this->isEmpty()) {
            return new HtmlString('');
        }

        if (!$view) {
            $view = '.menu';

            if ($this->getTreeMethod('path')) {
                $view = 'breadcrumbs.nav.menu';
            }
        }

        $view = ltrim(preg_replace('/(^|\.)menu.menu/i', '.menu', $view . '.menu'), '.');

        if (!Str::startsWith($view,'navigation::layouts.')) {
            if (View::exists("layouts.menu.{$view}")) {
                $view = "layouts.menu.{$view}";
            } elseif (View::exists("navigation::layouts.{$view}")) {
                $view = 'navigation::layouts.' . $view;
            }
        }

        return new HtmlString(
            view($view, ['tree' => $this])->render()
        );
    }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     * @throws \Throwable
     */
    public function __toString()
    {
        return $this->render()->toHtml();
    }
}
