<?php

namespace Laravelayers\Admin\Decorators;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravelayers\Form\Decorators\FormElementDecorator;
use Laravelayers\Previous\PreviousUrl;

trait DataDecorator
{
    use Actions, Elements, Translator {
        Actions::prepareActions as prepareActionsFromTrait;
        Elements::prepareElements as prepareElementsFromTrait;
    }

    /**
     * Form multiple elements.
     *
     * @var array
     */
    protected static $multipleElements;

    /**
     * The prefix name for the form element.
     *
     * @var string
     */
    protected static $elementPrefixName = 'element';

    /**
     * The sort order key.
     *
     * @var string
     */
    protected $sortKey = 'sorting';

    /**
     * Initialize the form element for actions.
     *
     * @return array
     */
    abstract protected function initActionsElement();

    /**
     * Initialize form elements for editing multiple collection elements.
     *
     * @return array
     */
    abstract protected function initMultipleElements();

    /**
     * Get default form elements.
     *
     * @return array
     */
    public function getDefaultElements()
    {
        foreach($this->getOnlyOriginal() as $key => $value)
        {
            if (in_array($key, $this->getHiddenKeys())) {
                continue;
            }

            $_key = Str::snake($key);

            $elements[$key] = [
                'type' => strlen($value) <= 255 ? 'text' : 'textarea',
                'value' => $value,
                'label' => static::transOrEmpty(static::$translationOfElements, "{$_key}_label")
                    ?: str_replace('_', ' ', Str::ucfirst($_key))
            ];

            if (in_array($key, $this->getDateKeys()) && !in_array($key, $this->getTimestampKeys())) {
                $elements[$key]['type'] = 'datetime.js';
            }
        }

        return $elements ?? [];
    }

    /**
     * Prepare the actions.
     *
     * @param array $data
     * @return \Laravelayers\Form\Decorators\FormDecorator
     */
    protected function prepareActions($data)
    {
        return $this->prepareActionsFromTrait($data)
            ->setElementsPrefix($this->getKey())
            ->getElements();
    }

    /**
     * Prepare form elements.
     *
     * @param array $elements
     * @return \Laravelayers\Form\Decorators\FormDecorator
     */
    protected function prepareElements($elements)
    {
        $this->getIdElement($elements);
        $this->getTimestampsElements($elements);
        $this->getActionsElement($elements);

        $this->prepareElementsByActions($elements);

        $this->getSubmittersElement($elements);

        $elements = $this->prepareElementsFromTrait($elements);

        if (static::$elementPrefixName) {
            $elements->setElementsPrefix($this->getKey(), static::$elementPrefixName);
        }

        if ($this->getCurrentAction('action') == 'editMultiple' && request()->isMethod('POST')) {
            return $elements->getElements();
        }

        return $elements->getElements($this);
    }

    /**
     * Prepare the form elements by actions.
     *
     * @param array|\Traversable $elements
     * @return array
     */
    protected function prepareElementsByActions(&$elements)
    {
        if ($this->getCurrentAction('multiple')) {
            foreach($elements as $key => $value) {
                unset($elements[$key]);

                if ($value instanceof FormElementDecorator) {
                    $value = $value->get();
                }

                $key = is_int($key) && !empty($value['name']) ? $value['name'] : $key;

                $elements[$key] = $value;
            }

            foreach ($this->getMultipleElements() as $key => $value) {
                $multiple[$key] = !empty($elements[$key])
                    ? array_merge($elements[$key], $value)
                    : $value;
            }

            $elements = $multiple ?? [];
        }

        return $elements;
    }

    /**
     * Get initialize form elements for editing multiple resources.
     *
     * @param string|array $actions
     * @param string $key
     * @return array|null
     */
    protected function getMultipleElements($key = '', $actions = '')
    {
        if (!$this->isAction($actions ?: ['editMultiple', 'updateMultiple'])) {
            return $key ? null : [];
        }

        $group = $this->getKey();

        if (!isset(static::$multipleElements[$group])) {
            static::$multipleElements[$group] = $this->prepareMultipleElements($this->initMultipleElements());
        }

        return $key
            ? (static::$multipleElements[$group][$key] ?? null)
            : static::$multipleElements[$group];
    }

    /**
     * Prepare form elements for editing multiple resources.
     *
     * @param array $elements
     * @return array
     */
    protected function prepareMultipleElements($elements)
    {
        foreach($elements as $key => $value) {
            unset($elements[$key]);

            if ($value instanceof FormElementDecorator) {
                $value = $value->get();
            }

            if (!is_iterable($value)) {
                if (is_string($key)) {
                    $value = $value && (is_string($value) || is_int($value)) ? ['width' => $value] : [];
                } else {
                    $key = $value;
                    $value = [];
                }
            } else {
                $key = is_int($key) && !empty($value['name']) ? $value['name'] : $key;
            }

            $elements[$key] = $value;
        }

        return $elements;
    }

    /**
     * Get the form element for the ID.
     *
     * @param array $elements
     * @return array
     */
    protected function getIdElement(&$elements)
    {
        $idName = $this->getKeyName();
        $id = $elements[$idName] ?? [];

        if ($value = $this->getKey()) {
            $id = array_merge([
                'type' => 'text',
                'name' => $idName,
                'value' => $value,
                'label' => static::transOfElement('id_label'),
                'required' => '',
                'disabled' => true
            ], $id);

            unset($elements[$idName]);
        }

        if (!empty($id['value'])) {
            $elements = array_merge([$idName => $id], $elements);
        }

        return $elements;
    }

    /**
     * Get form elements for timestamps.
     *
     * @param array $elements
     * @return array
     */
    protected function getTimestampsElements(&$elements)
    {
        if (!empty($createdAt = $this->getTimestampKeys()['created_at'] ?? '')) {
            $created = $elements[$createdAt] ?? [];

            if ($value = $this->{$createdAt}) {
                $created = array_merge([
                    'type' => 'text',
                    'name' => $createdAt,
                    'value' => $value,
                    'label' => static::transOfElement('created_at_label'),
                    'line' => 'created',
                    'required' => '',
                    'disabled' => '',
                    'hidden' => $this->isAction('create')
                ], $created);
            }

            unset($elements[$createdAt]);

            if (!empty($created['value'])) {
                $elements[$createdAt] = $created;
            }
        }

        if (!empty($updatedAt = $this->getTimestampKeys()['updated_at'] ?? '')) {
            $updated = $elements[$updatedAt] ?? [];

            if ($value = $this->{$updatedAt}) {
                $updated = array_merge([
                    'type' => 'text',
                    'name' => $updatedAt,
                    'value' => $value,
                    'label' => static::transOfElement('updated_at_label'),
                    'line' => 'created',
                    'required' => '',
                    'disabled' => '',
                    'hidden' => $this->isAction('create')
                ], $updated);
            }

            unset($elements[$updatedAt]);

            if (!empty($updated['value'])) {
                $elements[$updatedAt] = $updated;
            }
        }

        return $elements;
    }

    /**
     * Get the form element for actions.
     *
     * @param array $elements
     * @return array
     */
    protected function getActionsElement(&$elements)
    {
        $actions = $elements['actions'] ?? [];

        if ($value = $this->prepareActionsElement($this->initActionsElement())) {
            $actions = array_merge([
                'type' => 'button.dropdown',
                'value' => $value,
                'text' => static::transOfAction("text"),
                'label' => '',
                'class' => 'expanded hollow'
            ], $actions);
        }

        unset($elements['actions']);

        if (!empty($actions['value'])) {
            $elements = array_merge(['actions' => $actions], $elements);
        }

        return $elements;
    }

    /**
     * Prepare the form element for actions.
     *
     * @param string|array $actions
     * @param array $data
     * @return array
     */
    protected function prepareActionsElement($data, $actions = '')
    {
        if (!$this->isAction($actions ?: 'edit')) {
            $data = [];
        }

        foreach ($data as $key => $value) {
            $value['name'] = $value['name'] ?? $key;

            $this->getActionType($value);
            $this->getActionText($value);
            $this->getActionElementLink($value);

            if (empty($value['hidden'])) {
                $data[$key] = Arr::except($value, 'key');
            } else {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Get the form element link for action.
     *
     * @param array $data
     * @return array
     */
    protected function getActionElementLink(&$data)
    {
        if (!empty($data['link'])) {
            $data = $this->getActionLink($data);

            $data['link'] = PreviousUrl::addHash($data['link']);
        }

        return $data;
    }

    /**
     * Get the link of the form action.
     *
     * @param array $data
     * @return array
     */
    protected function getFormActionLink($data)
    {
        $data['httpQuery'] = array_merge(PreviousUrl::getQuery(), $data['httpQuery'] ?? []);

        return $this->getActionLink($data);
    }

    /**
     * Initialize default form actions.
     *
     * @return array
     */
    protected function initDefaultFormActions()
    {
        return [
            'create' => $this->initFormActionToStore(),
            'store' => $this->initFormActionToStore(),
            'edit' => $this->initFormActionToUpdate(),
            'update' => $this->initFormActionToUpdate(),
        ];
    }

    /**
     * Initialize the form action to store.
     *
     * @return array
     */
    protected function initFormActionToStore()
    {
        return [
            'method' => 'POST',
            'methodField' => 'POST',
            'action' => 'store',
            'link' => 'store'
        ];
    }

    /**
     * Initialize the form action to update.
     *
     * @return array
     */
    protected function initFormActionToUpdate()
    {
        return [
            'method' => 'POST',
            'methodField' => 'PUT',
            'action' => 'update'
        ];
    }

    /**
     * Get an action link.
     *
     * @param array $data
     * @return array
     */
    protected function getActionLink(&$data)
    {
        if ($this->getKey()) {
            $data['routeParameters'] = array_merge(
                [$this->getKey()],
                $data['routeParameters'] ?? []
            );
        }

        $data['httpQuery'] = array_merge(PreviousUrl::query(), $data['httpQuery'] ?? []);

        return $data = $this->prepareActionLink($data);
    }

    /**
     * Initialize actions by default.
     *
     * @return array
     */
    protected function initDefaultActions()
    {
        return [
            'show' => $this->initActionToShow(),
            'create' => $this->initActionToCreate(),
            'edit' => $this->initActionToEdit()
        ];
    }

    /**
     * Initialize the action to show.
     *
     * @return array
     */
    protected function initActionToShow()
    {
        return [
            'type' => 'show',
            'link' => 'show',
            'text' => static::transOfAction( 'show'),
            'group' => 0,
            'hidden' => Gate::denies('view')
        ];
    }

    /**
     * Initialize the action to create.
     *
     * @return array
     */
    protected function initActionToCreate()
    {
        return [
            'type' => 'create',
            'link' => 'create',
            'httpQuery' => ['id' => $this->getKey()],
            'text' => static::transOfAction('copy'),
            'view' => 'copy',
            'hidden' => Gate::denies('create')
        ];
    }

    /**
     * Initialize the action to edit.
     *
     * @return array
     */
    protected function initActionToEdit()
    {
        return [
            'type' => 'edit',
            'link' => 'edit',
            'text' => static::transOfAction('edit'),
            'hidden' => Gate::denies('update')
        ];
    }

    /**
     * Initialize an action checkbox.
     *
     * @return array
     */
    protected function initActionCheckbox()
    {
        return [
            'type' => 'checkbox',
            'name' => 'id',
            'value' => $this->getKey()
        ];
    }

    /**
     * Initialize an action checkbox to readonly.
     *
     * @return array
     */
    protected function initActionCheckboxToReadonly()
    {
        return array_merge($this->initActionCheckbox(), [
            'type' => $this->initActionCheckbox()['type'] . '.readonly',
            'value' => [[
                'id' => $this->getKey(),
                'value' => $this->getKey(),
                'selected' => true
            ]],
            'multiple' => false
        ]);
    }

    /**
     * Call the method of rendering the decorator item by key if the method methodExists.
     *
     * @param string $key
     * @param bool $text
     * @return string
     */
    public function getRenderer($key, $text = false)
    {
        if (!$text && !is_null($this->getMultipleElements($key)) && $this->getElements()->has($key)) {
            $element = $this->getElements()->{$key};

            if (is_null($element->getAttributes('disabled'))) {
                $element->getElement([
                    'label' => '',
                    'help' => '',
                    'error' => '',
                    'tooltip' => $element->getError() ?: $element->get('help')
                ]);

                if ($element->getAttributes('width')) {
                    $element->addAttributes([
                        'style' => $element->getAttributes('style') . " min-width: {$element->getAttributes('width')}px;",
                    ]);
                }

                return $element->render();
            }
        }

        return parent::getRenderer($key, $text);
    }

    /**
     * Get the primary key for the data.
     *
     * @return string|null
     */
    public function getKeyName()
    {
        if ($this->isEmpty()) {
            return null;
        }

        return parent::getKeyName() ?: key($this->initElements());
    }

    /**
     * Get the sort order key.
     *
     * @return string
     */
    public function getSortKey()
    {
        return $this->sortKey;
    }

    /**
     * Search and replace values according to form elements.
     *
     * @param string $pattern
     * @param string $replacement
     * @param mixed $only
     * @return $this
     */
    public function replaceElements($pattern, $replacement, $only = '')
    {
        $function = preg_match('/^\/([^\/]+)\/([^\/]*)$/', $pattern)
            ? 'preg_replace' : 'str_replace';

        $elements = $this->getElements()->only($only ?: null);

        foreach($elements as $elementKey => $element) {
            if (is_string($this->{$elementKey}) && is_null($element->getAttributes('disabled')) ) {
                $value = $function(
                    $pattern, $replacement, $this->{$elementKey}
                );

                $setter = Str::camel("set_{$elementKey}");

                if (method_exists($this, $setter)) {
                    $this->{$setter}($value);
                } else {
                    $this->put($elementKey, $value);
                }
            }
        }

        return $this;
    }
}
