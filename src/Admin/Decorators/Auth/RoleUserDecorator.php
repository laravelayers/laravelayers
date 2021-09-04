<?php

namespace Laravelayers\Admin\Decorators\Auth;

use Laravelayers\Contracts\Admin\Decorators\DataDecorator as DataDecoratorContract;

class RoleUserDecorator extends UserDecorator implements DataDecoratorContract
{
    /**
     * Initialize translation path.
     *
     * @return string
     */
    protected static function initTranslationPath()
    {
        return 'admin::auth/users';
    }

    /**
     * Initialize form elements.
     *
     * @return array
     */
    protected function initElements()
    {
        return [];
    }

    /**
     * Initialize the form element for actions.
     *
     * @return array
     */
    protected function initActionsElement()
    {
        return [];
    }

    /**
     * Initialize form elements for editing multiple collection elements.
     *
     * @return array
     */
    protected function initMultipleElements()
    {
        return array_keys($this->initElements());
    }

    /**
     * Initialize actions.
     *
     * @return array
     */
    protected function initActions()
    {
        return [];
    }
}
