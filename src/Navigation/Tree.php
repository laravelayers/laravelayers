<?php

namespace Laravelayers\Navigation;

use Laravelayers\Contracts\Navigation\MenuItem as MenuItemContract;
use Laravelayers\Contracts\Navigation\Tree as TreeContract;

/**
 * The class for get a tree data structure from a collection of items
 * that implement the interface @see \Laravelayers\Contracts\Menu\TreeItem.
 */
class Tree implements TreeContract
{
    /**
     * Get the tree from the items for the specified node ID and the specified max level.
     *
     * @param \Traversable $items
     * @param int $id
     * @param null|int $maxLevel
     * @return \Illuminate\Support\Collection
     */
    public function getTree($items, $id = 0, $maxLevel = null)
    {
        $id = $this->prepareNodeId($id);

        return $this->makeTree(
            $this->prepareTree($items), $id, $maxLevel
        );
    }

    /**
     * Get the sequence of the parent nodes from the items for the specified node ID and the specified max level.
     *
     * @param \Traversable $items
     * @param int|\Laravelayers\Contracts\Navigation\MenuItem $id
     * @param null|int $maxLevel
     * @return \Illuminate\Support\Collection
     */
    public function getPath($items, $id, $maxLevel = null)
    {
        $items = $this->prepareTree($items);

        $id = $this->prepareNodeId($id);

        if (!is_null($maxLevel) && $maxLevel < 1) {
            $maxLevel = 1;
        }

        $root = $this->makePath(
            $items, $id, $maxLevel
        );

        $items[0] = !empty($root[0]) ? $root[0] : $root;

        return $this->makeTree($items);
    }

    /**
     * Get the node from the items for the specified node ID and siblings for the specified max level.
     *
     * @param \Traversable $items
     * @param int|\Laravelayers\Contracts\Navigation\MenuItem $id
     * @param null|int $maxLevel
     * @return \Illuminate\Support\Collection
     */
    public function getNode($items, $id, $maxLevel = null)
    {
        $id = $this->prepareNodeId($id);

        return $this->makeTree(
            $this->makeNode(
                $this->prepareTree($items), $id
            ), 0, $maxLevel
        );
    }

    /**
     * Prepare the node ID.
     *
     * @param int|\Laravelayers\Contracts\Navigation\MenuItem $id
     * @return mixed
     */
    protected function prepareNodeId($id)
    {
        if ($id instanceof MenuItemContract) {
            $id = $id->getNodeId();
        }

        return (is_int($id) || is_string($id))
            ? $id
            : 0;
    }

    /**
     * Make a tree of items.
     *
     * @param $items
     * @param int $id
     * @param int|null $maxLevel
     * @param int $level
     * @return \Illuminate\Support\Collection
     */
    protected function makeTree($items, $id = 0, int $maxLevel = null, $level = 0)
    {
        $tree = [];

        if (is_null($maxLevel) || $maxLevel >= 0) {
            $maxLevel--;

            if (!empty($items[$id])) {
                foreach ($items[$id] as $key => $item) {
                    $item->setNodeLevel($level);

                    $item->setTree(
                        $this->makeTree($items, $item->getNodeId(), $maxLevel, $level + 1)
                    );

                    $tree[] = $item;
                }

                $tree = $this->sortNodes($tree);
            }
        }

        return collect($tree);
    }

    /**
     * Make the sequence of the parent nodes for the specified node ID and the specified max level.
     *
     * @param $items
     * @param $id
     * @param int|null $maxLevel
     * @param array $parents
     * @return array
     */
    protected function makePath($items, $id, int $maxLevel = null, $parents = [])
    {
        if ($id) {
            foreach ($items as $key => $item) {
                if (!empty($item[$id])) {
                    $parents[0][$id] = clone $item[$id];

                    if (is_null($maxLevel) || $maxLevel > 0) {
                        if ($maxLevel) {
                            $maxLevel--;
                        }

                        if ($item[$id]->getNodeParentId()) {
                            $parents = $this->makePath($items, $item[$id]->getNodeParentId(), $maxLevel, $parents);
                        }

                        $count = count($parents[0]);

                        $i = 0;
                        foreach ($parents[0] as $key => $value) {
                            $parents[0][$key]->setNodeSorting($count);

                            if (!$i) {
                                $parents[0][$key]->setIsSelected(true);
                            } else {
                                $parents[0][$key]->setIsSelected(false);
                            }

                            $count--;
                            $i++;
                        }
                    }
                }
            }
        }

        return $parents;
    }

    /**
     * Make a node for the specified node ID and siblings for the specified max level.
     *
     * @param $items
     * @param $id
     * @return array|null
     */
    protected function makeNode($items, $id)
    {
        if ($id) {
            foreach ($items as $key => $item) {
                if (!empty($item[$id])) {
                    $items[0] = [
                        $id => $item[$id]
                    ];

                    return $items;
                }
            }
        }

        return null;
    }

    /**
     * Sort the nodes.
     *
     * @param $items
     * @return mixed
     */
    protected function sortNodes($items)
    {
        if (!is_null(current($items)->getNodeSorting())) {
            array_multisort(array_map(function ($item) {
                return $item->getNodeSorting();
            }, $items), SORT_ASC, $items);
        }

        return $items;
    }

    /**
     * Prepare a tree of items.
     *
     * @param $items
     * @return array
     */
    protected function prepareTree($items)
    {
        $groups = [];

        foreach($items as $k => $item) {
            $item = $this->prepareTreeItem($item);

            if ($item->getIsNodeSelected()) {
                $selectedId = $item->getNodeId();
            }

            $parentId = $item->getNodeParentId()
                ? $item->getNodeParentId()
                : 0;

            $groups[$parentId][$item->getNodeId()] = $item;
        }

        if (!empty($selectedId)) {
            $groups = $this->markParentNodes($groups, $selectedId);
        }

        return $groups;
    }

    /**
     * Prepare the tree item.
     *
     * @param \Laravelayers\Contracts\Navigation\MenuItem $item
     * @return \Laravelayers\Contracts\Navigation\MenuItem
     */
    protected function prepareTreeItem(MenuItemContract $item)
    {
        return clone $item;
    }

    /**
     * Mark the parent nodes of the selected node ID.
     *
     * @param $items
     * @param $id
     * @return mixed
     */
    protected function markParentNodes($items, $id)
    {
        $parents = $this->makePath($items, $id);

        foreach($items as $parentId => $group) {
            foreach($group as $key => $item) {
                if ($key != $id) {
                    if (!empty($parents[0][$item->getNodeId()])) {
                        $item->setIsNodeSelected(true);
                    }
                }
            }

        }

        return $items;
    }
}
