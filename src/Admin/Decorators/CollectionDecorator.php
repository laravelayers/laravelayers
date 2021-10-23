<?php

namespace Laravelayers\Admin\Decorators;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravelayers\Contracts\Admin\Decorators\Actions as ActionsContract;
use Laravelayers\Contracts\Admin\Decorators\Elements as ElementsContract;
use Laravelayers\Contracts\Admin\Decorators\Translator as TranslatorContract;
use Laravelayers\Form\Decorators\FormDecorator;
use Laravelayers\Foundation\Services\Service;
use Laravelayers\Previous\PreviousUrl;
use Laravelayers\Foundation\Decorators\CollectionDecorator as BaseCollectionDecorator;
use Laravelayers\Foundation\Decorators\Decorator;

abstract class CollectionDecorator extends BaseCollectionDecorator implements ActionsContract, ElementsContract, TranslatorContract
{
    use Actions, Elements, Translator {
        Actions::prepareActions as prepareActionsFromTrait;
        Elements::prepareElements as prepareElementsFromTrait;
    }

    /**
     * Columns.
     *
     * @var null|\Laravelayers\Foundation\Decorators\CollectionDecorator
     */
    protected static $columns;

    /**
     * The filter form elements.
     *
     * @var null|\Laravelayers\Form\Decorators\FormDecorator
     */
    protected static $filter;

    /**
     * The table view.
     *
     * @var string
     */
    protected static $tableView = 'admin::layouts.table.table';

    /**
     * Translation key format for columns.
     *
     * @var string
     */
    protected static $translationOfColumns = 'admin::columns.%s';

    /**
     * Initialize the columns.
     *
     * @return array
     */
    abstract protected function initColumns();

    /**
     * Initialize the filter.
     *
     * @return array
     */
    abstract protected function initFilter();

    /**
     * Initialize the quick filter.
     *
     * @return array
     */
    abstract protected function initQuickFilter();

    /**
     * Render the columns.
     *
     * @param string $string
     * @return \Illuminate\Support\HtmlString
     * @throws \Throwable
     */
    public function render($string = '')
    {
        $elements = $this->getElements();

        if ($this->isEmpty()) {
            $actions = $this->getActions()->get('action');

            if ($actions && $actions->getValue()->isNotEmpty()) {
                $string .= $actions->render();
            }

            if (Request::has(Service::getSearchName())) {
                $elements->setWarning(static::trans('admin::alerts.not_found'));
            }
        }

        if ($this->isNotEmpty()) {
            $string .= $elements->renderElements();

            if ($this->getData() instanceof AbstractPaginator) {
                $string .= $this->getData()->summary();
            }
        }

        $string .= view(static::$tableView, ['items' => $this]);

        if ($this->getData() instanceof AbstractPaginator && !$this->getCurrentAction('multiple')) {
            $string .= $this->getData()->render();
        }

        return $elements->render($string);
    }

    /**
     * Get the columns.
     *
     * @return \Laravelayers\Foundation\Decorators\CollectionDecorator
     */
    public function getColumns()
    {
        if (!static::$columns) {
            $columns = $this->initColumns();

            if (!static::$columns) {
                static::$columns = $this->prepareColumns($columns);
            }
        }

        return static::$columns;
    }

    /**
     * Add a column.
     *
     * @param string $name
     * @return $this
     */
    public function addColumn($name)
    {
        if (!static::$columns) {
            static::$columns = $this->prepareColumns([$name => []]);
        } else {
            $column = static::$columns->get($name) ?: $this->prepareColumn(['column' => $name]);

            static::$columns->put($name, $column);
        }

        static::$columns->activeColumn = $name;

        return $this;
    }

    /**
     * Get the default columns.
     *
     * @return array
     */
    public function getDefaultColumns()
    {
        if ($first = $this->first()) {
            foreach($first->getDefaultElements() as $key => $column) {
                $_key = Str::snake($key);

                $columns[$key] = [
                    'name' => static::transOfColumn("{$_key}", [], true)
                        ?: str_replace('_', ' ', Str::ucfirst($_key)),
                    'html' => true
                ];
            }
        }

        return $columns ?? [];
    }

    /**
     * Prepare the columns.
     *
     * @param array $columns
     * @return \Laravelayers\Foundation\Decorators\CollectionDecorator
     */
    protected function prepareColumns($columns)
    {
        $this->getIdColumn($columns);

        foreach ($columns as $column => $value) {
            if ($value instanceof $this) {
                unset($columns[$column]);

                continue;
            }

            if (!is_iterable($value)) {
                if (is_int($column)) {
                    unset($columns[$column]);

                    $column = $value;
                    $value = [];
                } elseif (is_string($value) && $value) {
                    $value = ['name' => $value];
                } else {
                    $value = [];
                }
            }

            if (isset($value['hidden'])) {
                if (!empty($value['hidden'])) {
                    unset($columns[$column]);

                    continue;
                }

                unset($value['hidden']);
            }

            $value['column'] = $value['column'] ?? $column;

            $value = $this->prepareColumn($value);

            $columns[$column] = $value;
        }

        return Decorator::make(
            collect($columns)
        );
    }

    /**
     * Get the column for the ID.
     *
     * @param array $columns
     * @return array
     */
    protected function getIdColumn(&$columns)
    {
        if ($this->first()) {
            if ($idName = $this->first()->getKeyName()) {
                $id = array_merge([
                    'name' => static::transOfColumn('id'),
                    'sort' => 'id',
                    'checked' => !array_filter($columns, function($value){
                        return !empty($value['checked']);
                    })
                ], $columns[$idName] ?? []);

                unset($columns[$idName]);

                $columns = array_merge([$idName => $id], $columns);
            }
        }

        return $columns;
    }

    /**
     * Prepare a column.
     *
     * @param array $data
     * @return array
     */
    protected function prepareColumn($data)
    {
        $this->getColumnName($data);
        $this->getColumnTooltip($data);
        $this->getColumnText($data);
        $this->getColumnSorting($data);
        $this->getColumnLength($data);
        $this->getColumnAttributes($data);

        return $data;
    }

    /**
     * Get the column name.
     *
     * @param array $data
     * @return array
     */
    protected function getColumnName(&$data)
    {
        if (!isset($data['name'])) {
            $data['name'] = static::transOfColumn($data['column']);
        }

        return $data;
    }

    /**
     * Add a column name.
     *
     * @param string $name
     * @return $this
     */
    public function addColumnName($name)
    {
        if (static::$columns && static::$columns->activeColumn) {
            $column = static::$columns->get(static::$columns->activeColumn)->put('name', $name);

            static::$columns->put($column->get('column'), $this->getColumnName($column));
        }

        return $this;
    }

    /**
     * Get the column tooltip.
     *
     * @param array $data
     * @return array
     */
    protected function getColumnTooltip(&$data)
    {
        if (empty($data['tooltip'])) {
            $data['tooltip'] = static::transOfColumn("{$data['column']}_tooltip", [], true);
        }

        if ($data['tooltip']) {
            $data['name'] = view('admin::layouts.table.tooltip', [
                'slot' => $data['name'],
                'title' => $data['tooltip']
            ]);
        }

        return $data;
    }

    /**
     * Add a column tooltip.
     *
     * @param string $tooltip
     * @return $this
     */
    public function addColumnTooltip($tooltip)
    {
        if (static::$columns && static::$columns->activeColumn) {
            $column = static::$columns->get(static::$columns->activeColumn)
                ->put('tooltip', $tooltip);

            if (!is_string($column->get('name')) || !is_numeric($column->get('name'))) {
                $column->forget('name');
            }

            $this->getColumnName($column);
            $this->getColumnTooltip($column);

            static::$columns->put($column->get('column'), $column);
        }

        return $this;
    }

    /**
     * Get the column text.
     *
     * @param array $data
     * @return array
     */
    protected function getColumnText(&$data)
    {
        if (empty($data['text'])) {
            $data['text'] = static::transOfColumn("{$data['column']}_text", [], true);
        }

        return $data;
    }

    /**
     * Add a column text.
     *
     * @param string $text
     * @return $this
     */
    public function addColumnText($text)
    {
        if (static::$columns && static::$columns->activeColumn) {
            $column = static::$columns->get(static::$columns->activeColumn)->put('text', $text);

            static::$columns->put($column->get('column'), $this->getColumnText($column));
        }

        return $this;
    }

    /**
     * Get the column sorting.
     *
     * @param array $data
     * @return array
     */
    protected function getColumnSorting(&$data)
    {
        $sorting = Request::route()->getController()->getSorting() ?: [];

        if (!isset($data['desc'])) {
            $data['desc'] = 1;
        }

        if (!isset($data['sort'])) {
            $data['sort'] = '';
        }

        if (empty($data['checked']) || $sorting) {
            $data['checked'] = false;
        }

        if ($data['sort']) {
            if ($data['sort'] == key($sorting)) {
                $data['desc'] = current($sorting) == 'desc' ? 1 : 0;
                $data['checked'] = true;
            }

            if (!$this->getCurrentAction('multiple') && is_null($data['link'] ?? null)) {
                $data['link'] = url()->current() . '?' . http_build_query(
                        array_merge(Request::query(), [
                            Service::getSortingName() => $data['sort'],
                            Service::getSortingDescName() => $data['desc'] ? 0 : 1
                        ])
                    );
            }
        }

        return $data;
    }

    /**
     * Add a column sorting.
     *
     * @param string $sort
     * @param int $desc
     * @param bool $checked
     * @param bool $link
     * @return $this
     */
    public function addColumnSorting($sort, $desc = 0, $checked = false, $link = null)
    {
        if (static::$columns && static::$columns->activeColumn) {
            $column = static::$columns->get(static::$columns->activeColumn)
                ->put('sort', $sort)
                ->put('desc', $desc ? 1 : 0)
                ->put('checked', (bool)$checked)
                ->put('link', $link);

            static::$columns->put($column->get('column'), $this->getColumnSorting($column));
        }

        return $this;
    }

    /**
     * Get the minimum length of the column text to crop.
     *
     * @param array $data
     * @return array
     */
    protected function getColumnLength(&$data)
    {
        if (!isset($data['length'])) {
            $data['length'] = isset($data['html']) ? '256' : 0;
        }

        // Show all text in html format.
        $data['html'] = (!isset($data['html']) || $data['html']) ? true : false;

        return $data;
    }

    /**
     * Add the minimum length of the column text to trim.
     *
     * @param int|string $length
     * @param bool $html
     * @return $this
     */
    public function addColumnLength($length, $html = true)
    {
        if (static::$columns && static::$columns->activeColumn) {
            $column = static::$columns->get(static::$columns->activeColumn)
                ->put('length', $length)
                ->put('html', $html);

            static::$columns->put($column->get('column'), $this->getColumnLength($column));
        }

        return $this;
    }

    /**
     * Get the column attributes.
     *
     * @param array $data
     * @return array
     */
    protected function getColumnAttributes(&$data)
    {
        $parameters = ['column', 'name', 'sort', 'checked', 'desc', 'link', 'length', 'html', 'tooltip', 'text', 'attributes'];

        $data['attributes'] = $data['attributes'] ?? '';

        foreach($data as $key => $value) {
            if (!in_array($key, $parameters)) {
                if (!is_null($value)) {
                    $data['attributes'] .= " {$key}";

                    if (is_bool($value)) {
                        if (!$value) {
                            $data['attributes'] .= '="false"';
                        }
                    } else {
                        $data['attributes'] .= "=\"{$value}\"";
                    }
                }

                unset($data[$key]);
            }
        }

        $data['attributes'] = trim($data['attributes']);

        return $data;
    }

    /**
     * Add column attributes.
     *
     * @param array $attributes
     * @return $this
     */
    public function addColumnAttributes($attributes)
    {
        if (static::$columns && static::$columns->activeColumn) {
            $column = static::$columns->get(static::$columns->activeColumn);

            foreach ((array)$attributes as $key => $value) {
                $column->put($key, $value);
            }

            static::$columns->put($column->get('column'), $this->getColumnAttributes($column));
        }

        return $this;
    }

    /**
     * Add a "hidden" column parameter.
     *
     * @param bool $hidden
     * @return $this
     */
    public function addColumnHidden($hidden = true)
    {
        if (static::$columns && static::$columns->activeColumn && $hidden) {
            static::$columns->forget(static::$columns->activeColumn);

            static::$columns->activeColumn = null;
        }

        return $this;
    }

    /**
     * Get true if the rows is sortable or false.
     *
     * @return bool
     */
    public function getIsSortableRows()
    {
        return $this->first()
            && !is_null($this->first()->{$this->first()->getSortKey()})
            && $this->isAction('editMultiple');
    }

    /**
     * Set the sort order of the specified item.
     *
     * @param int|string $key
     * @param array $ids
     * @return \Laravelayers\Foundation\Decorators\DataDecorator
     */
    public function setSorting($id)
    {
        $item = clone $this->getByKey($id);

        if (!is_null($item->{$item->getSortKey()})) {
            $elements = $this->getElements()
                ->getRequest()
                ->getFormElements();

            $key = array_search($id, array_column($elements, 'id'));

            $setter = Str::camel("set_{$item->getSortKey()}");

            $item->{$setter}($this->get($key)->{$item->getSortKey()});
        }

        return $item;
    }


    /**
     * Get the link of the form action.
     *
     * @param array $data
     * @return array
     */
    protected function getFormActionLink($data)
    {
        $data['httpQuery'] = array_merge(Request::except([
            'action', '_token', '_method', FormDecorator::getElementsPrefixName(), PreviousUrl::getInputName()
        ]), $data['httpQuery'] ?? []);

        return $this->getActionLink($data);
    }

    /**
     * Initialize form actions by default.
     *
     * @return array
     */
    protected function initDefaultFormActions()
    {
        return [
            'index' => $this->initFormActionToStore(),
            'editMultiple' => $this->initFormActionToUpdateMultiple(),
            'updateMultiple' => $this->initFormActionToUpdateMultiple(),
            'deleteMultiple' => $this->initFormActionToDestroyMultiple(),
            'destroyMultiple' => $this->initFormActionToDestroyMultiple()
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
            'action' => 'index',
            'link' => 'store',
        ];
    }

    /**
     * Initialize the form action to update multiple.
     *
     * @return array
     */
    protected function initFormActionToUpdateMultiple()
    {
        return [
            'method' => 'POST',
            'action' => 'updateMultiple',
            'link' => 'store'
        ];
    }

    /**
     * Initialize the form action to destroy multiple.
     *
     * @return array
     */
    protected function initFormActionToDestroyMultiple()
    {
        return [
            'method' => 'POST',
            'action' => 'destroyMultiple',
            'link' => 'store'
        ];
    }

    /**
     * Get an action link.
     *
     * @param array $data
     * @return array
     */
    protected function getActionLink($data)
    {
        $data['httpQuery'] = array_merge(PreviousUrl::query(), $data['httpQuery'] ?? []);

        return $this->prepareActionLink($data);
    }

    /**
     * Get an action value.
     *
     * @param array $data
     * @return array
     */
    protected function getActionValue($data)
    {
        if (!isset($data['hidden']) || is_null($data['hidden'])) {
            $data['hidden'] = Gate::denies(
                trim(Str::before(strtolower($data['value']), 'multiple'), '_')
            );
        }

        return $data;
    }

    /**
     * Initialize actions by default.
     *
     * @return array
     */
    protected function initDefaultActions()
    {
        return [
            'add' => $this->initActionToAdd(),
            'select' => $this->initActionToSelect(),
            'create' => $this->initActionToCreate(),
            'editMultiple' => $this->initActionToEdit(),
            'deleteMultiple' => $this->initActionToDelete()
        ];
    }

    /**
     * Initialize the action to add.
     *
     * @return array
     */
    protected function initActionToAdd()
    {
        return [
            'type' => 'add',
            'link' => Request::route()->getName(),
            'httpQuery' => [
                'action' => 'add'
            ],
            'text' => static::transOfAction('add'),
            'hidden' => $this->getCurrentAction('_action') == 'add' ?: Gate::denies('update')
        ];
    }

    /**
     * Initialize the action to select.
     *
     * @return array
     */
    protected function initActionToSelect()
    {
        return [
            'type' => 'select',
            'name' => 'select',
            'value' => 'edit_multiple',
            'text' => static::transOfAction('select'),
            'group' => 2,
            'hidden' => $this->isEmpty() || $this->getCurrentAction('_action') != 'add' ?: Gate::denies('update')
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
            'text' => static::transOfAction('create'),
            'hidden' => $this->getCurrentAction('_action') == 'add' ?: Gate::denies('create')
        ];
    }

    /**
     * Initialize the action to edit multiple.
     *
     * @return array
     */
    protected function initActionToEdit()
    {
        return [
            'type' => 'edit',
            'name' => 'edit',
            'value' => 'edit_multiple',
            'text' => static::transOfAction('edit_multiple'),
            'group' => 2,
            'hidden' => $this->isEmpty() || $this->getCurrentAction('_action') == 'add' ?: Gate::denies('update')
        ];
    }

    /**
     * Initialize the action to delete multiple.
     *
     * @return array
     */
    protected function initActionToDelete()
    {
        return [
            'type' => 'delete',
            'name' => 'delete',
            'value' => 'delete_multiple',
            'text' => static::transOfAction('delete_multiple'),
            'group' => 2,
            'hidden' => $this->isEmpty() || $this->getCurrentAction('_action') == 'add' ?: Gate::denies('delete')
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
            'type' => 'checkbox'
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
            'type' => in_array($this->initActionCheckbox()['type'], ['checkbox', 'radio'])
                ? $this->initActionCheckbox()['type'] . '.readonly'
                : 'hidden',
            'value' => [['selected' => true]]
        ]);
    }

    /**
     * Get the filter.
     *
     * @return \Laravelayers\Form\Decorators\FormDecorator
     */
    public function getFilter()
    {
        if (!isset(static::$filter['filter'])) {
            static::$filter['filter'] = $this->prepareFilter($this->initFilter());
        }

        return static::$filter['filter'];
    }

    /**
     * Prepare the filter.
     *
     * @param array $elements
     * @return \Laravelayers\Form\Decorators\FormDecorator
     */
    protected function prepareFilter($elements)
    {
        $this->prepareFilterElements($elements);

        $this->getSearchElements($elements);

        $this->getPaginationElement($elements);

        $this->getActionElement($elements);

        $this->getPreviousElement($elements);

        return app(FormDecorator::class, [$elements])->setForm(['form' => [
            'name' => 'form_filter',
            'value' => Arr::except($this->initFilterFormAction(), ['class']),
            'class' => $this->initFilterFormAction()['class'] ?? ''
        ]])->getElements($this);
    }

    /**
     * Initialize the filter form action.
     *
     * @return array
     */
    protected function initFilterFormAction()
    {
        return [
            'method' => 'GET',
            'action' => Request::url(),
            'data-form-beforeunload' => '',
            'class' => 'form-beforeunload'
        ];
    }

    /**
     * Get the form element of the current action.
     *
     * @param array $elements
     * @return array
     */
    protected function getActionElement(&$elements)
    {
        if ($action = $this->getCurrentAction('_action')) {
            $elements['action'] = $action;
        }

        return $elements;
    }

    /**
     * Get the previous form element.
     *
     * @param array $elements
     * @return array
     */
    protected function getPreviousElement(&$elements)
    {
        if (PreviousUrl::getHash()) {
            $elements = array_merge([PreviousUrl::getInputName() => [
                'type' => 'hidden',
                'id' => 'previous_for_filter',
                'value' => PreviousUrl::getHash(),
            ]], $elements);
        }

        return $elements;
    }

    /**
     * Get the search form elements.
     *
     * @param array $elements
     * @return array
     */
    protected function getSearchElements(&$elements)
    {
        $searchOptionsKey = key($this->initSearchOptionsElement());
        $searchKey = key($this->initSearchElement());

        $elements = array_merge([$searchOptionsKey => [], $searchKey => []], $elements);

        $elements[$searchOptionsKey] = array_merge($this->prepareSearchOptionsElement(
            $this->initSearchOptionsElement()[$searchOptionsKey]
        ), $elements[$searchOptionsKey]);

        $elements[$searchKey] = array_merge($this->prepareElement(
            $this->initSearchElement()[$searchKey]
        ), $elements[$searchKey]);

        if (isset($elements[$searchKey]['disabled']) || !empty($elements[$searchKey]['hidden'])) {
            unset($elements[$searchOptionsKey]);

            if (!empty($elements[$searchKey]['hidden'])) {
                unset($elements[$searchKey]);
            }
        }

        return $elements;
    }

    /**
     * Prepare the search options form element.
     *
     * @param array $data
     * @return array
     */
    protected function prepareSearchOptionsElement($data)
    {
        foreach($data['value'] as $key => $value) {
            if (!is_iterable($value)) {
                if (is_int($key)) {
                    unset($data['value'][$key]);

                    $key = $value;
                }

                $value = [];
            }

            $value['name'] = $value['name'] ?? $key;

            $this->getSearchOptionValue($value);
            $this->getSearchOptionText($value);
            $this->getSearchOptionSelected($value);

            $data['value'][$key] = $value;
        }

        return $this->prepareElement($data);
    }

    /**
     * Get the option value of the search options form element.
     *
     * @param array $data
     * @return array
     */
    protected function getSearchOptionValue(&$data)
    {
        if (!isset($data['value'])) {
            $data['value'] = $data['name'];
        }

        return $data;
    }

    /**
     * Get the option text of the search options form element.
     *
     * @param array $data
     * @return array
     */
    protected function getSearchOptionText(&$data)
    {
        if (!isset($data['text'])) {
            $data['text'] = static::transOfElement("search_by_{$data['name']}_text");
        }

        return $data;
    }

    /**
     * Get the selected option of the search options form element.
     *
     * @param array $data
     * @return array
     */
    protected function getSearchOptionSelected(&$data)
    {
        if (Request::get(Service::getSearchByName()) == $data['name']) {
            $data['selected'] = true;
        }

        return $data;
    }

    /**
     * Initialize the search form element.
     *
     * @return array
     */
    protected function initSearchElement()
    {
        return [
            Service::getSearchName() => [
                'type' => 'search.group',
                'name' => Service::getSearchName(),
                'label' => static::transOfElement("search_label", [], true),
                'value' => Request::get(Service::getSearchName()),
                'line' => 'search',
                'group' => 'search'
            ],
        ];
    }

    /**
     * Initialize the search options form element.
     *
     * @return array
     */
    protected function initSearchOptionsElement()
    {
        return [
            Service::getSearchByName() => [
                'type' => 'select',
                'name' => Service::getSearchByName(),
                'label' => static::transOfElement("search_label", [], true),
                'value' => $this->initSearch(),
                'hidden' => !$this->initSearch() ?: false,
                'required' => true,
                'line' => 'search',
                'group' => 'search'
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
        return [
            'id' => []
        ];
    }

    /**
     * Prepare the filter elements.
     *
     * @param array $elements
     * @return array
     */
    protected function prepareFilterElements(&$elements)
    {
        foreach($elements as $key => $value) {
            $value['name'] = $value['name'] ?? $key;

            if (!isset($value['group'])) {
                $value['group'] = 'filter';
            }

            if (is_null($value['value'] ?? null)) {
                $value['value'] = Request::get($value['name']);
            } elseif ($value['value'] instanceof BaseCollectionDecorator) {
                if (Request::has($value['name']) && $value['value']->getSelectedItems()->isEmpty()) {
                    $value['value'] = $value['value']->setSelectedItems(Request::get($value['name']));
                }
            }

            $elements[$key] = $this->prepareElement($value);
        }

        return $elements;
    }

    /**
     * Get the pagination form element.
     *
     * @param array $elements
     * @return array
     */
    protected function getPaginationElement(&$elements)
    {
        if (($data = $this->getData()) instanceof AbstractPaginator) {
            if (current($this->initPagination()) != $data->perPage() || $data->hasMorePages()) {
                $paginationKey = key($this->initPaginationElement());

                $elements[$paginationKey] = array_merge($this->preparePaginationElement(
                        current($this->initPaginationElement())
                ), $elements[$paginationKey] ?? []);

                if (!empty($elements[$paginationKey]['hidden'])) {
                    unset($elements[$paginationKey]);
                }
            }
        }

        return $elements;
    }

    /**
     * Prepare the pagination form element.
     *
     * @param array $data
     * @return array
     */
    protected function preparePaginationElement($data)
    {
        $data['value'] = array_flip($data['value']);

        if (!empty($data['value']) && $this->perPage() && empty($data['value'][$this->perPage()])) {
            $data['value'][$this->perPage()] = '';

            ksort($data['value']);
        }

        foreach ($data['value'] as $key => $value) {
            $data['value'][$key] = [
                'name' => $key,
                'value' => $key,
                'text' => $key,
                'selected' => $this->perPage() == $key,
            ];
        }

        return $this->prepareElement($data);
    }

    /**
     * Initialize the pagination form element.
     *
     * @return array
     */
    protected function initPaginationElement()
    {
        return [
            'pagination' => [
                'type' => 'radio.group',
                'name' => Service::getPerPageName(),
                'value' => $this->initPagination(),
                'label' => static::transOfElement("pagination_label", [], true),
                'group' => 'pagination'
            ],
        ];
    }

    /**
     * Initialize the pagination options.
     *
     * @return array
     */
    protected function initPagination()
    {
        return [25, 50, 100];
    }

    /**
     * Get the filter link.
     *
     * @return \Laravelayers\Form\Decorators\FormElementDecorator
     */
    public function getFilterLink()
    {
        if (!isset(static::$filter['link'])) {
            static::$filter['link'] = app(FormDecorator::class, [$this->initFilterLink()])
                ->getElements()->first();
        }

        return static::$filter['link'];
    }

    /**
     * Initialize the filter link.
     *
     * @return array
     */
    protected function initFilterLink()
    {
        return [
            'filter_link' => [
                'type' => 'search.group',
                'value' => Request::get(Service::getSearchName()),
                'placeholder' => static::transOfElement('filter_link_placeholder'),
                'icon' => 'icon-filter',
            ],
        ];
    }

    /**
     * Get the filter reset link.
     *
     * @return \Laravelayers\Form\Decorators\FormElementDecorator
     */
    public function getFilterResetLink()
    {
        if (!isset(static::$filter['reset'])) {
            static::$filter['reset'] = app(FormDecorator::class, [$this->initFilterResetLink()])
                ->getElements()
                ->first();
        }

        return static::$filter['reset'];
    }

    /**
     * Initialize the filter reset link.
     *
     * @return array
     */
    protected function initFilterResetLink()
    {
        return [
            'filter_reset' => [
                'type' => 'button',
                'value' => [
                    'link' => [
                        'type' => 'reset',
                        'text' => static::transOfElement('filter_reset_link_text'),
                        'link' => PreviousUrl::addQueryHash(URL::current())
                    ],
                ],
                'hidden' => !Request::has(Service::getSearchName())
            ],
        ];
    }

    /**
     * Get the quick filter.
     *
     * @return \Laravelayers\Form\Decorators\FormDecorator
     */
    public function getQuickFilter()
    {
        if (!isset(static::$filter['quick'])) {
            static::$filter['quick'] = $this->prepareQuickFilter($this->initQuickFilter());
        }

        return static::$filter['quick'];
    }

    /**
     * Prepare the quick filter.
     *
     * @param array $data
     * @param string|null $class
     * @return array
     */
    public function prepareQuickFilter($data, $class = null)
    {
        foreach($data as $key => $value) {
            $value['type'] = $value['type'] ?? 'submit';

            if (!isset($name)) {
                $name = $value['name'] ?? 'quick';
            }

            $value['name'] = $value['name'] ?? $name;
            $value['value'] = $value['value'] ?? $key;

            if (!empty($value['value'])) {
                $value['link'] = [$value['name'] => $value['value']];
            }

            $value['text'] = $value['text'] ?? static::transOfElement("{$value['name']}_{$value['value']}_text");

            unset($value['value']);

            if (is_array($value['link'])) {
                $query = $value['link'];
                $value['link'] = Request::fullUrlWithQuery(array_merge(['search' => ''], $value['link']));
            }

            if (is_null($class)) {
                $value['class'] = ($value['class'] ?? '') . ' small hollow';
            }

            $data[$key] = $value;

            if (!empty($query)) {
                foreach (Request::query() as $queryKey => $queryValue) {
                    if (isset($query[$queryKey]) && $query[$queryKey] == $queryValue) {
                        unset($query[$queryKey]);

                        if (!$query) {
                            $data[$key]['disabled'] = '';
                            $data[$key]['link'] = Request::fullUrlWithQuery([
                                'search' => strlen(Request::get('search')) ? Request::get('search') : null,
                                'role' => null
                            ]);
                        }
                    }
                }
            }
        }

        $data = ['button' => [
            'type' => 'button',
            'name' => 'quick',
            'value' => $data
        ]];

        return app(FormDecorator::class, [$data])->getElements()->first();
    }

    /**
     * Prepare the actions.
     *
     * @param array $data
     * @return \Laravelayers\Form\Decorators\FormDecorator
     */
    protected function prepareActions($data)
    {
        return $this->prepareActionsFromTrait($data)->getElements();
    }

    /**
     * Get default form elements.
     *
     * @return array
     */
    protected function getDefaultElements()
    {
        return [
            'pattern' => [
                'type' => 'text',
                'group' => 'replacement',
                'line' => 'preg_replace'
            ],
            'replacement' => [
                'type' => 'text',
                'group' => 'replacement',
                'line' => 'preg_replace'
            ],
        ];
    }

    /**
     * Prepare form elements.
     *
     * @param array|\Traversable $elements
     * @return \Laravelayers\Form\Decorators\FormDecorator
     */
    protected function prepareElements($elements)
    {
        $this->prepareElementsByActions($elements);

        $this->getCurrentActionElement($elements);

        $this->getSubmittersElement($elements);

        return $this->prepareElementsFromTrait($elements)->getElements($this);
    }

    /**
     * Prepare the form elements by actions.
     *
     * @param array|\Traversable $elements
     * @return array
     */
    protected function prepareElementsByActions(&$elements)
    {
        if (!$this->isAction(['editMultiple', 'updateMultiple'])) {
            $elements = [];
        }

        return $elements;
    }

    /**
     * Get the form element of the current action.
     *
     * @param array $elements
     * @return array
     */
    protected function getCurrentActionElement(&$elements)
    {
        if ($this->getCurrentAction('multiple')) {
            $elements['action'] = Str::snake($this->getCurrentFormAction('action'));
            $elements['preaction'] = Str::snake($this->getCurrentAction('action'));
        }

        if ($action = $this->getCurrentAction('_action')) {
            $elements['_action'] = $action;
        }

        return $elements;
    }

    /**
     * Get a translation for the given column key.
     *
     * @param string $key
     * @param array $replace
     * @param bool $empty
     * @param string $locale
     * @return string
     */
    public static function transOfColumn($key = null, $replace = [], $empty = false, $locale = null)
    {
        return static::trans(static::$translationOfColumns, $key, $replace, $locale, $empty);
    }
}
