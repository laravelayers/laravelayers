<?php

namespace Laravelayers\Contracts\Navigation;

use Laravelayers\Contracts\Form\Decorators\FormElement;
use Laravelayers\Navigation\Decorators\MenuDecorator;

/**
 * @see \Laravelayers\Navigation\Decorators\MenuItemDecorator
 */
interface MenuItem extends FormElement
{
    /**
     * Get the menu item name.
     *
     * @return string
     */
    public function getMenuName();

    /**
     * Get the menu item url.
     *
     * @return string
     */
    public function getMenuUrl();

    /**
     * Get the value of the HTML attribute of the class of the menu item icon.
     *
     * @return string
     */
    public function getMenuIcon();

    /**
     * Get the menu item label.
     *
     * @return string
     */
    public function getMenuLabel();

    /**
     * Set the menu item label.
     *
     * @param string $value
     * @param string $class
     * @return $this
     */
    public function setMenuLabel($value, $class = '');

    /**
     * Get the sort value of the menu item.
     *
     * @return int
     */
    public function getMenuSorting();

    /**
     * Get the value of the HTML attribute of the class of the menu item.
     *
     * @return string
     */
    public function getMenuClass();

    /**
     * Get the parent menu item ID.
     *
     * @return int|string
     */
    public function getMenuParentId();

    /**
     * Get the tree node ID.
     *
     * @return int|string
     */
    public function getNodeId();

    /**
     * Set the tree node ID.
     *
     * @param int|string $value
     * @return $this
     */
    public function setNodeId($value);

    /**
     * Get the parent tree node ID.
     *
     * @return int|string
     */
    public function getNodeParentId();

    /**
     * Set the parent tree node ID.
     *
     * @param int|string $value
     * @return $this
     */
    public function setNodeParentId($value);

    /**
     * Get the sort value of the tree node.
     *
     * @return int|float
     */
    public function getNodeSorting();

    /**
     * Set the sort value of the tree node.
     *
     * @param int|float $value
     * @return $this
     */
    public function setNodeSorting($value);

    /**
     * Get the value of the nesting level for the tree node.
     *
     * @return int
     */
    public function getNodeLevel();

    /**
     * Set the value of the nesting level for the tree node.
     *
     * @param int $value
     * @return $this
     */
    public function setNodeLevel($value);

    /**
     * Get true if the tree node is selected or false.
     *
     * @return bool
     */
    public function getIsNodeSelected();

    /**
     * Set true if the tree node is selected or false.
     *
     * @param bool $value
     * @return $this
     */
    public function setIsNodeSelected($value);

    /**
     * Get the subtree of the menu item.
     *
     * @return MenuDecorator
     */
    public function getTree();

    /**
     * Set the subtree of the menu item.
     *
     * @param mixed $value
     * @return $this
     */
    public function setTree($value);

    /**
     * Get the siblings of the menu item.
     *
     * @return MenuDecorator
     */
    public function getSiblings();

    /**
     * Get a sequence of parent menu items.
     *
     * @param int|null $maxLevel
     * @return MenuDecorator
     */
    public function getPath($maxLevel = null);

    /**
     * Get the parent menu item.
     *
     * @return MenuDecorator
     */
    public function getParent();

    /**
     * Get the original collection of menu items.
     *
     * @return MenuDecorator
     */
    public function getOriginal();

    /**
     * Determine if there an original collection of menu items.
     *
     * @return bool
     */
    public function hasOriginal();

    /**
     * Set the original collection of tree items.
     *
     * @param MenuDecorator $original
     * @return mixed
     */
    public function setOriginal(MenuDecorator $original);

    /**
     * Get the tree method or specified.
     *
     * @param string $name
     * @return array
     */
    public function getTreeMethod($name = '');
}
