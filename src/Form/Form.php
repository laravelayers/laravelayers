<?php

namespace Laravelayers\Form;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laravelayers\Contracts\Form\Form as FormContract;

/**
 * The class for get a form elements from a collection of items.
 *
 * @package Laravelayers\Form
 */
class Form implements FormContract
{
    use ButtonElement;
    use CheckboxElement;
    use FileElement;
    use FormElement;
    use SelectElement;

    /**
     * Form elements.
     *
     * @var array
     */
    protected $elements = [];

    /**
     * Form element parameters.
     *
     * @var array
     */
    protected $parameters = [
        'type', 'view', 'name', 'id', 'value', 'text', 'multiple', 'label', 'class',
        'help', 'error', 'tooltip', 'icon', 'reverse', 'group', 'line', 'rules', 'hidden'
    ];

    /**
     * Get the form element from the item.
     *
     * @param string $key
     * @param array|\Traversable $value
     * @return array|\Traversable
     */
    public function getElement($key, $value)
    {
        return $this->getElements([$key => $value])->first();
    }

    /**
     * Get the form elements from the items.
     *
     * @param array|\Traversable $elements
     * @return \Illuminate\Support\Collection
     */
    public function getElements($elements)
    {
        return (new static)->prepareElements($elements);
    }

    /**
     * Prepare form elements.
     *
     * @param array|\Traversable $elements
     * @return \Illuminate\Support\Collection
     */
    protected function prepareElements($elements)
    {
        foreach($elements as $key => $element) {
            if (is_numeric($element) || is_string($element)) {
                $element = ['value' => $element];
            }

            if (empty($element['name'])) {
                $element['name'] = $key;
            }

            $this->elements[$key] = $this->prepareElement($element);
        }

        return collect($this->elements);
    }

    /**
     * Prepare form element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function prepareElement($element)
    {
        $element = $this->getElementParameters($element, $this->parameters);

        return $element;
    }

    /**
     * Get element parameters.
     *
     * @param array|\Traversable $element
     * @param array $parameters
     * @param string $methodPrefix
     * @param bool $toString
     * @return array|\Traversable
     */
    protected function getElementParameters($element, $parameters = [], $methodPrefix = 'getElement', $toString = false)
    {
        $element = $this->getElementAttributes($element, $parameters, $toString);

        foreach($parameters as $parameter) {
            if (!isset($element[$parameter])) {
                $element[$parameter] = null;
            }

            $method = Str::camel("{$methodPrefix}_{$parameter}");

            if (method_exists($this, $method)) {
                $element = $this->{$method}($element);
            }
        }

        return $element;
    }

    /**
     * Get element attributes.
     *
     * @param array|\Traversable $element
     * @param array $parameters
     * @param bool $toString
     * @return array|\Traversable
     */
    protected function getElementAttributes($element, $parameters, $toString = false)
    {
        $attributes = collect([]);
        $attributeStr = '';

        if (isset($element['attributes'])) {
            foreach ($element['attributes'] as $name => $value) {
                $attributes[$name] = $value;
            }

            unset($element['attributes']);
        }

        $element = Arr::except($element, array_diff($this->parameters, $parameters));

        foreach($element as $key => $value) {
            if (!in_array($key, $parameters)) {
                if (!is_null($value)) {
                    if ($toString) {
                        if ($attributeStr) {
                            $attributeStr .= ' ';
                        }

                        if (is_bool($value)) {
                            $attributeStr .= "{$key}";

                            if (!$value) {
                                $attributeStr .= '="false"';
                            }
                        } else {
                            $attributeStr .= "{$key}=\"{$value}\"";
                        }
                    }

                    $attributes[$key] = $value;
                }

                unset($element[$key]);
            }
        }

        $element['attributes'] = $toString ? $attributeStr : $attributes;

        if ($attributeStr) {
            $element['attributeArray'] = $attributes;
        }

        return $element;
    }

    /**
     * Get the type for the element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getElementType($element)
    {
        if (!$element['type']) {
            $element['type'] = 'hidden';
        }

        $element['type'] = strtolower($element['type']);

        return $element;
    }

    /**
     * Get the view for the element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getElementView($element)
    {
        if (!$element['view']) {
            $element['view'] = $element['type'];
        }

        if (!view()->exists($element['view'])) {
            $view = "layouts.form.{$element['view']}.element";

            $element['view'] = view()->exists($view)
                ? $view
                : "form::layouts.{$element['view']}.element";
        }

        if (!view()->exists($element['view'])) {
            throw new InvalidArgumentException("View [{$element['view']}] not found.");
        }

        return $element;
    }

    /**
     * Get the "name" attribute for the element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable|bool
     */
    protected function getElementName($element)
    {
        if (!strlen($element['name'])) {
            unset($element['name']);
        }

        return !$element['name'] ?: $element;
    }

    /**
     * Get the "ID" attribute for the element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getElementId($element)
    {
        if (!$element['id']) {
            $element['id'] = $element['name'];
        }

        return $element;
    }

    /**
     * Get the "multiple" attribute for the element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getElementMultiple($element)
    {
        $element['multiple'] = (boolean) $element['multiple'];

        return $element;
    }

    /**
     * Get the "selected" attribute for the element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getElementSelected($element)
    {
        $element['selected'] = (boolean) $element['selected'];

        return $element;
    }

    /**
     * Get the "value" attribute for the element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getElementValue($element)
    {
        $method = Str::camel('get_' . current(explode('.', $element['type'], 2)) . '_element');

        if (method_exists($this, $method)) {
            if (is_null($element['value']) || is_bool($element['value'])) {
                $element['value'] = (int) $element['value'];
            }

            $element = $this->{$method}($element);
        }

        return $element;
    }

    /**
     * Get the value hidden or not for the element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getElementHidden($element)
    {
        $element['hidden'] = (boolean) $element['hidden'];

        return $element;
    }
}
