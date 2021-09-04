<?php

namespace Laravelayers\Admin\Decorators;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Laravelayers\Form\Decorators\FormDecorator;
use Laravelayers\Foundation\Decorators\Decorator;

trait Actions
{
    /**
     * Form actions.
     *
     * @var array
     */
    protected static $formActions = [];

    /**
     * Actions.
     *
     * @var array
     */
    protected static $actions = [];

    /**
     * Translation key format for actions.
     *
     * @var string
     */
    protected static $translationOfActions = 'admin::actions.%s';

    /**
     * Initialize the default form actions.
     *
     * @return array
     */
    abstract protected function initDefaultFormActions();

    /**
     * Initialize default actions.
     *
     * @return array
     */
    abstract protected function initDefaultActions();

    /**
     * Initialize an action checkbox.
     *
     * @return array
     */
    abstract protected function initActionCheckbox();

    /**
     * Initialize an action checkbox to readonly.
     *
     * @return array
     */
    abstract protected function initActionCheckboxToReadonly();

    /**
     * Checks if the current action is one of the specified.
     *
     * @param string|array $actions
     * @return bool
     */
    public function isAction($actions)
    {
        $actions = (array) $actions;

        if (!in_array($this->getCurrentAction('action'), $actions)) {
            foreach($actions as $action) {
                if ($action = $this->getFormActions()->get($action)) {
                    $types[] = $action->get('action');
                }
            }

            return in_array($this->getCurrentAction('action'), array_unique($types ?? []));
        }

        return true;
    }

    /**
     * Get the form data for the current action.
     *
     * @param string $key
     * @return array|string
     */
    protected function getCurrentFormAction($key = '')
    {
        return $this->getFormAction($this->getCurrentAction('action'), $key);
    }

    /**
     * Get the form data for the specified action.
     *
     * @param $action
     * @param string $key
     * @return array|string
     */
    protected function getFormAction($action, $key = '')
    {
        $action = $this->initFormActions()[$action] ?? [];

        if ($action) {
            $action = $this->prepareFormAction($action);
        }

        return $action[$key] ?? $action;
    }

    /**
     * Get the current action.
     *
     * @param string $key
     * @return array|mixed
     */
    protected function getCurrentAction($key = '')
    {
        $current = [
            'method' => $method = Request::route()->getActionMethod(),
            'action' => $method,
            '_action' => Request::query('action', Request::get('_action')),
            'multiple' => false,
        ];

        if (in_array($current['action'], ['store', 'index'])) {
            if ($action = Request::get('action', Request::old('preaction'))) {
                if (Request::query('action') != $action) {
                    $current = [
                        'method' => 'store',
                        'action' => $action = Str::camel($action),
                        '_action' => $current['_action'],
                        'multiple' => Str::endsWith($action, 'Multiple')
                    ];
                }
            }
        }
        return $key ? $current[$key] : $current;
    }

    /**
     * Get the actions of the form.
     *
     * @return \Laravelayers\Foundation\Decorators\CollectionDecorator
     */
    protected function getFormActions()
    {
        $id = spl_object_id($this);

        if (!isset(static::$formActions[$id])) {
            static::$formActions[$id] = $this->prepareFormActions(
                $this->initFormActions()
            );
        }

        return static::$formActions[$id];
    }

    /**
     * Initialize form actions.
     *
     * @return array
     */
    protected function initFormActions()
    {
        return $this->getDefaultFormActions();
    }

    /**
     * Get the default form actions.
     *
     * @param array $only
     * @return array
     */
    protected function getDefaultFormActions($only = [])
    {
        return $this->filterActions($only
            ? Arr::only($this->initDefaultFormActions(), $only)
            : $this->initDefaultFormActions()
        );
    }

    /**
     * Prepare the actions of the form.
     *
     * @param array $actions
     * @return \Laravelayers\Foundation\Decorators\CollectionDecorator
     */
    protected function prepareFormActions($actions)
    {
        return Decorator::make(collect($actions));
    }

    /**
     * Prepare an action of the form.
     *
     * @param array $data
     * @return array
     */
    protected function prepareFormAction($data)
    {
        $actions = $this->initDefaultFormActions();

        if (!empty($actions[$data['action']])) {
            $data = array_merge($actions[$data['action']], $data);
        }

        $data['link'] = $data['link'] ?? $data['action'];

        $data['hidden'] = false;

        foreach($data as $key => $value) {
            $method = Str::camel("getFormAction_{$key}");

            if (method_exists($this, $method)) {
                $data = $this->$method($data);
            }
        }

        unset($data['hidden']);

        return $data;
    }

    /**
     * Get the actions.
     *
     * @return \Laravelayers\Form\Decorators\FormDecorator
     */
    public function getActions()
    {
        $id = spl_object_id($this);

        if (!isset(static::$actions[$id])) {
            static::$actions[$id] = $this->prepareActions($this->initActions());
        }

        return static::$actions[$id];
    }

    /**
     * Initialize actions.
     *
     * @return array
     */
    protected function initActions()
    {
        return $this->getDefaultActions();
    }

    /**
     * Get actions by default.
     *
     * @param array|null $only
     * @return array
     */
    protected function getDefaultActions($only = null)
    {
        return !is_null($only)
            ? Arr::only($this->initDefaultActions(), $only)
            : $this->filterActions($this->initDefaultActions());
    }

    /**
     * Filter actions.
     *
     * @param array $actions
     * @return array
     */
    protected function filterActions($actions)
    {
        $controller = Request::route()->getController();

        foreach ((array) $actions as $key => $value) {
            if (!method_exists($controller, Str::camel($key))) {
                unset($actions[$key]);
            }
        }

        return $actions;
    }

    /**
     * Prepare the actions.
     *
     * @param array $data
     * @return \Laravelayers\Form\Decorators\FormDecorator
     */
    protected function prepareActions($data)
    {
        $actions = [];

        if ($this->getCurrentAction('multiple')) {
            if ($checkbox = $this->initActionCheckboxToReadonly()) {
                $actions['checkbox'] = $checkbox;
            }
        } else {
            foreach ($data as $key => $value) {
                $value['name'] = $value['name'] ?? $key;

                $data[$key] = $this->prepareAction($value);

                if (!$data[$key] || !empty($data[$key]['hidden'])) {
                    unset($data[$key]);
                }
            }

            $actions['action'] = [
                'type' => count($data) == 1 ? 'button' : 'button.dropdown',
                'value' => $data,
                'text' => static::transOfAction('text', [], true),
                'disabled' => !$data ? '' : null,
            ];

            if (count($data) == 1) {
                $actions['action']['value'] = [
                    key($data) => array_merge(
                        current($data),
                        ['class' => current($data)['class'] ?? 'expanded']
                    )
                ];
            }

            if ($checkbox = $this->initActionCheckbox()) {
                $actions['checkbox'] = $checkbox;
            }
        }

        return app(FormDecorator::class, [$actions ?? []]);
    }

    /**
     * Prepare an action.
     *
     * @param array $data
     * @return array
     */
    protected function prepareAction($data)
    {
        $this->getActionType($data);

        $actions = $this->initDefaultActions();

        if (!empty($actions[$data['type']])) {
            $data = array_merge($actions[$data['type']], $data);
        }

        foreach($data as $key => $value) {
            $method = Str::camel("getAction_{$key}");

            if (method_exists($this, $method)) {
                $data = $this->$method($data);
            }
        }

        return $this->getActionText($data);
    }

    /**
     * Get an action type.
     *
     * @param array $data
     * @return array
     */
    protected function getActionType(&$data)
    {
        if (!isset($data['type'])) {
            $data['type'] = 'link';
        }

        return $data;
    }

    /**
     * Get an action text.
     *
     * @param array $data
     * @return array
     */
    protected function getActionText(&$data)
    {
        if (!isset($data['text'])) {
            $data['text'] = static::transOfAction($data['name'] ?? $data['type'], [], true);
        } elseif (is_int($data['text'])) {
            $count = is_int($data['text'] ?? '') ? "({$data['text']})" : '';

            $data['text'] = static::transOfAction($data['name'] ?? $data['type'], compact('count'));
        }

        return $data;
    }

    /**
     * Prepare an action link.
     *
     * @param array $data
     * @return array
     */
    protected function prepareActionLink($data)
    {
        if (filter_var($data['link'], FILTER_VALIDATE_URL) === false) {
            if (strpos($data['link'], '.') === false) {
                $data['link'] = $this->getRouteByAction($data['link']);
            }

            $action = $data['link'];

            $data['link'] = route($data['link'], array_merge(
                Request::route()->parameters(),
                array_values($data['routeParameters'] ?? [])
            ));

            $data['link'] = explode('?', $data['link'])[0];

            if (!empty($data['httpQuery'])) {
                $data['link'] .= '?' . http_build_query($data['httpQuery']);
            }

            if (!isset($data['hidden']) || is_null($data['hidden'])) {
                $data['hidden'] = Gate::denies($action);
            }
        }

        unset($data['routeParameters']);
        unset($data['httpQuery']);

        return $data;
    }

    /**
     * Get route by action.
     *
     * @param string $action
     * @return string
     */
    protected function getRouteByAction($action)
    {
        return preg_replace(
            '/(\.)?'. Request::route()->getActionMethod() .'$/i',
            '$1' . $action,
            Request::route()->getName()
        );
    }

    /**
     * Get a translation for the given action key.
     *
     * @param string $key
     * @param array $replace
     * @param bool $empty
     * @param string $locale
     * @return string
     */
    public static function transOfAction($key = null, $replace = [], $empty = false, $locale = null)
    {
        return static::trans(static::$translationOfActions, $key, $replace, $locale, $empty);
    }
}
