<?php

namespace Laravelayers\Contracts\Navigation;

/**
 * @see \Laravelayers\Navigation\Tree
 */
interface Tree
{
    /**
     * Get the tree from the items for the specified node ID and the specified max level.
     *
     * @param \Traversable $items
     * @param int $id
     * @param null|int $maxLevel
     * @return \Illuminate\Support\Collection
     */
    public function getTree($items, $id = 0, $maxLevel = null);

    /**
     * Get the sequence of the parent nodes from the items for the specified node ID and the specified max level.
     *
     * @param \Traversable $items
     * @param int|\Laravelayers\Contracts\Navigation\MenuItem $id
     * @param null|int $maxLevel
     * @return \Illuminate\Support\Collection
     */
    public function getPath($items, $id, $maxLevel = null);

    /**
     * Get the node from the items for the specified node ID and siblings for the specified max level.
     *
     * @param \Traversable $items
     * @param int|MenuItem $id
     * @param null|int $maxLevel
     * @return \Illuminate\Support\Collection
     */
    public function getNode($items, $id, $maxLevel = null);
}
