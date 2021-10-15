<?php

namespace Laravelayers\Form\Decorators;

use ArrayIterator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Laravelayers\Contracts\Form\Form as FormContract;
use Laravelayers\Contracts\Form\Decorators\FormElement as FormElementContract;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Decorators\DataDecorator;
use Laravelayers\Foundation\Decorators\Decorator;

class FormElementDecorator extends DataDecorator implements FormElementContract, Htmlable
{
    use FormElement;

    /**
     * The original data of the form elements.
     *
     * @var array
     */
    protected $original;

    /**
     * The prefix for the form element.
     *
     * @var string
     */
    protected $elementPrefix;

    /**
     * Indicates whether the element is already rendered.
     *
     * @var bool
     */
    protected $isRendered = false;

    /**
     * Prepare data for decoration.
     *
     * @param $data
     * @return mixed
     */
    protected static function prepare($data)
    {
        if (is_numeric($data) || is_string($data)) {
            $data = ['name' => $data];
        }

        return parent::prepare($data);
    }

    /**
     * Decorate the collection.
     *
     * @param CollectionDecorator $data
     * @return FormDecorator|CollectionDecorator
     */
    protected static function decorateCollection(CollectionDecorator $data)
    {
        foreach ($data->get() as $key => $item) {
            if (static::isDecorator($item) === false) {
                if (!$item->getOriginal()) {
                    $item->getElement();
                }

                continue;
            }

            $data->put($key, static::decorate(static::prepare($item)));

            $data->get($key)->original = [$key => $item];

            $data->get($key)->put('name', $item['name'] ?? $key);

            $data->get($key)->getElement();
        }

        return $data;
    }

    /**
     * Get the original data of the form element.
     *
     * @return array
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Get the form element.
     *
     * @param array $value
     * @return FormElementDecorator
     */
    public function getElement($value = [])
    {
        $name = $this->getData()['name'] ?? $this->getData()['type'];

        if (!$this->original) {
            $this->original = [$name => $this->getData()];
        }

        if ($value) {
            $this->original = [
                $name => array_merge(current($this->original), (array) $value)
            ];
        }

        $this->setData(
            resolve(FormContract::class)->getElement(key($this->original), current($this->original))
        );

        return $this;
    }

    /**
     * Get the type of the form element.
     *
     * @return mixed
     */
    public function getType()
    {
        return current(explode('.', $this->get('type'), 2));
    }

    /**
     * Add the type of the form element.
     *
     * @param string $value
     * @return $this
     */
    public function addType($value = 'hidden')
    {
        return $this->put('type', $value)->getElement(['type' => $value]);
    }

    /**
     * Get the view of the form element.
     *
     * @return mixed
     */
    public function getView()
    {
        return $this->get('view');
    }

    /**
     * Add the view of the form element.
     *
     * @param string $value
     * @return $this
     */
    public function addView($value)
    {
        return $this->put('view', $value)->getElement(['view' => $value]);
    }

    /**
     * Get the name of the form element.
     *
     * @param FormElementContract|null $value
     * @return mixed|string
     */
    public function getName(FormElementContract $value = null)
    {
        if ($value) {
            return $this->getNameByValue($value);
        }

        $name = $this->get('name');

        if ($this->getElementPrefix()) {
            $name = "{$this->getElementPrefix()}[{$name}]";
        }

        if ($this->getMultiple()) {
            $name .= '[]';
        }

        return $name;
    }

    /**
     * Get the name in dotted notation.
     *
     * @param FormElementContract|null $value
     * @return string
     */
    public function getNameDot(FormElementContract $value = null)
    {
        return str_replace('[', '.', str_replace(
            ["'", '[]', ']'], '', $this->getName($value)
        ));
    }

    /**
     * Get the name of the form element by value.
     *
     * @param FormElementContract $value
     * @return string
     */
    protected function getNameByValue(FormElementContract $value)
    {
        $name = $this->getName();

        if ($this->getMultiple()) {
            $name = str_replace('[]', "[{$value->getFormElementId()}]", $name);
        }

        return $name;
    }

    /**
     * Add the name of the form element.
     *
     * @param string $value
     * @return $this
     */
    public function addName($value)
    {
        return $this->put('name', $value)->getElement(['name' => $value]);
    }

    /**
     * Get the name with the specified prefix.
     *
     * @param $value
     * @return string
     */
    public function getPrefixedName($value)
    {
        $name = Str::snake($this->get('name'));
        $prefix = Str::snake($value);

        return $this->getElementPrefix()
            ? str_replace("[{$name}]", "[{$prefix}_{$name}]", $this->getName())
            : "{$prefix}_{$this->getName()}";
    }

    /**
     * Get the ID of the form element.
     *
     * @param FormElementContract|null $value
     * @return mixed|string
     */
    public function getId(FormElementContract $value = null)
    {
        if ($value) {
            return $this->getIdByValue($value);
        }

        $id = $this->get('id');

        if ($this->elementPrefix) {
            $id = "{$this->elementPrefix}_{$id}";
        }

        $id = Str::snake(str_replace(['[', ']', "'", '"'], ' ', $id));

        return $id;
    }

    /**
     * Get the ID of the form element by value.
     *
     * @param FormElementContract $value
     * @return string
     */
    protected function getIdByValue(FormElementContract $value)
    {
        return strlen($value->getFormElementId())
            ? "{$this->getId()}_{$value->getFormElementId()}"
            : $this->getId();
    }

    /**
     * Add the ID of the form element.
     *
     * @param string $value
     * @return $this
     */
    public function addId($value)
    {
        return $this->put('id', $value)->getElement(['id' => $value]);
    }

    /**
     * Get the value of the form element.
     *
     * @param FormElementContract|string $value
     * @return array|\Illuminate\Http\Request|mixed|string
     */
    public function getValue($value = '')
    {
        if ($value) {
            if (is_numeric($value) || is_string($value)) {
                return $this->get('value')->{$value};
            }

            return $this->getValueByValue($value);
        }

        if (is_object($this->get('value'))) {
            return $this->decorateValue(
                $this->get('value')
            );
        }

        return $this->get('value');
    }

    /**
     * Get the value of the form element by value.
     *
     * @param \Laravelayers\Contracts\Form\Decorators\FormElement $value
     * @return string
     */
    protected function getValueByValue(FormElementContract $value)
    {
        return $value->has('value')
            ? $value->get('value')
            : $value->getFormElementId();
    }

    /**
     * Add the value of the form element.
     *
     * @param mixed $value
     * @return $this
     */
    public function addValue($value)
    {
        return $this->put('value', $value)->getElement(['value' => $value]);
    }

    /**
     * Get the text of the form element.
     *
     * @param FormElementContract|null $value
     * @return mixed|string
     */
    public function getText(FormElementContract $value = null)
    {
        return $value
            ? $this->getTextByValue($value)
            : $this->get('text');
    }

    /**
     * Get the text of the form element by value.
     *
     * @param \Laravelayers\Contracts\Form\Decorators\FormElement $value
     * @return string
     */
    protected function getTextByValue(FormElementContract $value)
    {
        return $value->getFormElementText();
    }

    /**
     * Add the text of the form element.
     *
     * @param string $value
     * @return $this
     */
    public function addText($value)
    {
        return $this->put('text', $value)->getElement(['text' => $value]);
    }

    /**
     * Decorating the elements of the form element value.
     *
     * @param $data
     * @return mixed
     */
    protected function decorateValue($data)
    {
        if (strcasecmp(get_class($data), CollectionDecorator::class) == 0) {
            foreach ($data as $key => $value) {
                if ($value instanceof FormElementContract) {
                    break;
                }

                if (strcasecmp(get_class($value), CollectionDecorator::class) == 0) {
                    $data[$key] = $this->decorateValue($value);
                } else {
                    $data[$key] = FormElementDecorator::make($value);
                }
            }
        }

        return $data;
    }

    /**
     * Get true if element is selected or false.
     *
     * @param FormElementContract|null $value
     * @return bool
     */
    public function getIsSelected(FormElementContract $value = null)
    {
        if ($value) {
            return $this->getIsSelectedByValue($value);
        }

        return boolval($this->get('selected') ?: parent::getIsSelected());
    }

    /**
     * Get true if element value is selected or false.
     *
     * @param FormElementContract $value
     * @return bool
     */
    protected function getIsSelectedByValue(FormElementContract $value)
    {
        return boolval($value->getIsFormElementSelected());
    }

    /**
     * Get the value of the multiplicity of the form element.
     *
     * @return string
     */
    public function getMultiple()
    {
        return $this->get('multiple') ? 'multiple' : '';
    }

    /**
     * Add the value of the multiplicity of the form element.
     *
     * @param bool $value
     * @return $this
     */
    public function addMultiple($value)
    {
        $value = (bool) $value;

        return $this->put('multiple', $value)->getElement(['multiple' => $value]);
    }

    /**
     * Get the label of the form element.
     *
     * @return mixed
     */
    public function getLabel()
    {
        return $this->getType() != 'hidden' ? $this->get('label') : '';
    }

    /**
     * Add the label of the form element.
     *
     * @param string $value
     * @return $this
     */
    public function addLabel($value)
    {
        return $this->put('label', $value)->getElement(['label' => $value]);
    }

    /**
     * Get the form element class.
     *
     * @param FormElementContract|array|string $value
     * @return string
     */
    public function getClass($value = '')
    {
        if ($value) {
            if ($value instanceof FormElementContract) {
                return $this->getClassByValue($value);
            } else {
                $value = (array) $value;

                foreach(explode(' ', $this->getFormElementClass()) as $row) {
                    if (in_array(trim($row), $value)) {
                        $class[] = $row;
                    }
                }

                return implode(' ', $class ?? []);
            }
        }

        return $this->getFormElementClass();
    }

    /**
     * Get the class of the form element by value.
     *
     * @param FormElementContract $value
     * @return string
     */
    protected function getClassByValue(FormElementContract $value)
    {
        return $value->getFormElementClass();
    }

    /**
     * Add the class of the form element.
     *
     * @param string $value
     * @return $this
     */
    public function addClass($value)
    {
        return $this->put('class', $value)->getElement(['class' => $value]);
    }

    /**
     * Get the help text of the form element.
     *
     * @return mixed
     */
    public function getHelp()
    {
        return $this->getType() != 'hidden' ? $this->get('help') : '';
    }

    /**
     * Add the help text of the form element.
     *
     * @param string $value
     * @return $this
     */
    public function addHelp($value)
    {
        return $this->put('help', $value)->getElement(['help' => $value]);
    }

    /**
     * Get the help ID of the form element.
     *
     * @param null|\Laravelayers\Contracts\Form\Decorators\FormElement $value
     * @return string
     */
    public function getHelpId($value = null)
    {
        return $this->get('help') ? "{$this->getId($value)}_help" : '';
    }

    /**
     * Ge the icon of the form element.
     *
     * @return string
     * @throws \Throwable
     */
    public function getIcon()
    {
        if (!$this->get('icon')) {
            return $this->get('icon');
        }

        $icon = explode(':', $this->get('icon'));

        return $icon[1] ?? view('foundation::layouts.icon', ['class' => $icon[0]])->render();
    }

    /**
     * Add the icon of the form element.
     *
     * @param string $value
     * @return $this
     */
    public function addIcon($value)
    {
        return $this->put('icon', $value)->getElement(['icon' => $value]);
    }

    /**
     * Get the group of the form element.
     *
     * @return mixed
     */
    public function getGroup()
    {
        return $this->get('group');
    }

    /**
     * Add the group of the form element.
     *
     * @param string $value
     * @return $this
     */
    public function addGroup($value)
    {
        return $this->put('group', $value)->getElement(['group' => $value]);
    }

    /**
     * Get the line of the form element.
     *
     * @return mixed
     */
    public function getLine()
    {
        return $this->get('line');
    }

    /**
     * Add the line of the form element.
     *
     * @param string $value
     * @return $this
     */
    public function addLine($value)
    {
        return $this->put('line', $value)->getElement(['line' => $value]);
    }

    /**
     * Get the value "hidden" for the form element.
     *
     * @return mixed
     */
    public function getHidden()
    {
        return $this->get('hidden');
    }

    /**
     * Add the value "hidden" for the form element.
     *
     * @param bool $value
     * @return $this
     */
    public function addHidden($value)
    {
        $value = (bool) $value;

        return $this->put('hidden', $value)->getElement(['hidden' => $value]);
    }

    /**
     * Get the attributes of the form element.
     *
     * @param string|array $attribute
     * @return string
     */
    public function getAttributes($attribute = '')
    {
        if ($this->getType() == 'hidden') {
            return '';
        }

        $attributes = $this->get('attributes');

        if ($attribute) {
            if (!is_array($attribute)) {
                return $attributes->get($attribute);
            }

            $method = key($attribute) != 'only' ? 'except' : 'only';

            $attributes = $attributes->{$method}(current($attribute));
        }

        $str = '';

        foreach($attributes as $key => $value) {
            if (!is_null($value)) {
                if ($str) {
                    $str .= ' ';
                }

                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }

                if ($value instanceof DataDecorator) {
                    $value = implode($value->all(),',');
                }

                $str .= "{$key}=\"{$value}\"";
            }
        }

        return $str;
    }

    /**
     * Get the attributes of the form element except specified.
     *
     * @param array $exception
     * @return string
     */
    public function getAttributesExcept($exception)
    {
        return $this->getAttributes(['except' => $exception]);
    }

    /**
     * Get the attributes of the form element only specified.
     *
     * @param array $exception
     * @return string
     */
    public function getAttributesOnly($exception)
    {
        return $this->getAttributes(['only' => $exception]);
    }

    /**
     * Add the attributes of the form element.
     *
     * @param array $attributes
     * @return $this
     */
    public function addAttributes($attributes)
    {
        if (!is_array(current($attributes))) {
            $attributes = (array) $attributes;
        }

        foreach($attributes as $name => $value) {
            $this->addAttribute($name, $value);
        }

        return $this;
    }

    /**
     * Add the attribute of the form element.
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addAttribute($name, $value)
    {
        return $this->put($name, $value)->getElement([$name => $value]);
    }

    /**
     * Get the first error message for the form element.
     *
     * @return string
     */
    public function getError()
    {
        if ($this->getType() == 'hidden') {
            return '';
        }

        return $this->getErrorKey()
            ? session('errors')->first($this->getErrorKey())
            : $this->get('error');
    }

    /**
     * Add the error message for the form element.
     *
     * @param string $value
     * @return $this
     */
    public function addError($value)
    {
        return $this->put('error', $value)->getElement(['error' => $value]);
    }

    /**
     * Get all the error messages for the form element.
     *
     * @return string
     */
    public function getErrors()
    {
        return $this->getErrorKey()
            ? session('errors')->get($this->getErrorKey())
            : '';
    }

    /**
     * Get the error key for the form element.
     *
     * @return string
     */
    protected function getErrorKey()
    {
        $isError = false;
        $nameDot = $this->getNameDot();

        if (session()->has('errors') ) {
            $isError = session('errors')->has($nameDot);

            if (!$isError) {
                $nameDot .= '.*';
                $isError = session('errors')->has($nameDot);
            }
        }

        return $isError ? $nameDot : '';
    }

    /**
     * Get the value "tooltip" for the form element.
     *
     * @return string
     */
    public function getTooltip()
    {
        $tooltip = '';

        if ($this->get('tooltip')) {
            $attributes = [
                'data-tooltip' => $this->getAttributes('data-tooltip') ?: '',
                'data-trigger-class' => $this->getAttributes('data-trigger-class') ?: '',
                'data-alignment' => $this->getAttributes('data-alignment') ?: 'left',
                'data-tooltip-class' => $this->getAttributes('data-tooltip-class') ?: 'tooltip form-tooltip',
                'title' => $this->get('tooltip')
            ];

            foreach($attributes as $key => $value) {
                $tooltip .= ($tooltip ? ' ' : '') ."{$key}" . ($value ? "=\"{$value}\"": '');
            }
        }

        return $tooltip;
    }

    /**
     * Add the value "tooltip" for the form element.
     *
     * @param string $value
     * @return $this
     */
    public function addTooltip($value)
    {
        return $this->put('tooltip', $value)->getElement(['tooltip' => $value]);
    }

    /**
     * Get validation rules for form elements.
     *
     * @return string
     */
    public function getRules()
    {
        $rules = $this->get('rules');

        return !is_null(static::isDecorator($this->get('rules')))
            ? $rules->get()
            : $rules;
    }

    /**
     * Add validation rules for the form element.
     *
     * @param string|array $value
     * @return $this
     */
    public function addRules($value)
    {
        return $this->put('rules', $value)->getElement(['rules' => $value]);
    }

    /**
     * Get the prefix of the form element.
     *
     * @return string
     */
    public function getElementPrefix()
    {
        return $this->elementPrefix;
    }

    /**
     * Set the prefix of the form element.
     *
     * @param string $prefix
     * @return $this
     */
    public function setElementPrefix($prefix)
    {
        $this->elementPrefix = $prefix;

        return $this;
    }

    /**
     * Render the form element using the given view.
     *
     * @param  array  $data
     * @return \Illuminate\Support\HtmlString
     * @throws \Throwable
     */
    public function render($data = [])
    {
        $this->isRendered = true;

        if (($view = $data['view'] ?? $this->getView()) && !$this->getHidden()) {
            $isLabel = ($this->getLabel() || $this->getHelp() || $this->getError() || $this->getTooltip()) ?: false;

            $string = view($view)->with(array_merge($data, [
                'element' => $this,
                'isWrapper' => !$isLabel
            ]))->render();

            if ($isLabel) {
                $string = view("form::layouts.label.element")->with([
                    'slot' => new HtmlString($string),
                    'element' => $this
                ])->render();
            }
        }

        return new HtmlString($string ?? '');
    }

    /**
     * Determine if the element is already rendered.
     *
     * @return bool
     */
    public function isRendered()
    {
        return (bool) $this->isRendered;
    }

    /**
     * Get the form element text.
     *
     * @return string
     */
    public function getFormElementText()
    {
        return $this->get('text');
    }

    /**
     * Get the value of the HTML attribute of the class of the form element.
     *
     * @return string
     */
    public function getFormElementClass()
    {
        return $this->get('class');
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        if ($this->getValue() instanceof Decorator) {
            return new ArrayIterator($this->getValue()->all());
        }

        return parent::getIterator();
    }

    /**
     * Prepare the instance for cloning.
     *
     * @return void
     */
    public function __clone()
    {
        if (is_object($this->{$this->getDataKeyName()})) {
            $this->{$this->getDataKeyName()} = clone $this->getDataKey();
        } else {
            foreach($this->getDataKey() as $key => $value)
            {
                if (is_object($value)) {
                    $this[$key] = clone $value;
                }
            }
        }
    }

    /**
     * Render the contents of the form element when casting to string.
     *
     * @return string
     * @throws \Throwable
     */
    public function __toString()
    {
        return (string) $this->render();
    }

    /**
     * Render the contents of the form element to HTML.
     *
     * @return string
     * @throws \Throwable
     */
    public function toHtml()
    {
        return (string) $this->render();
    }
}
