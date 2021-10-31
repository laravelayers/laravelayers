<?php

namespace Laravelayers\Form;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use InvalidArgumentException;

trait ButtonElement
{
    /**
     * Get the "button" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    public function getButtonElement($element)
    {
        if (is_numeric($element['value']) || is_string($element['value'])) {
            $element['value'] = $element['value'] ?: 'submit';

            $element['value'] = [$element['value'] => [
                'type' => $element['value'],
            ]];
        }

        foreach ($element['value'] as $key => $value) {
            if ($value instanceof Collection) {
                $prepared = true;

                break;
            }

            if (is_numeric($value) || is_string($value)) {
                $value = [
                    'value' => $key,
                    'text' => $value
                ];
            }

            if (empty($value['type'])) {
                $value['type'] = $key;
            }

            if (empty($value['name'])) {
                $value['name'] = $key;
            }

            $element['value'][$key] = $this->prepareButtonElement($value);
        }

        if (empty($prepared)) {
            $element['value'] = $this->prepareButtonElementValue($element['value']);
        }

        return $element;
    }

    /**
     * Prepare the "button" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function prepareButtonElement($element)
    {
        $parameters = [
            'type', 'view', 'name', 'id', 'value', 'text', 'link', 'class', 'icon', 'reverse', 'group', 'hidden'
        ];
        
        $element = $this->getElementParameters($element, $parameters, 'getButtonElement');

        return $element;
    }

    /**
     * Get the type for the "button" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getButtonElementType($element)
    {
        $element['type'] = strtolower($element['type']);

        return $element;
    }

    /**
     * Prepare the value for the "button" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function prepareButtonElementValue($element)
    {
        $prepared = [];

        foreach ($element as $key => $value) {
            $prepared[$value['group']][$key] = $value;
        }

        foreach ($prepared as $type => $actions) {
            $prepared[$type] = collect($actions);
        }

        return collect($prepared);
    }

    /**
     * Get the view for the "button" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getButtonElementView($element)
    {
        if (!$element['view']) {
            $element['view'] = $element['type'];
        }

        if (!view()->exists($element['view'])) {
            $view = "layouts.form.button.action.{$element['view']}";

            $element['view'] = view()->exists($view)
                ? $view
                : "form::layouts.button.action.{$element['view']}";
        }

        if (!view()->exists($element['view'])) {
            throw new InvalidArgumentException("View [{$element['view']}] not found.");
        }

        return $element;
    }

    /**
     * Get the "name" attribute for the "button" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getButtonElementName($element)
    {
        if (!strlen($element['name'])) {
            $element['name'] = $element['type'];
        }

        return $element;
    }

    /**
     * Get the text for the "button" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getButtonElementText($element)
    {
        if (!$element['text']) {
            $trans = "form.button.{$element['type']}";

            if (Lang::has($trans)) {
                $element['text'] = Lang::get($trans);
            } else {
                $trans = "form::form.button.{$element['type']}";

                $element['text'] = Lang::has($trans)
                    ? Lang::get($trans)
                    : ucfirst($element['name']);
            }
        }

        return $element;
    }

    /**
     * Get the "ID" attribute for the "button" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getButtonElementId($element)
    {
        return $this->getElementId($element);
    }

    /**
     * Get the group for the "button" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getButtonElementGroup($element)
    {
        if ($element['group'] === '') {
            $element['group'] = 1;
        } elseif (!$element['group']) {
            $element['group'] = 0;
        }

        return $element;
    }

    /**
     * Get the value hidden or not for the "button" element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getButtonElementHidden($element)
    {
        return $this->getElementHidden($element);
    }
}
