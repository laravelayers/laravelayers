<?php

namespace Laravelayers\Foundation\Decorators;

use ArrayAccess;
use ArrayIterator;
use Countable;
use BadMethodCallException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use IteratorAggregate;
use JsonSerializable;
use Laravelayers\Foundation\Dto\Dto;
use Laravelayers\Pagination\Decorators\PaginatorDecorator;
use stdClass;

/**
 * The base class for decorators is the layer that wraps the repository data received in the service layer.
 *
 * The data from the model object is converted to a Data Transfer object (Dto)
 * and wrapped by the custom decorator methods.
 *
 * @package App\Decorators
 */
class Decorator implements Arrayable, ArrayAccess, Countable, IteratorAggregate, Jsonable, JsonSerializable
{
    /**
     * The properties that should be visible in serialization.
     *
     * @var array
     */
    protected $visibleProperties = [];

    /**
     * The getters that should be visible in serialization.
     *
     * @var array
     */
    protected $visibleGetters = [];

    /**
     * Indicates whether data keys are snake cased for serialization.
     *
     * @var bool
     */
    public static $snakeAttributes = true;

    /**
     * Decorator startup method.
     *
     * @param $data
     * @return $this|DataDecorator|CollectionDecorator|PaginatorDecorator
     */
    public static function make($data = [])
    {
        return app(DataDecorator::class, [$data]);
    }

    /**
     * Prepare data for decoration.
     *
     * @param $data
     * @return mixed
     */
    protected static function prepare($data)
    {
        if (!is_null(static::isDecorator($data))) {
            return $data;
        }

        if (is_array($data) || $data instanceof stdClass) {
            return new static($data);
        }

        $dto = app(Dto::class, [$data]);

        if ($data instanceof Collection) {
            if (!array_search(CollectionDecorator::class, class_parents(static::class))) {
                $data = app(CollectionDecorator::class, [$dto]);
            }
        } elseif ($data instanceof AbstractPaginator) {
            if (!array_search(PaginatorDecorator::class, class_parents(static::class))) {
                $data = app(PaginatorDecorator::class, [$dto]);
            }
        }

        if (is_null(static::isDecorator($data))) {
            $data = $dto->get();
        }

        if (static::isDecorator($data) !== false) {
            $data = static::make($data);
        }

        return $data;
    }

    /**
     * Create a new Decorator instance.
     *
     * @param $data
     */
    protected function __construct($data)
    {
        if (!isset($data->{$this->getDataKeyName()}) || static::isDecorator($data) === true) {
            $data = (object) [
                $this->getDataKeyName() => $data instanceof Arrayable ? $data : (array) $data
            ];
        }

        foreach ($data->{$this->getDataKeyName()} as $name => $value) {
            $propertyName = Str::camel($name);

            if (property_exists($this, $propertyName)) {
                if ($value) {
                    $this->$propertyName = is_array($value)
                        ? array_merge((array) $this->$propertyName, $value)
                        : $value;
                }

                unset($data->{$this->getDataKeyName()}[$name]);
            }
        }

        foreach (get_object_vars($data) as $name => $value) {
            $name = Str::camel($name);
            if (property_exists($this, $name)) {
                $this->$name = $value;
            }
        }
    }

    /**
     * Determine if the data is an instance of the decorator and not an instance of the current decorator class.
     *
     * @param $data
     * @return bool|null
     */
    static public function isDecorator($data)
    {
        if ($data instanceof self) {
            return strcasecmp(get_class($data), static::class)
                ? true
                : false;
        }

        return null;
    }

    /**
     * Dynamically retrieve data from the decorator.
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($this->getterExists($key)) {
            return $this->getGetter($key);
        }

        return $this->get($key);
    }

    /**
     * Checks if the decorator getter methodExists.
     *
     * @param string $key
     * @return bool
     */
    protected function getterExists($key)
    {
        $method = $this->getGetterName($key);

        if (!method_exists($this, $method)) {
            $data = $this->getDataKey();

            if (!is_null(static::isDecorator($data))) {
                return $data->getterExists($method);
            }

            return false;
        }

        try {
            return (new \ReflectionClass($this))->getMethod($method)->isPublic();
        } catch (\ReflectionException $e) {

        }

        return true;
    }

    /**
     * Get the value of the getter for the decorator key.
     *
     * @param $key
     * @return mixed
     */
    protected function getGetter($key)
    {
        return $this->{$this->getGetterName($key)}();
    }

    /**
     * Get the name of the getter for the decorator key.
     *
     * @param $key
     * @return string
     */
    protected function getGetterName($key)
    {
        if (!Str::startsWith($key, 'get')) {
            return Str::camel('get_' . $key);
        }

        return $key;
    }

    /**
     * Handle dynamic method calls into the decorator.
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        try {
            $data = $this->getDataKey();

            if (method_exists($this, $method)
                || is_array($data)
            ) {
                throw new BadMethodCallException();
            }

            return $data->$method(...$parameters);
        } catch (BadMethodCallException $e) {
            throw new BadMethodCallException(
                sprintf('Call to undefined method %s::%s()', get_class($this), $method)
            );
        }
    }

    /**
     * Dynamically check if the decorator key methodExists.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        if (!$this->offsetExists($key)) {
            return $this->getterExists($key);
        }

        return true;
    }

    /**
     * Dynamically set properties on the decorator.
     *
     * @param $key
     * @param $value
     * @throws \Exception
     */
    public function __set($key, $value)
    {
        if (property_exists($this, $key)) {
            throw new \Exception(sprintf('Cannot access property %s::$%s', get_class($this), $key));
        }

        if (is_array($value) || $value instanceof Collection) {
            $value = self::make($value);
        }

        $this->{$key} = $value;
    }

    /**
     * Get all of the data in the decorator.
     *
     * @return array
     */
    public function all()
    {
        $data = $this->get();

        foreach ($data as $key => $value) {
            $data[$key] = $this->{$key};
        }

        return $data;
    }

    /**
     * Determine if the decorator is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->get());
    }

    /**
     * Determine if the decorator is not empty.
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return ! $this->isEmpty();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $vars = [];
        $visibleProperties = array_merge($this->getVisibleProperties());

        foreach ($visibleProperties as $property) {
            $vars[$property] = $this->{$property};
        }

        $visibleProperties[] = $this->getDataKeyName();

        $vars[$this->getDataKeyName()] = $this instanceof DataDecorator
            ? $this->all()
            : $this->getDataKey()
        ;

        foreach($this->getVisibleGetters() as $getter) {
            $getterKey = Str::camel(Str::after($getter, 'get'));

            $vars[$getterKey] = $this->{$getter}();

            $visibleProperties = array_merge($visibleProperties, (array) $getterKey);
        }

        $vars = Arr::only($vars, $visibleProperties);

        $array = [];

        foreach ($vars as $propertyName => $propertyValue) {
            if (is_object($propertyValue)) {
                $propertyValue = $propertyValue instanceof Arrayable
                    ? $propertyValue->toArray()
                    : get_object_vars($propertyValue);
            }

            if (static::$snakeAttributes) {
                $propertyName = Str::snake($propertyName);
            }

            $array[$propertyName] = $propertyValue;
        }

        if (count($array) == 1) {
            $array = current($array);
        }

        return $array;
    }

    /**
     * Determine if the given item methodExists.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->get()[$this->getItemName($key)]);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->__get($key);
    }

    /**
     * Set the item at the given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->{$this->getDataKeyName()}[] = $value;
        } else {
            $this->{$this->getDataKeyName()}[$this->getItemName($key)] = $value;
        }
    }

    /**
     * Unset the item at the given key.
     *
     * @param  mixed  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->{$this->getDataKeyName()}[$key]);
    }

    /**
     * Determine if an item methodExists in the decorator by key.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function has($key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        foreach ($keys as $key) {
            if (!array_key_exists($this->getItemName($key), $this->get())) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get data from the decorator or item from the data by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        $data = $this->getDataKey();

        if (!is_null(static::isDecorator($data))) {
            return $data->get($key, $default);
        }

        if (is_null($key)) {
            if ($data instanceof Collection) {
                $data = $data->all();
            }
        } else {
            $key = $this->getItemName($key);

            if (!$this->offsetExists($key)) {
                return value($default);
            }

            $data = $data[$key];

            if (is_array($data) || $data instanceof Collection) {
                $data = $this->decorateItem($data);
                $this->put($key, $data);
            }
        }

        return $data;
    }

    /**
     * Decorate data item.
     *
     * @param $data
     * @return array|Decorator
     */
    protected function decorateItem($data)
    {
        return self::make($data);
    }

    /**
     * Get undecorated data from the decorator.
     *
     * @return mixed
     */
    public function getData()
    {
        $data = $this->getDataKey();

        if (!is_null(static::isDecorator($data))) {
            return $data->getData();
        }

        return $data;
    }

    /**
     * Set undecorated data for the decorator.
     *
     * @param $value
     * @return $this|Decorator
     */
    public function setData($value)
    {
        if (!is_null(static::isDecorator($this->getDataKey()))) {
            return $this->getDataKey()->setData($value);
        }

        return $this->setDataKey($value);
    }

    /**
     * Get data from the decorator.
     *
     * @return mixed
     */
    public function getDataKey()
    {
        return $this->{$this->getDataKeyName()};
    }

    /**
     * Set data for the decorator.
     *
     * @param $value
     * @return $this
     */
    public function setDataKey($value)
    {
        $this->{$this->getDataKeyName()} = $value;

        return clone $this;
    }

    /**
     * Get the key name with decorator data.
     *
     * @return null|string
     */
    protected function getDataKeyName()
    {
        return $this->dataKey;
    }

    /**
     * Get the item name.
     *
     * @param mixed $key
     * @return mixed|null
     */
    public function getItemName($key)
    {
        $method = Str::camel("get_as_{$key}");

        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return $key;
    }

    /**
     * Get the visible properties for the serialization.
     *
     * @return array
     */
    public function getVisibleProperties()
    {
        return $this->visibleProperties;
    }

    /**
     * Set the visible properties for the serialization.
     *
     * @param array|string $value
     * @return $this
     */
    public function setVisibleProperties($value)
    {
        $this->visibleProperties = (array) $value;

        return $this;
    }

    /**
     * Get the visible getters for the serialization.
     *
     * @return array
     */
    public function getVisibleGetters()
    {
        return $this->visibleGetters;
    }

    /**
     * Set the visible getters for the serialization.
     *
     * @param array|string $value
     * @return $this
     */
    public function setVisibleGetters($value)
    {
        $this->visibleGetters = (array) $value;

        return $this;
    }

    /**
     * Put an item in the Decorator by key.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return $this
     */
    public function put($key, $value)
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * Remove an item from the Decorator by key.
     *
     * @param  string|array  $keys
     * @return $this
     */
    public function forget($keys)
    {
        foreach ((array) $keys as $key) {
            $this->offsetUnset($key);
        }

        return $this;
    }

    /**
     * Get the number of items for the current page.
     *
     * @return int
     */
    public function count()
    {
        return count($this->get());
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->all());
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Prepare the instance for cloning.
     *
     * @return void
     */
    public function __clone()
    {
        foreach($this->getData() as $key => $value)
        {
            if (!is_null(static::isDecorator($value))) {
                $this[$key] = clone $value;
            }
        }
    }
}
