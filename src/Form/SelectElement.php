<?php

namespace Laravelayers\Form;

use Illuminate\Support\Str;

trait SelectElement
{
    /**
     * Get the "select" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    public function getSelectElement($element)
    {
        if (is_numeric($element['value']) || is_string($element['value'])) {
            $element['value'] = [[
                'name' => $element['value'],
                'selected' => true
            ]];
        }

        foreach ($element['value'] as $key => $value) {
            if (is_object($value)) {
                break;
            }

            if (is_numeric($value) || is_string($value)) {
                $value = [
                    'value' => $value,
                    'text' => $key,
                ];
            }

            if (empty($value['name'])) {
                $value['name'] = $key ? Str::snake($key) : '';
            }

            $element['value'][$key] = $this->prepareSelectElement($value);
        }

        $element['value'] = is_array($element['value'])
            ? collect($element['value'])
            : $element['value'];

        return $element;
    }

    /**
     * Prepare the "select" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function prepareSelectElement($element)
    {
        $parameters = [
            'name', 'value', 'text', 'id', 'selected', 'class', 'hidden'
        ];

        $element = $this->getElementParameters($element, $parameters, 'getSelectElement');

        return $element;
    }

    /**
     * Get the text for the "select" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getSelectElementText($element)
    {
        if (!$element['text']) {
            $element['text'] = ucfirst($element['name']);
        }

        return $element;
    }

    /**
     * Get the "ID" attribute for the "select" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getSelectElementId($element)
    {
        return $this->getElementId($element);
    }

    /**
     * Get the "selected" attribute for the "select" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getSelectElementSelected($element)
    {
        return $this->getElementSelected($element);
    }

    /**
     * Get the value hidden or not for the "select" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getSelectElementHidden($element)
    {
        return $this->getElementHidden($element);
    }
}
