<?php

namespace Laravelayers\Contracts\Foundation\Services;

use Laravelayers\Foundation\Decorators\Decorator;

/**
 * @see \Laravelayers\Form\Services\Files
 */
interface Files
{
    /**
     * Store the uploaded files on a filesystem disk.
     *
     * @param Decorator $item
     * @param bool $forced
     * @return $this
     */
    public function storeFiles(Decorator $item, $forced = false);
}
