<?php

namespace Laravelayers\Navigation\Decorators;

use Laravelayers\Contracts\Navigation\MenuItem as MenuItemContract;
use Laravelayers\Form\Decorators\FormElement;
use Laravelayers\Foundation\Decorators\CollectionDecorator;

trait MenuItem
{
    use FormElement;

    /**
     * Original collection of tree items.
     *
     * @var MenuDecorator|null
     */
    protected $original;

    /**
     * Menu item data.
     *
     * @var array
     */
    protected $menu = [];

    /**
     * Decorate the collection.
     *
     * @param CollectionDecorator $data
     * @return MenuDecorator|CollectionDecorator
     */
    protected static function decorateCollection(CollectionDecorator $data)
    {
        foreach ($data->get() as $key => $item) {
            if ($item instanceof MenuItemContract) {
                continue;
            }

            $data->put($key, static::decorate(static::prepare($item)));
        }

        return $data;
    }

    /**
     * Get the form element text.
     *
     * @return string
     */
    public function getFormElementText()
    {
        return $this->getMenuName();
    }

    /**
     * Get the value of the HTML attribute of the class of the form element.
     *
     * @return string
     */
    public function getFormElementClass()
    {
        return $this->getMenuClass();
    }

    /**
     * Get the menu item label.
     *
     * @return array
     */
    public function getMenuLabel()
    {
        return (isset($this->menu['label']) && (!$this->hasOriginal() || !$this->getTreeMethod('path')))
            ? ['label' => $this->menu['label'], 'class' => $this->menu['class']]
            : [];
    }

    /**
     * Set the menu item label.
     *
     * @param string $value
     * @param string $class
     * @return $this
     */
    public function setMenuLabel($value, $class = '')
    {
        $this->menu['label'] = $value;
        $this->menu['class'] = $class;

        return $this;
    }

    /**
     * Set true if the item is selected or false.
     *
     * @param bool $value
     * @return $this
     */
    public function setIsSelected($value = true)
    {
        return parent::setIsSelected($value)->setIsNodeSelected($value);
    }

    /**
     * Get the tree node ID.
     *
     * @return int|string
     */
    public function getNodeId()
    {
        return $this->menu[$this->getKeyName()] ?? $this->getKey();
    }

    /**
     * Set the tree node ID.
     *
     * @param int|string $value
     * @return $this
     */
    public function setNodeId($value)
    {
        $this->menu[$this->getKeyName()] = $value;

        return $this;
    }

    /**
     * Get the parent tree node ID.
     *
     * @return int|string
     */
    public function getNodeParentId()
    {
        return $this->menu['parent_id'] ?? $this->getMenuParentId();
    }

    /**
     * Set the parent tree node ID.
     *
     * @param int|string $value
     * @return $this
     */
    public function setNodeParentId($value)
    {
        $this->menu['parent_id'] = $value;

        return $this->setNodeId("{$value}_{$this->getNodeId()}");
    }

    /**
     * Get the sort value of the tree node.
     *
     * @return int|float
     */
    public function getNodeSorting()
    {
        return $this->menu['sorting'] ?? $this->getMenuSorting();
    }

    /**
     * Set the sort value of the tree node.
     *
     * @param int|float $value
     * @return $this
     */
    public function setNodeSorting($value)
    {
        $this->menu['sorting'] = $value;

        return $this;
    }

    /**
     * Get the value of the nesting level for the tree node.
     *
     * @return int
     */
    public function getNodeLevel()
    {
        return $this->menu['level'] ?? 0;
    }

    /**
     * Set the value of the nesting level for the tree node.
     *
     * @param int $value
     * @return $this
     */
    public function setNodeLevel($value)
    {
        $this->menu['level'] = (int) $value;

        return $this;
    }

    /**
     * Get true if the tree node is selected or false.
     *
     * @return bool
     */
    public function getIsNodeSelected()
    {
        return $this->menu['selected'] ?? $this->getIsSelected();
    }

    /**
     * Set true if the tree node is selected or false.
     *
     * @param bool $value
     * @return $this
     */
    public function setIsNodeSelected($value)
    {
        $this->menu['selected'] = (bool) $value;

        return $this;
    }

    /**
     * Get the subtree of the menu item.
     *
     * @param int|null $maxLevel
     * @return MenuDecorator
     */
    public function getTree($maxLevel = null)
    {
        if (!is_null($maxLevel)) {
            return $this->getOriginal()->getTree($this, $maxLevel);
        }

        $tree = MenuDecorator::make(
            static::make($this->menu['tree'] ?? [])
        );

        return $this->getOriginal() ? $tree->setOriginal($this->getOriginal()) : $tree;
    }

    /**
     * Set the subtree of the menu item.
     *
     * @param mixed $value
     * @return $this
     */
    public function setTree($value)
    {
        $this->menu['tree'] = $value;

        return $this;
    }

    /**
     * Get the siblings of the menu item.
     *
     * @return MenuDecorator
     */
    public function getSiblings()
    {
        return $this->getOriginal()->getSiblings($this);
    }

    /**
     * Get a sequence of parent menu items.
     *
     * @param int|null $maxLevel
     * @return MenuDecorator
     */
    public function getPath($maxLevel = null)
    {
        return $this->getOriginal()->getPath($this, $maxLevel);
    }

    /**
     * Get the parent menu item.
     *
     * @return MenuDecorator
     */
    public function getParent()
    {
        return $this->getOriginal()->getParent($this);
    }

    /**
     * Get the original collection of menu items.
     *
     * @return MenuDecorator
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Determine if there an original collection of menu items.
     *
     * @return bool
     */
    public function hasOriginal()
    {
        return isset($this->original);
    }

    /**
     * Set the original collection of tree items.
     *
     * @param MenuDecorator $original
     * @return mixed
     */
    public function setOriginal(MenuDecorator $original)
    {
        $this->original = $original;

        return $this;
    }

    /**
     * Get the tree method or specified.
     *
     * @param string $name
     * @return array
     */
    public function getTreeMethod($name = '')
    {
        return $this->getOriginal()->getTreeMethod($name);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        if ($this->getOriginal()) {
            $this->setVisibleProperties('menu');
        }

        return parent::toArray();
    }
}
