<?php

namespace Laravelayers\Contracts\Foundation\Services;

use Laravelayers\Foundation\Decorators\Decorator;

/**
 * @see \Laravelayers\Form\Services\Images
 */
interface Images
{
    /**
     * Store the uploaded files on a filesystem disk.
     *
     * @param Decorator $item
     * @param bool $forced
     * @return $this
     */
    public function storeImages(Decorator $item, $forced = false);
}
