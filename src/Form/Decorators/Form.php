<?php

namespace Laravelayers\Form\Decorators;

trait Form
{
    use Files, Images;

    /**
     * Form elements.
     *
     * @var array
     */
    protected static $elements = [];

    /**
     * Initialize form elements.
     *
     * @return mixed
     */
    abstract protected function initElements();

    /**
     * Get the form elements.
     *
     * @return \Laravelayers\Form\Decorators\FormDecorator
     */
    public function getElements()
    {
        $id = spl_object_id($this);

        if (!isset(static::$elements[$id])) {
            static::$elements[$id] = $this->prepareElements($this->initElements());
        }

        return static::$elements[$id];
    }

    /**
     * Prepare form elements.
     *
     * @param array|\Traversable $elements
     * @return \Laravelayers\Form\Decorators\FormDecorator
     */
    protected function prepareElements($elements)
    {
        return app(FormDecorator::class, [$elements])->getElements($this);
    }
}
