<?php

namespace Laravelayers\Admin\Decorators\Auth;

class RoleUserCollectionDecorator extends UserCollectionDecorator
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
     * Initialize the quick filter.
     *
     * @return array
     */
    protected function initQuickFilter()
    {
        return [];
    }

    /**
     * Initialize form elements.
     *
     * @return mixed
     */
    protected function initElements()
    {
        return [];
    }

    /**
     * Initialize actions.
     *
     * @return array
     */
    protected function initActions()
    {
        return $this->getDefaultActions(['add', 'select', 'deleteMultiple']);
    }
}
