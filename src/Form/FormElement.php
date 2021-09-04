<?php

namespace Laravelayers\Form;

trait FormElement
{
    /**
     * Get the form element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    public function getFormElement($element)
    {
        if (!$element['value']) {
            $element['value'] = [];
        }

        $element['value'] = $this->prepareFormElement($element['value']);

        $element['attributes'] = $element['attributes']->merge($element['value']['attributes']->all());

        unset($element['value']['attributes']);

        return $element;
    }

    /**
     * Prepare form element.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function prepareFormElement($element)
    {
        $parameters = [
            'method', 'methodField', 'link', 'action',
        ];

        $element = $this->getElementParameters($element, $parameters, 'getFormElement');

        return $element;
    }

    /**
     * Get the form element method.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getFormElementMethod($element)
    {
        if (!$element['method']) {
            $element['method'] = !empty($element['methodField']) ? 'POST' : '';
        }

        if (strcasecmp($element['method'], 'POST') !== 0) {
            $element['method'] = 'GET';
        }

        return $element;
    }

    /**
     * Get the form element method field.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getFormElementMethodField($element)
    {
        if (!$element['methodField']) {
            $element['methodField'] = $element['method'] == 'POST' ? 'POST' : '';
        }

        return $element;
    }

    /**
     * Get the form element link.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getFormElementLink($element)
    {
        if ($element['link']) {
            $element['action'] = $element['link'];
        }

        unset($element['link']);

        return $element;
    }


    /**
     * Get the form element action.
     *
     * @param array|\Traversable $element
     * @return array|\Traversable
     */
    protected function getFormElementAction($element)
    {
        if (!$element['action']) {
            $element['action'] = request()->url();
        }

        return $element;
    }
}
