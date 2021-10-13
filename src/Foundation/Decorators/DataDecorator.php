<?php

namespace Laravelayers\Foundation\Decorators;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * The data decorator.
 *
 * @package App\Decorators
 */
class DataDecorator extends Decorator
{
    /**
     * Property name with decorator data.
     *
     * @var string
     */
    protected $dataKey = 'data';

    /**
     * The decorator's data.
     *
     * @var array|static
     */
    protected $data;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table;

    /**
     * The primary key for the data.
     *
     * @var string
     */
    protected $primaryKey;

    /**
     * The keys that are the original.
     *
     * @var array
     */
    protected $originalKeys = [];

    /**
     * The keys that are the date.
     *
     * @var array
     */
    protected $dateKeys = [];

    /**
     * The keys that are the timestamps.
     *
     * @var array
     */
    protected $timestampKeys = [];

    /**
     * Indicates if the data should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The loaded relationships.
     *
     * @var array
     */
    protected $relations = [];

    /**
     * The keys that should be hidden for serialization.
     *
     * @var array
     */
    protected $hiddenKeys = [];

    /**
     * Indicates whether the item is selected.
     *
     * @var bool
     */
    protected $isSelected = false;

    /**
     * Decorator startup method.
     *
     * @param $data
     * @return $this|static|mixed
     */
    public static function make($data = null)
    {
        $data = static::prepare($data);

        return $data instanceof CollectionDecorator
            ? static::decorateCollection($data)
            : static::decorate($data);
    }

    /**
     * Decorate the collection.
     *
     * @param CollectionDecorator $data
     * @return CollectionDecorator
     */
    protected static function decorateCollection(CollectionDecorator $data)
    {
        foreach ($data->get() as $key => $item) {
            if (static::isDecorator($item) === false) {
                continue;
            }

            if (is_array($item) || is_object($item)) {
                $data->put($key, static::decorate(static::prepare($item)));
            }
        }

        return $data;
    }

    /**
     * Decorate the data.
     *
     * @param $data
     * @return static
     */
    protected static function decorate($data)
    {
        if (static::isDecorator($data) === true) {
            if (!strcasecmp(get_class($data), self::class)) {
                $data = array_merge($data->get(), [
                    'table' => $data->getTable(),
                    'primaryKey' => $data->getKeyName(),
                    'originalKeys' => $data->getOriginalKeys(),
                    'dateKeys' => $data->getDateKeys(),
                    'timestampKeys' => $data->getTimestampKeys(),
                    'timestamps' => $data->timestamps,
                    'relations' => $data->getRelations(false),
                    'hiddenKeys' => $data->getHiddenKeys(),
                    'isSelected' => $data->getIsSelected(),
                    'visibleProperties' => $data->getVisibleProperties(),
                    'visibleGetters' => $data->getVisibleGetters()
                ]);

            }

            $data = new static($data);
        }

        return $data;
    }

    /**
     * Determine if the data is an instance of the decorator and not an instance of the current decorator class.
     *
     * @param $data
     * @return bool|null
     */
    static public function isDecorator($data)
    {
        $data = parent::isDecorator($data);

        if (!is_null($data)) {
            if (!strcasecmp(self::class, static::class)) {
                return false;
            }
        }

        return $data;
    }

    /**
     * Get the table name for the data.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Get the value of the data's primary key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->get($this->getKeyName() ?: 0);
    }

    /**
     * Get the primary key for the data.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * Set the primary key for the data.
     *
     * @param int|string $value
     * @return $this
     */
    public function setKeyName($value)
    {
        $this->primaryKey = $value;

        return $this;
    }

    /**
     * Get data from the collection, only for the original keys.
     *
     * @param bool $force
     * @param bool $decorate
     * @return array
     */
    public function getOnlyOriginal($force = true, $decorate = false)
    {
        $data = !$decorate ? $this->get() : $this->all();

        return $this->getOriginalKeys()
            ? Arr::only($data, $this->getOriginalKeys())
            : ($force ? $data : []);
    }

    /**
     * Get the original keys for the data.
     *
     * @return array
     */
    public function getOriginalKeys()
    {
        return $this->originalKeys;
    }

    /**
     * Sync the original keys with the current.
     *
     * @return $this
     */
    public function syncOriginalKeys()
    {
        $this->originalKeys = array_keys($this->get());

        return $this;
    }

    /**
     * Get the keys that are the date.
     *
     * @return array
     */
    public function getDateKeys()
    {
        return $this->dateKeys;
    }

    /**
     * Get the keys that are the timestamp.
     *
     * @return array
     */
    public function getTimestampKeys()
    {
        return $this->timestampKeys;
    }

    /**
     * Get all the loaded relations.
     *
     * @param bool $decorated
     * @return array
     */
    public function getRelations($decorated = true)
    {
        if ($decorated) {
            foreach ($this->relations as $relation => $value) {
                $this->relations[$relation] = $this->{$relation};
            }
        }

        return $this->relations;
    }

    /**
     * Get a specified relationship.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getRelation($name, $default = null)
    {
        if (!isset($this->relations[$name])) {
            return $default;
        }

        if (is_array($this->relations[$name]) || $this->relations[$name] instanceof Collection) {
            $this->relations[$name] = $this->decorateItem($this->relations[$name]);
        }

        return $this->relations[$name];
    }

    /**
     * Set the specific relationship.
     *
     * @param string $relation
     * @param mixed $value
     * @return $this
     */
    public function setRelation($relation, $value = [])
    {
        $this->relations[$relation] = $value;

        return $this;
    }

    /**
     * Set the entire relations array.
     *
     * @param array $relations
     * @return $this
     */
    public function setRelations(array $relations)
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * Get the hidden keys for the serialization.
     *
     * @return array
     */
    public function getHiddenKeys()
    {
        return $this->hiddenKeys;
    }

    /**
     * Set the hidden keys for the serialization.
     *
     * @param array|string|null $value
     * @return $this
     */
    public function setHiddenKeys($value = null)
    {
        if (is_array($value)) {
            $this->hiddenKeys = $value;
        } else {
            $this->hiddenKeys = array_merge($this->hiddenKeys, func_get_args());
        }

        return $this;
    }

    /**
     * Get true if the item is selected or false.
     *
     * @return bool
     */
    public function getIsSelected()
    {
        return $this->isSelected;
    }

    /**
     * Set true if the item is selected or false.
     *
     * @param bool $value
     * @return $this
     */
    public function setIsSelected($value = true)
    {
        $this->isSelected = (bool) $value;

        return $this;
    }

    /**
     * Call the method of rendering the decorator item by key if the method methodExists.
     *
     * @param string $key
     * @param bool $text
     * @return string
     */
    public function getRenderer($key, $text = false)
    {
        $method = Str::camel('render_' . $key);

        if (method_exists($this, $method)) {
            $result = $this->{$method}($text);
        } else {
            $data = $this->getDataKey();

            if (!is_null(static::isDecorator($data))) {
                return $data->getRenderer($key, $text);
            }

            $result = $this->{$key};
        }

        if ($text) {
            if (is_object($result)) {
                $result = $this->{$key};
            }

            if (is_array($result)) {
                $result = static::make($result);
            }
        }

        return (string) $result;
    }

    /**
     * Get the text of the decorator item by key.
     *
     * @param string $key
     * @param bool $strlen
     * @param bool $text
     * @return string
     */
    public function getRendererText($key, $strlen = false, $text = true)
    {
        $str = strip_tags($this->getRenderer($key, $text));
        $str = str_replace(["\r\n", "\r", "\n"], ' ', $str);
        $str = trim($str);

        return $strlen ? mb_strlen($str) : $str;
    }

    /**
     * Get the cropped text of the decorator item by key.
     *
     * @param string $key
     * @param int $length
     * @param bool $after
     * @param string $delimiter
     * @return string
     */
    public function getCroppedRenderText($key, $length, $after = false, $delimiter = ' ')
    {
        $str = $this->getRendererText($key);
        $strpos = mb_strlen($str) > $length ? mb_strpos($str, $delimiter, $length) : 0;

        return ($strpos || $after)
            ? mb_substr($str, $after ? $strpos : 0, $after && $strpos ? null : $strpos)
            : $str;
    }


    /**
     * Dynamically retrieve data from the decorator.
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        $value = parent::__get($key);

        if (!$this->has($key) && is_null($value) && !is_null($this->getRelation($key))) {
            return $this->getRelation($key);
        }

        return $value;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $vars = parent::toArray();

        $data = !empty($vars[$this->getDataKeyName()])
            ? $vars[$this->getDataKeyName()]
            : $vars;

        foreach($this->getRelations() as $relation => $value) {
            $data[$relation] = $value;
        }

        $array = [];

        foreach ($data as $key => $value) {
            if ($this instanceof DataDecorator && in_array($key, $this->getHiddenKeys())) {
                continue;
            }

            if (is_object($value)) {
                $value = $value instanceof Arrayable
                    ? $value->toArray()
                    : get_object_vars($value);
            }

            if (static::$snakeAttributes) {
                $key = Str::snake($key);
            }

            $array[$key] = $value;
        }

        if (!empty($vars[$this->getDataKeyName()])) {
            $array = array_merge($vars, [$this->getDataKeyName() => $array]);
        }

        return $array;
    }

    /**
     * Prepare the instance for cloning.
     *
     * @return void
     */
    public function __clone()
    {
        parent::__clone();

        foreach($this->getRelations(false) as $relation => $value)
        {
            if (!is_null(static::isDecorator($value))) {
                $this->setRelation($relation, clone $value);
            }
        }
    }
}
