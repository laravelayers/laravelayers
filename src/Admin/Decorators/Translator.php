<?php

namespace Laravelayers\Admin\Decorators;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

trait Translator
{
    /**
     * Get a translation for the given key in accordance with the specified format.
     *
     * @param string $format
     * @param string $key
     * @param array $replace
     * @param string $locale
     * @param bool $empty
     * @return string
     */
    public static function trans($format, $key = null, $replace = [], $locale = null, $empty = false)
    {
        if (is_iterable($key) && !$replace) {
            $replace = $key;
            $key = '';
        }

        $transKey = $format;

        if ($key) {
            $separator = Lang::has($format) ? '.' : '/';

            $transKey = strpos($format, '%s') !== false
                ? sprintf($format, $key)
                : str_replace('..', '.', "{$format}{$separator}{$key}");
        }

        $isTransKey = Lang::has($transKey);

        $parsedFile = explode('::', $transKey, 2);

        if (!empty($parsedFile[1])) {
            if (!$isTransKey) {
                $transKeyForLocale = $parsedFile[0] . '::' . $parsedFile[0] . '.' . $parsedFile[1];

                if ($isTransKey = Lang::has($transKeyForLocale)) {
                    $transKey = $transKeyForLocale;
                }
            }

            $transKeyForPath = static::prepareTranslationPath(static::getDefaultTranslationPath())
                . $parsedFile[1];

            if (!Lang::has($transKeyForPath)) {
                $transKeyForPath = static::prepareTranslationPath(static::initTranslationPath())
                    . $parsedFile[1];
            }

            $isTransKeyForPath = Lang::has($transKeyForPath);

            if ($isTransKeyForPath || !$isTransKey) {
                $transKey = $transKeyForPath;
                $isTransKey = $isTransKey ?: $isTransKeyForPath;
            }
        }

        if (!$isTransKey) {
            $transKey = '/lang/' . app()->getLocale() . '/' . trim($transKey, '/');
        }

        return (!$empty || $isTransKey)
            ? Lang::get($transKey, $replace, $locale)
            : '';
    }

    /**
     * Get a translation for the given key in accordance with the specified format r return an empty string.
     *
     * @param string $format
     * @param string $key
     * @param array $replace
     * @param string $locale
     * @return string
     */
    public static function transOrEmpty($format, $key = null, $replace = [], $locale = null)
    {
        return static::trans($format, $key, $replace, $locale, true);
    }

    /**
     * Prepare the translation path.
     *
     * @param string $path
     * @return string
     */
    protected static function prepareTranslationPath($path)
    {
        $path = preg_replace('/\.php$/', '', $path);

        $end = ltrim(strrchr($path, '/'), '/');

        return preg_replace("/{$end}\/{$end}[\/]*$/", $end, $path) . '.';
    }

    /**
     * Initialize the translation path.
     *
     * @return string
     */
    protected static function initTranslationPath()
    {
        return static::getDefaultTranslationPath();
    }

    /**
     * Get the default translation path.
     *
     * @return string
     */
    protected static function getDefaultTranslationPath()
    {
        $action = Route::resourceVerbs()[Request::route()->getActionMethod()] ?? Request::route()->getActionMethod();

        return preg_replace(
            '/\/'. $action .'$/i',
            '',
            preg_replace('/\/\{[^\}]*\}/', '', Request::route()->uri())
        );
    }
}
