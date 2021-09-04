<?php

namespace Laravelayers\Foundation\Dto;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Laravelayers\Contracts\Foundation\Dto\Dtoable;

/**
 * Data Transfer Objects.
 *
 * Conversion from a model object to a simple data object is read-only.
 * Used in the decorator layer when getting data from the repository in the service layer.
 *
 * @see \Laravelayers\Contracts\Foundation\Dto\Dtoable
 * @see \Laravelayers\Foundation\CollectionMacros
 */
class Dto
{
    /**
     * The DTO's data.
     *
     * @var object
     */
    protected $entity;

    /**
     * DTO startup method.
     *
     * @param $data
     * @return $this
     */
    public static function make($data)
    {
        if ($data instanceof Dto) {
            return $data;
        }

        return new static($data);
    }

    /**
     * Create a new DTO instance.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->entity = $this->toDto($data);
    }

    /**
     * Convert the data to a DTO.
     *
     * @param $data
     * @return object
     */
    protected function toDto($data)
    {
        if (!is_array($data)) {
            if ($data instanceof Dtoable || $data instanceof Collection) {
                $data = $data->toDto();

                if ($data instanceof Collection) {
                   $data = ['items' => $data];
                }
            } elseif ($data instanceof Arrayable) {
                $data = $data->toArray();
            } elseif (is_object($data)) {
                $data = get_object_vars($data);
            } else {
                $data = (array) $data;
            }
        }

        if (!isset($data['data']) && !isset($data['items'])) {
            $data  = array('data' => $data);
        }

        return (object) $data;
    }

    /**
     * Get data from the DTO.
     *
     * @param string $key
     * @return object
     */
    public function get($key = null)
    {
        return is_null($key)
            ? $this->entity
            : $this->entity->{$key};
    }

    /**
     * Dynamically retrieve data on the DTO.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }
}
