<?php

namespace Laravelayers\Contracts\Form\Decorators;

/**
 * @see \Laravelayers\Form\Decorators\FormElement
 */
interface FormElement
{
    /**
     * Get the form element ID.
     *
     * @return int|string
     */
    public function getFormElementId();

    /**
     * Get the form element text.
     *
     * @return string
     */
    public function getFormElementText();

    /**
     * Get true if the form element is selected or false.
     *
     * @return bool
     */
    public function getIsFormElementSelected();

    /**
     * Get the value of the HTML attribute of the class of the form element.
     *
     * @return string
     */
    public function getFormElementClass();
}
