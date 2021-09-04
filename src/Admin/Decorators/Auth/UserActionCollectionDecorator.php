<?php

namespace Laravelayers\Admin\Decorators\Auth;

use Laravelayers\Admin\Decorators\CollectionDecorator;

class UserActionCollectionDecorator extends CollectionDecorator
{
    /**
     * Initialize translation path.
     *
     * @return string
     */
    protected static function initTranslationPath()
    {
        return 'admin::auth/users/actions';
    }
    
    /**
     * Initialize the columns.
     *
     * @return array
     */
    protected function initColumns()
    {
        return [
            $this->first()->getKeyName() => [
                'hidden' => true,
            ],
            'action' => [
                'sort' => 'action',
                'checked' => true
            ],
            'allowed' => [
                'name' => static::transOfColumn('allowed'),
                'sort' => 'allowed',
                'desc' => 0
            ],
            'ip' => [
                'name' => static::transOfColumn('ip')
            ]
        ];
    }

    /**
     * Initialize the filter.
     *
     * @return array
     */
    protected function initFilter()
    {
        return [
            'is_role' => [
                'type' => 'radio.group',
                'value' => [
                    'yes' => [
                        'value' => 1,
                        'checked' => request()->get('is_role')
                    ],
                    'no' => [
                        'value' => 0,
                        'checked' => !request()->get('is_role')
                    ]
                ]
            ]
        ];
    }

    /**
     * Initialize the search options.
     *
     * @return array
     */
    protected function initSearch()
    {
        return ['action', 'ip'];
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
        return array_merge($this->getDefaultElements(), [
            'allowed' => [
                'type' => 'radio.group',
                'value' => [
                    'yes' => [
                        'value' => 1,
                        'checked' => true
                    ],
                    'no' => 0
                ],
                'group' => static::transOfElement('allowed_label'),
                'label' => ''
            ],
        ]);
    }

    /**
     * Initialize actions.
     *
     * @return array
     */
    protected function initActions()
    {
        return $this->getDefaultActions(['add', 'create',  'select', 'editMultiple', 'deleteMultiple']);
    }
}
