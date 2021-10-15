<?php

namespace Laravelayers\Form\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Decorators\Decorator;

trait Images
{
    /**
     * Store the uploaded files on a filesystem disk.
     *
     * @param Decorator $item
     * @param bool $forced
     * @return $this
     */
    public function storeImages(Decorator $item, $forced = false)
    {
        $item->getElements();

        $files = $item->getUploadedImages();

        if ($files instanceof CollectionDecorator) {
            if ($this->getResult() || $forced) {
                foreach($files as $uploaded) {
                    if ($uploaded->file instanceof UploadedFile) {
                        $uploaded->file->storeAs($uploaded->path, $uploaded->sizes->first()->name, $uploaded->disk);
                    } elseif ($uploaded->sizes->isEmpty()) {
                        Storage::disk($uploaded->disk)->delete($uploaded->path . $uploaded->file);

                        $idDeleted = true;
                    }
                }
            }

            if (!empty($idDeleted)) {
                session()->flash('success', trans('form::form.alerts.success_delete'));
            }
        } else {
            $uploaded = $files;

            if (!$this->getResult() && !$forced) {
                $uploaded->put('file', '');
                $uploaded->put('stored', '');
            }

            if (!$uploaded->file instanceof UploadedFile) {
                $saved = (bool) $uploaded->file;

                $uploaded->put('file', '');
            }

            if (empty($saved)) {
                if ($uploaded->stored) {
                    foreach ($uploaded->stored->sizes as $size) {
                        Storage::disk($uploaded->disk)->delete($uploaded->path . $size->name);
                    }
                }
            }

            if ($uploaded->file) {
                $image = Image::make($uploaded->file);

                if ($cropped = json_decode($item->getCroppedImage())) {
                    if ($cropped->scaleX == -1) {
                        $image = $image->flip('h');
                    }

                    if ($cropped->scaleY == -1) {
                        $image = $image->flip('v');
                    }

                    if ($rotate = intval($cropped->rotate)) {
                        $image = $image->rotate(-$rotate);
                    }

                    if (($width = intval($cropped->width)) && ($height = intval($cropped->height))) {
                        $image = $image->crop($width, $height, intval($cropped->x), intval($cropped->y));
                    }
                }

                if ($uploaded->sizes->isEmpty()) {
                    $uploaded = $item->setImageSize()->getUploadedImages();
                }

                foreach ($uploaded->sizes as $key => $size) {
                    $_image = clone $image;

                    if ($size->height) {
                        $_image->heighten($size->height);
                    }

                    if ($size->width) {
                        $_image->widen($size->width);
                    }

                    $extension = $item->getImageExtension() ?: $uploaded['file']->getClientOriginalExtension();

                    Storage::disk($uploaded->disk)->put(
                        $uploaded->path . $size->name,
                        $_image->encode($extension, $size->quality)->getEncoded()
                    );
                }
            }
        }

        return $this;
    }
}
