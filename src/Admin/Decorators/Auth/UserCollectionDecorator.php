<?php

namespace Laravelayers\Admin\Decorators\Auth;

use Laravelayers\Admin\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Decorators\Decorator;

class UserCollectionDecorator extends CollectionDecorator
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
     * Initialize the columns.
     *
     * @return array
     */
    protected function initColumns()
    {
        return [
            'name' => [
                'sort' => 'name',
            ],
            'email' => [
                'sort' => 'email',
            ],
            'avatar' => []
        ];
    }

    /**
     * Initialize the filter.
     *
     * @return array
     */
    protected function initFilter()
    {
        return [];
    }

    /**
     * Initialize the search options.
     *
     * @return array
     */
    protected function initSearch()
    {
        return array_merge(parent::initSearch(), [
            'name' => ['selected' => true],
            'email' => []
        ]);
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
        return $this->getDefaultElements();
    }
}
