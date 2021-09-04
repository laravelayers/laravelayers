<?php

namespace Laravelayers\Contracts\Admin\Decorators;

/**
 * @see \Laravelayers\Admin\Decorators\Translator
 */
interface Translator
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
    public static function trans($format, $key = null, $replace = [], $locale = null, $empty = false);

    /**
     * Get a translation for the given key in accordance with the specified format r return an empty string.
     *
     * @param string $format
     * @param string $key
     * @param array $replace
     * @param string $locale
     * @return string
     */
    public static function transOrEmpty($format, $key = null, $replace = [], $locale = null);
}
