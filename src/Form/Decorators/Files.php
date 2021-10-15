<?php

namespace Laravelayers\Form\Decorators;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Decorators\DataDecorator;

trait Files
{
    /**
     * Uploaded files.
     *
     * @var array
     */
    protected $uploadedFiles = [];

    /**
     * Get the files stored on a filesystem disk.
     *
     * @param string $disk
     * @param string $path
     * @param null $prefix
     * @return array
     */
    public function getStoredFiles($disk, $path = '', $prefix = null)
    {
        $disk = Storage::disk($disk);

        $prefix = $this->getFilePrefix($prefix);

        $_prefix = rtrim($prefix, '_');

        foreach ($disk->files($path) as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);

            if (!$prefix || Str::startsWith($name, $prefix) || $name == $_prefix) {
                $url = $disk->url($file);

                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    $url = $disk->path($file);
                }

                $urls[] = $url;
            }
        }

        return $urls ?? [];
    }

    /**
     * Get the uploaded files before store on a filesystem disk.
     *
     * @return CollectionDecorator
     */
    public function getUploadedFiles()
    {
        return DataDecorator::make(collect($this->uploadedFiles));

    }

    /**
     * Set the uploaded files before store on a filesystem disk.
     *
     * @param array|UploadedFile $files
     * @param string $disk
     * @param string $path
     * @param string|null $prefix
     * @param array $names
     * @param bool $ajax
     * @return array
     */
    public function setUploadedFiles($files, $disk, $path = '', $prefix = null, $names = [], $ajax = false)
    {
        $multiple = true;

        if (!is_array($files)) {
            $files = $files ? [$files] : [];

            $multiple = false;
        }

        $names = (array) $names;

        $uploaded = false;

        foreach ($files as $key => $file) {
            $url = $this->setUploadedFile($file, $disk, $path, $prefix, $names[$key] ?? null, $ajax);

            if ($url) {
                $urls[] = $url;
            }

            if (!$uploaded && $file instanceof UploadedFile) {
                $uploaded = true;
            }
        }

        if (($multiple && !$uploaded) || (!$multiple && ($uploaded || !$files))) {
            foreach ($this->getStoredfiles($disk, $path) as $key => $file) {
                $name = basename($file);

                if ($this->getUploadedFiles()->where('file', $name)->isEmpty()) {
                    $this->setUploadedFile('', $disk, $path, $prefix, $name);
                }
            }
        }

        return $urls ?? [];
    }

    /**
     * Set the uploaded file before store on a filesystem disk.
     *
     * @param UploadedFile|string $file
     * @param string $disk
     * @param string $path
     * @param string|null $prefix
     * @param string|null $name
     * @param bool $ajax
     * @return string
     */
    protected function setUploadedFile($file, $disk, $path = '', $prefix = null, $name = null, $ajax = false)
    {
        if ($ajax || !request()->ajax()) {
            $path = ($path = trim($path, '/')) ? "{$path}/" : '';

            if ($file instanceof UploadedFile) {
                $name = is_null($name)
                    ? $this->getFilePrefix($prefix) . $this->getFileName($file)
                    : rtrim($this->getFilePrefix($prefix) . $name, '_') . '.' . $file->getClientOriginalExtension();

                $url = Storage::disk($disk)->url($path . $name);

                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    $url = Storage::disk($disk)->path($path . $name);
                }
            }

            $this->uploadedFiles[] = ['file' => $file, 'disk' => $disk, 'path' => $path, 'name' => $name];
        }

        return $url ?? '';
    }

    /**
     * Get the file name.
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function getFileName($file)
    {
        return $file->hashName();
    }

    /**
     * Get the file prefix.
     *
     * @param null $prefix
     * @return string
     */
    protected function getFilePrefix($prefix = null)
    {
        $prefix = is_null($prefix) ? $this->getKey() : rtrim($prefix, '_');

        return $prefix = $prefix ? "{$prefix}_" : '';
    }
}
