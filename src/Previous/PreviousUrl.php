<?php

namespace Laravelayers\Previous;

class PreviousUrl
{
    /**
     * The name of the query string parameter for the hash of the previous URL.
     *
     * @var string
     */
    protected static $inputName = 'previous';

    /**
     * Calculate the hash from the current or specified URL.
     *
     * @param string|null $url
     * @return string
     */
    public static function hash($url = null)
    {
        if (!$url) {
            $url = static::fullUrl();
        }

        return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)
            ? md5($url)
            : 1;
    }

    /**
     * Get the hash of the previous URL from the session.
     *
     * @return string
     */
    public static function getHash()
    {
        return session()->get(static::getInputName() . '.hash');
    }

    /**
     * Add a query string parameter with a hash of the current or previous URL to the specified URL.
     *
     * @param string $url
     * @param bool $previous
     * @return string
     */
    public static function addHash($url, $previous = false)
    {
        if (!static::getInputName()) {
            return $url;
        }

        $url = explode('?', $url, 2);

        if (!empty($url[1])) {
            parse_str($url[1], $query);
        }

        $query = http_build_query(array_merge($query ?? [], $previous ? static::getQuery() : static::query()));

        return $url = $url[0] . ($query ? '?' . $query : '');
    }

    /**
     * Add a query string parameter with a hash of the previous URL to the specified URL.
     *
     * @param string $url
     * @return string
     */
    public static function addQueryHash($url)
    {
        return static::addHash($url, true);
    }

    /**
     * Get the full URL for the request.
     *
     * @param string|null $url
     * @return string
     */
    public static function fullUrl($url = null)
    {
        if ($url) {
            $parsedUrl = explode('?', $url, 2);

            if (empty($parsedUrl[1])) {
                $parsedUrl[1] = '';
            }
        }

        $url = $parsedUrl[0] ?? request()->url();

        $query = $parsedUrl[1] ?? request()->getQueryString();

        if ($query) {
            $query = explode('&', $query);

            asort($query);

            $query = '?' . implode('&', $query);
        }

        return $url . $query;
    }

    /**
     * Get the previous URL from the session.
     *
     * @return string
     */
    public static function getUrl()
    {
        return session()->get(static::getInputName() . '.url');
    }

    /**
     * Get the previous URL parameter from the query string.
     *
     * @param string|null $url
     * @return string
     */
    public static function getUrlFromQuery($url = null)
    {
        if ($url) {
            $urls = parse_url($url);

            if (!empty($urls['query'])) {
                $queries = explode('&', $urls['query']);

                foreach($queries as $param) {
                    $params = explode('=', $param, 2);

                    if (array_shift($params) == static::getInputName()) {
                        $inputName = array_shift($params);
                    }
                }
            }
        }

        $inputName = $inputName ?? request()->get(static::getInputName());

        return session()->get(static::getInputName() . '.hashes', [])[$inputName] ?? '';
    }

    /**
     * Get an array for a query string with a hash of the current URL.
     *
     * @return array
     */
    public static function query()
    {
        return static::getInputName() ? [static::getInputName() => static::hash()] : [];
    }

    /**
     * Get an array for a query string with a hash of the previous URL from the request.
     *
     * @return array
     */
    public static function getQuery()
    {
        return ($input = PreviousUrl::getInput()) ? [static::getInputName() => $input] : [];
    }

    /**
     * Get the name of the parameter requiring a redirect to the previous URL.
     *
     * @return string|null
     */
    public static function getRedirectInputName()
    {
        return !static::getInputName() ?: static::getInputName() . '.redirect';
    }

    /**
     * Get the hash of the previous URL from the request.
     *
     * @return string
     */
    public static function getInput()
    {
        return request()->get(static::getInputName(), request()->old(static::getInputName())) ?: '';
    }

    /**
     * Get the name of the request parameter for the hash of the previous URL.
     *
     * @return string
     */
    public static function getInputName()
    {
        return static::$inputName;
    }

    /**
     * Set the hash name of the previous URL.
     *
     * @param string $value
     * @return void
     */
    public static function setInputName($value)
    {
        static::$inputName = $value;
    }
}
