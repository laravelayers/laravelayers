<?php

namespace Laravelayers\Form\Decorators;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Decorators\DataDecorator;

trait Images
{
    /**
     * Uploaded images.
     *
     * @var array|Collection
     */
    protected $uploadedImages = [];

    /**
     * Cropped image data.
     *
     * @var string
     */
    protected $croppedImage = '';

    /**
     * Get the images stored on a filesystem disk.
     *
     * @param string $disk
     * @param string $path
     * @param null $prefix
     * @return array
     */
    public function getStoredImages($disk, $path = '', $prefix = null)
    {
        $storage = Storage::disk($disk);

        $filePrefix = $this->getFilePrefix($prefix);

        $filePrefixName = rtrim($filePrefix, '_');

        $urls = [];

        foreach ($storage->files($path) as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);

            if (!$filePrefix || Str::startsWith($name, $filePrefix) || $name == $filePrefixName) {
                $urls[] = $storage->url($file);
            }
        }

        if ($urls && !$this->uploadedImages) {
            $this->setUploadedImage(end($urls), $disk, $path, $prefix);

            $this->uploadedImages = ['stored' => $this->getUploadedImages()];
        }

        return $urls;
    }

    /**
     * Get URL of uploaded images.
     *
     * @param string|null $size
     * @return array|string|null
     */
    public function getImageUrls($size = null)
    {
        $uploaded = $this->getUploadedImages();

        if (!$uploaded instanceof CollectionDecorator) {
            $uploaded = [$uploaded];
        }

        foreach($uploaded as $file) {
            if (isset($file['file'])) {
                if (!$size && !is_null($size)) {
                    $size = 'default';
                }

                if ($size) {
                    return $file['sizes'][$size]['url'] ?? null;
                }

                foreach ($file['sizes'] as $key => $value) {
                    if (!empty($value['url'])) {
                        $urls[$key] = $value['url'];
                    }
                }
            }
        }

        return $urls ?? (!is_null($size) ? null : []);
    }

    /**
     * Get the uploaded images before store on a filesystem disk.
     *
     * @return DataDecorator|CollectionDecorator
     */
    public function getUploadedImages()
    {
        if (is_array($this->uploadedImages)) {
            if (empty($this->uploadedImages['sizes'])) {
                $this->setImageSize();
            }
        }

        return DataDecorator::make($this->uploadedImages);
    }

    /**
     * Set the uploaded images before store on a filesystem disk.
     *
     * @param array|string|UploadedFile $files
     * @param string $disk
     * @param string $path
     * @param string|null $prefix
     * @return array|$this
     */
    public function setUploadedImages($files, $disk, $path = '', $prefix = null)
    {
        if (!is_array($files)) {
            return $this->setUploadedImage($files, $disk, $path, $prefix);
        }

        if (!$files) {
            $files = $this->getStoredImages($disk, $path, $prefix);
        }

        foreach($files as $file) {
            $this->uploadedImages = [];

            if ($file instanceof UploadedFile) {
                $size = pathinfo($this->getImageName($file), PATHINFO_FILENAME);
            } else {
                $file = basename($file);
                $size = Str::after(pathinfo($file, PATHINFO_FILENAME), $this->getImagePrefix());
            }

            $this->setUploadedImage($file, $disk, $path, $prefix);

            $uploaded[] = $this->setUploadedImage($file, $disk, $path, $prefix)
                ->setImageSize($size, 0, 0, 100)
                ->getUploadedImages();

            $urls[] = current($this->getImageUrls());
        }

        $this->uploadedImages = collect($uploaded ?? []);

        return $urls ?? [];
    }

    /**
     * Set the uploaded image before store on a filesystem disk.
     *
     * @param UploadedFile|string $file
     * @param string $disk
     * @param string $path
     * @param string|null $prefix
     * @return $this
     */
    protected function setUploadedImage($file, $disk, $path = '', $prefix = null)
    {
        if ($file instanceof UploadedFile) {
            $extension = $file->getClientOriginalExtension();
        } else {
            if (!empty($this->uploadedImages['file']) && !$this->uploadedImages['file'] instanceof UploadedFile) {
                $stored = $this->uploadedImages;
            } elseif (!empty($this->uploadedImages['stored'])) {
                $stored = $this->uploadedImages['stored'];
            }

            $extension = pathinfo($file, PATHINFO_EXTENSION);

            if ($file && !$extension) {
                $extension = $file;
                $file = '';
            }
        }

        $this->uploadedImages = [
            'file' => $file,
            'disk' => $disk,
            'path' => ($path = trim($path, '/')) ? "{$path}/" : '',
            'prefix' => $prefix,
            'extension' => $extension,
            'sizes' => collect([])
        ];

        if (!empty($stored)) {
            $this->uploadedImages['stored'] = $stored;
        }

        return $this;
    }

    /**
     * Get the image name.
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function getImageName($file)
    {
        return $file->hashName();
    }

    /**
     * Get the size of the image stored on a filesystem disk.
     *
     * @param string $image
     * @param string $size
     * @param string $disk
     * @param string $path
     * @param null $prefix
     * @return string
     */
    public function getImageSize($image, $size = null, $disk, $path = '', $prefix = null)
    {
        if ($image) {
            if (!$size && !is_null($size)) {
                $size = 'default';
            }

            $image = !empty($this->uploadedImages['extension']) ? $this->uploadedImages['extension'] : $image;
            $disk = $this->uploadedImages['disk'] ?? $disk;
            $path = $this->uploadedImages['path'] ?? $path;
            $prefix = $this->uploadedImages['prefix'] ?? $prefix;

            $name = $size
                ? $this->getImageSizeName(pathinfo($image, PATHINFO_EXTENSION) ?: $image, $size, $path, $prefix)
                : $this->getImageSizeName('', basename($image), $path, '');

            $storage = Storage::disk($disk);

            $url = $storage->exists($name) ? $storage->url($name) : '';

            if ($url) {
                $this->setUploadedImage($url, $disk, $path, $prefix);
            }
        }

        if (empty($url)) {
            $this->setUploadedImage(null, $disk, $path, $prefix);
        }

        return $url ?? '';
    }
    
    /**
     * Set the size оf the uploaded image before store on a filesystem disk.
     *
     * @param string $size
     * @param int $width
     * @param int $height
     * @param int $quality
     * @return $this
     */
    public function setImageSize($size = '', $width = 0, $height = 0, $quality = 90)
    {
        $sizeName = $size;

        if (!$size) {
            $size = 'default';
            $sizeName = '';
        }

        $quality = ($quality > 0 && $quality <= 100) ? $quality : null;

        if (!empty($this->uploadedImages['file'])) {
            if ($this->uploadedImages['file'] instanceof UploadedFile) {
                $extension = $this->uploadedImages['extension'] ?? $this->uploadedImages['file']->getClientOriginalExtension();
            } else {
                $extension = pathinfo($this->uploadedImages['file'], PATHINFO_EXTENSION);
            }

            $url = Storage::disk($this->uploadedImages['disk'])->url(
                $this->getImageSizeName($extension, $sizeName, $this->uploadedImages['path'], $this->uploadedImages['prefix'])
            );

            $this->uploadedImages['sizes'][$size] = $this->prepareImageSize($url, $width, $height, $quality);
        }

        if (!empty($this->uploadedImages['stored'])) {
            $extension = pathinfo($this->uploadedImages['stored']['file'], PATHINFO_EXTENSION);

            $url = Storage::disk($this->uploadedImages['stored']['disk'])->url(
                $this->getImageSizeName($extension, $sizeName, $this->uploadedImages['stored']['path'], $this->uploadedImages['stored']['prefix'])
            );

            $this->uploadedImages['stored']['sizes'][$size] = $this->prepareImageSize($url, $width, $height, $quality);
        }

        return $this;
    }

    /**
     * Prepare the size оf the uploaded image before store on a filesystem disk.
     *
     * @param string $url
     * @param int|null $width
     * @param int|null $height
     * @param int $quality
     * @return array
     */
    protected function prepareImageSize($url = '', $width = null, $height = null, $quality = 90)
    {
        return [
            'width' => $width,
            'height' => $height,
            'quality' => $quality,
            'name' => basename($url),
            'url' => $url
        ];
    }
    
    /**
     * Get the name of the image size.
     *
     * @param string $extension
     * @param string $name
     * @param string $path
     * @param string|null $prefix
     * @return string
     */
    protected function getImageSizeName($extension, $name = '', $path = '', $prefix = null)
    {
        if ($extension) {
            $extension = $extension ? ".{$extension}" : '';
        }

        if ($name == 'default') {
            $name = '';
        }

        $name = rtrim($this->getImagePrefix($prefix) . $name, '_') . $extension;

        return ($path = trim($path, '/')) ? "{$path}/{$name}" : $name;
    }

    /**
     * Get the extension оf the uploaded image before store on a filesystem disk.
     *
     * @return string|null
     */
    public function getImageExtension()
    {
        return $this->uploadedImages['extension'] ?? null;
    }

    /**
     * Set the extension оf the uploaded image before store on a filesystem disk.
     *
     * @param string $extension
     * @return $this
     */
    public function setImageExtension($extension)
    {
        if (!empty($this->uploadedImages['file']) && $this->uploadedImages['file'] instanceof UploadedFile) {
            $this->uploadedImages['extension'] = pathinfo($extension, PATHINFO_EXTENSION) ?: $extension;

            foreach($this->uploadedImages['sizes'] as $size => $value)
            {
                $this->setImageSize($size, $value['width'], $value['height'], $value['quality']);
            }
        }

        return $this;
    }
    
    /**
     * Get cropped image data.
     *
     * @return string
     */
    public function getCroppedImage()
    {
        return $this->croppedImage;
    }

    /**
     * Set cropped image data.
     *
     * @param string $value
     * @return $this
     */
    public function setCroppedImage($value)
    {
        $this->croppedImage = $value;

        return $this;
    }

    /**
     * Get the image prefix.
     *
     * @param string|null $prefix
     * @return string
     */
    protected function getImagePrefix($prefix = null)
    {
        $prefix = is_null($prefix) ? $this->getKey() : rtrim($prefix, '_');

        return $prefix = $prefix ? "{$prefix}_" : '';
    }
}
