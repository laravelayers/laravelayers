<?php

namespace Laravelayers\Foundation\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Laravelayers\Contracts\Foundation\Dto\Dtoable;

/**
 * Additional method for converting Eloquent models to a DTO format.
 *
 * @see \Laravelayers\Foundation\Dto\Dto
 */
trait ModelToDto
{
    /**
     * Convert the Model instance in the DTO format.
     *
     * @see \Laravelayers\Foundation\Dto\Dto
     * @return array
     */
    public function toDto()
    {
        return array_merge(
            $this->valuesToDto(),
            $this->attributesToDto(),
            $this->relationsToDto()
        );
    }

    /**
     * Convert the model's attributes to a DTO format.
     *
     * @return array
     */
    protected function attributesToDto()
    {
        $attributes = $this->setHidden([])->attributesToArray();

        foreach($this->getDates() as $key) {
            if ($attribute = $this->{$key}) {
                $attributes[$key] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * Get the model's relationships in DTO format.
     *
     * @return array
     */
    protected function relationsToDto()
    {
        $attributes = [];

        foreach ($this->getArrayableRelations() as $key => $value) {
            // If the values implements the Arrayable interface we can just call this
            // toArray method on the instances which will convert both models and
            // collections to their proper array form and we'll set the values.
            if ($value instanceof Arrayable) {
                if ($value instanceof Collection) {
                    $relation = $value->toDto();
                } elseif ($value instanceof Dtoable) {
                    $relation = $value->toDto();
                } else {
                    $relation = $value->toArray();
                }
            }

            // If the value is null, we will convert the value to an empty array.
            elseif (is_null($value)) {
                $relation = [];
            }

            // If the relationships snake-casing is enabled, we will snake case this
            // key so that the relation attribute is snake cased in this returned
            // array to the developers, making this consistent with attributes.
            if (static::$snakeAttributes) {
                //$key = Str::snake($key);
            }

            // If the relation value has been set, we will set it on this attributes
            // list for returning. If it was not arrayable or null, we'll not set
            // the value on the array because it is some type of invalid value.
            if (isset($relation) || is_null($value)) {
                $attributes[$key] = $relation;
            }

            unset($relation);
        }

        return ['relations' => $attributes];
    }

    /**
     * Extract and cache all the mutated attributes of a class.
     *
     * @param  string  $class
     * @return void
     */
    public static function cacheMutatedAttributes($class)
    {
        $snakeAttributes = static::$snakeAttributes;
        static::$snakeAttributes = false;

        parent::cacheMutatedAttributes($class);

        static::$snakeAttributes = $snakeAttributes;
    }

    /**
     * Get additional values in DTO format form.
     *
     * @return array
     */
    protected function valuesToDto()
    {
        return [
            'table' => $this->getTable(),
            'primaryKey' => $this->getKeyName(),
            'originalKeys' => $this->getOriginalKeys(),
            'dateKeys' => $this->getDates(),
            'timestampKeys' => $this->timestampKeysToDto(),
            'timestamps' => $this->timestamps,
            'hiddenKeys' => $this->getHidden(),
        ];
    }

    /**
     * Get the original keys.
     *
     * @return array
     */
    protected function getOriginalKeys()
    {
        return array_keys($this->getOriginal());
    }


    /**
     * Get timestamp keys.
     *
     * @return array
     */
    protected function timestampKeysToDto()
    {
        $timestamps = [];

        if ($this->getDates()) {
            if (static::CREATED_AT) {
                $timestamps['created_at'] = static::CREATED_AT;
            }

            if (static::UPDATED_AT) {
                $timestamps['updated_at'] = static::UPDATED_AT;
            }
        }

        return $timestamps;
    }
}
