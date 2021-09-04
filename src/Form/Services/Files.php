<?php

namespace Laravelayers\Form\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravelayers\Foundation\Decorators\Decorator;

trait Files
{
    /**
     * Store the uploaded files on a filesystem disk.
     *
     * @param Decorator $item
     * @param bool $forced
     * @return $this
     */
    public function storeFiles(Decorator $item, $forced = false)
    {
        $item->getElements();

        $files = $item->getUploadedfiles();

        if ($this->getResult() || $forced) {
            foreach($files as $uploaded) {
                if ($uploaded->file instanceof UploadedFile) {
                    $uploaded->file->storeAs($uploaded->path, $uploaded->name, $uploaded->disk);
                } elseif (!$uploaded->file) {
                    Storage::disk($uploaded->disk)->delete($uploaded->path . $uploaded->name);
                }
            }
        }

        return $this;
    }
}
