<?php

namespace Laravelayers\Admin\Decorators\Auth;

use Illuminate\Support\Facades\Gate;
use Laravelayers\Admin\Decorators\DataDecorator as TraitDataDecorator;
use Laravelayers\Auth\Decorators\UserRoleActionDecorator as BaseUserRoleActionDecorator;
use Laravelayers\Contracts\Admin\Decorators\DataDecorator as DataDecoratorContract;

class UserRoleActionDecorator extends BaseUserRoleActionDecorator implements DataDecoratorContract
{
    use TraitDataDecorator;

    /**
     * The original value of the action.
     *
     * @var string
     */
    protected static $original_action;

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
     * Get the value of the data's primary key.
     *
     * @return string
     */
    public function getKey()
    {
        return static::$original_action[spl_object_id($this)] ?? $this->get($this->getActionColumn() ?: 0);
    }

    /**
     * Initialize form elements.
     *
     * @return array
     */
    protected function initElements()
    {
        return [
            parent::getKeyName() => [
                'type' => 'hidden',
            ],
            'action' => [
                'type' => 'text',
                'required' => '',
                'readonly' => Gate::denies('create') ?: null,
                'disabled' => null,
                'rules' => ['required', 'string', 'max:255', 'regex:/^[^\s\/]+$/i',
                    "unique:{$this->table},{$this->actionColumn},{$this->getKey()},{$this->actionColumn}"
                ]
            ],
            'allowed' => [
                'type' => 'radio.group',
                'value' => [
                    'yes' => [
                        'value' => 1,
                        'checked' => $this->getAllowed() || $this->isAction('create') ?: false
                    ],
                    'no' => [
                        'value' => 0,
                        'checked' => !$this->getAllowed() && !$this->isAction('create') ?: false
                    ]
                ],
                'label' => static::transOfElement('allowed_label'),
            ]
        ];
    }

    /**
     * Initialize the form element for actions.
     *
     * @return array
     */
    protected function initActionsElement()
    {
        return $this->getDefaultActions(['create']);
    }

    /**
     * Initialize form elements for editing multiple collection elements.
     *
     * @return array
     */
    protected function initMultipleElements()
    {
        return [
            'action' => 300
        ];
    }

    /**
     * Initialize actions.
     *
     * @return array
     */
    protected function initActions()
    {
        return [
            'create' => array_merge($this->initActionToCreate(), [
                'hidden' => $this->getCurrentAction('_action') ?: null,
            ]),
            'edit' => array_merge($this->initActionToEdit(), [
                'hidden' => $this->getCurrentAction('_action') ?: $this->initActionToEdit()['hidden']
            ])
        ];
    }

    /**
     * Set the role ID.
     *
     * @param string $value
     * @return string
     */
    public function setRole($value)
    {
        $this->put($this->getRoleColumn(), $value);

        return $value;
    }

    /**
     * Set the action value.
     *
     * @param string $value
     * @return string
     */
    public function setAction($value)
    {
        if (Gate::allows('create')) {
            $this->setRole(request()->role);

            static::$original_action[spl_object_id($this)] = $this->get($this->getActionColumn());

            if (strpos($value, '/') !== false) {
                $value = trim(str_replace('/', '.', preg_replace(
                    '/(^|\/)[0-9]+(\/?)/', '/', parse_url($value)['path']
                )), '.');
            }

            $value = preg_replace('/role(\.|$)/', '', $value);

            $this->put($this->getActionColumn(), $value);
        }

        return $value;
    }

    /**
     * Render the "allowed" value.
     *
     * @return mixed|string
     * @throws \Throwable
     */
    public function renderAllowed()
    {
        return $this->get($this->getAllowedColumn())
            ? $this->renderAsIcon('icon-check')
            : '';
    }

    /**
     * Set the "allowed" value.
     *
     * @param string $value
     * @param array $element
     * @return array
     */
    public function setAllowed($value, $element = [])
    {
        $this->put($this->getAllowedColumn(), $value ? 1 : 0);

        foreach($element['value'] ?? [] as $key => $params) {
            $element['value'][$key]['checked'] = boolval($params['value']) === boolval($value) ?: false;
        }

        return $element;
    }
}
