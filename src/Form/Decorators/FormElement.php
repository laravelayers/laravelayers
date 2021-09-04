<?php

namespace Laravelayers\Form\Decorators;

trait FormElement
{
    /**
     * Get the form element ID.
     *
     * @return int|string
     */
    public function getFormElementId()
    {
        return $this->getKeyName() ? $this->getKey() : $this->get('id');
    }

    /**
     * Get true if the form element is selected or false.
     *
     * @return bool
     */
    public function getIsFormElementSelected()
    {
        return $this->getIsSelected();
    }
}
