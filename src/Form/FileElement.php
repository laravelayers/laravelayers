<?php

namespace Laravelayers\Form;

use Illuminate\Support\Str;

trait FileElement
{
    /**
     * Get the "file" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    public function getFileElement($element)
    {
        if (is_numeric($element['value']) || is_string($element['value'])) {
            $element['value'] = $element['value']
                ? [['value' => $element['value'], 'id' => 0]]
                : [];

            if (!isset($element['multiple'])) {
                $element['multiple'] = false;
            }
        }

        foreach ($element['value'] as $key => $value) {
            if (is_object($value)) {
                break;
            }

            $id = !key($element['value']) ? $key + 1 : $key;

            if (is_numeric($value) || is_string($value)) {
                $value = [
                    'value' => $value,
                    'id' => $id,
                ];
            }

            if (empty($value['name'])) {
                $value['name'] = Str::snake($key);

                if (empty($value['id'])) {
                    $value['id'] = Str::snake($id);
                }
            }

            $element['value'][$key] = $this->prepareFileElement($value);
        }

        $element['value'] = is_array($element['value'])
            ? collect($element['value'])
            : $element['value'];

        return $element;
    }

    /**
     * Prepare the "checkbox" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function prepareFileElement($element)
    {
        $parameters = [
            'name', 'value', 'id', 'hidden'
        ];

        $element = $this->getElementParameters($element, $parameters, 'getFileElement');

        return $element;
    }

    /**
     * Get the "ID" attribute for the "checkbox" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getFileElementId($element)
    {
        return $this->getElementId($element);
    }

    /**
     * Get the value hidden or not for the "checkbox" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getFileElementHidden($element)
    {
        return $this->getElementHidden($element);
    }
}
