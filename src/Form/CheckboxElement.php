<?php

namespace Laravelayers\Form;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

trait CheckboxElement
{
    /**
     * Get the "radio" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    public function getRadioElement($element)
    {
        return $this->getCheckboxElement($element, 'radio');
    }

    /**
     * Get the "checkbox" element.
     *
     * @param array|\Traversable $element
     * @param string $type
     * @return array|\Traversable
     */
    public function getCheckboxElement($element, $type = 'checkbox')
    {

        if (is_numeric($element['value']) || is_string($element['value'])) {
            $element['value'] = [[
                'value' => $element['value'],
                'id' => $element['value'],
            ]];
        }

        $isMultipleValue = (count($element['value']) > 1 || !empty($element['multiple'])) ?: false;

        if (!$isMultipleValue && !is_object($element['value'])) {
            $key = key($element['value']);

            $element['value'][$key]['name'] = $element['name'];
            $element['value'][$key]['id'] = $element['value'][$key]['id'] ?? $key;

            if (!empty($element['text'])) {
                $element['value'][$key]['text'] = $element['text'];

                unset($element['text']);
            }
        }

        if ($type == 'checkbox' && !isset($element['multiple'])) {
            $element['multiple'] = $isMultipleValue;
        }

        foreach ($element['value'] as $key => $value) {
            if (is_object($value)) {
                break;
            }

            if (is_numeric($value) || is_string($value)) {
                $value = [
                    'value' => $key,
                    'text' => $value,
                ];
            }

            if (empty($value['name'])) {
                $value['name'] = Str::snake($key);
            }

            if (!isset($value['value'])) {
                $value['value'] = 1;
            }

            if (!isset($value['selected']) && !isset($value['checked'])) {
                $value['selected'] = ($element['attributes']->get('selected') || $element['attributes']->get('checked')) ?: false;
            }

            if (!empty($element['attributes']['disabled'])) {
                $value['disabled'] = !isset($value['disabled']) ? true : $value['disabled'];
            }

            $element['value'][$key] = $this->prepareCheckboxElement($value);
        }

        if ($element['attributes']->has('selected') || $element['attributes']->has('checked')) {
            $element['attributes']->forget('selected')->forget('checked');
        }

        $element['value'] = is_array($element['value'])
            ? collect($element['value'])
            : $element['value'];

        unset($element['attributes']['disabled']);

        return $element;
    }

    /**
     * Prepare the "checkbox" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function prepareCheckboxElement($element)
    {
        $parameters = [
            'name', 'value', 'text', 'id', 'selected', 'checked', 'class', 'hidden'
        ];

        $element = $this->getElementParameters($element, $parameters, 'getCheckboxElement');

        return $element;
    }

    /**
     * Get the "ID" attribute for the "checkbox" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getCheckboxElementId($element)
    {
        return $this->getElementId($element);
    }

    /**
     * Get the "selected" attribute for the "checkbox" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getCheckboxElementSelected($element)
    {
        return $this->getElementSelected($element);
    }

    /**
     * Get the "checked" attribute for the "checkbox" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getCheckboxElementChecked($element)
    {
        if (!empty($element['checked'])) {
            $element['selected'] = $element['checked'];

            $element = $this->getElementSelected($element);
        }

        unset($element['checked']);

        return $element;
    }

    /**
     * Get the value hidden or not for the "checkbox" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getCheckboxElementHidden($element)
    {
        return $this->getElementHidden($element);
    }
}
