<?php

namespace Laravelayers\Form\Decorators;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Decorators\Decorator;
use Laravelayers\Previous\PreviousUrl;

class FormDecorator extends CollectionDecorator
{
    /**
     * HTTP request.
     *
     * @var Request $request
     */
    protected $request;

    /**
     * The form.
     *
     * @var array|\Laravelayers\Form\Decorators\FormElementDecorator
     */
    protected $form = [];

    /**
     * The prefix for form elements.
     *
     * @var string|null
     */
    protected $elementsPrefix = null;

    /**
     * The prefix name for form elements.
     *
     * @var string
     */
    protected static $elementsPrefixName = 'element';

    /**
     * Validation rules for form elements.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Validation messages for form elements.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * Validation errors for form elements.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Warnings for the form.
     *
     * @var array
     */
    protected $warnings = [];

    /**
     * Success message for the form.
     *
     * @var string|null
     */
    protected $success;

    /**
     * True if the button named submit is pressed and there must be a redirect to the previous page of the form.
     *
     * @var bool
     */
    public $redirectToPrevious = false;

    /**
     * Decorator startup method.
     *
     * @param mixed $data
     * @return $this|static|mixed
     */
    public static function make($data = [])
    {
        if (static::isDecorator($data)) {
            if ($data instanceof FormElementDecorator) {
                $data = [$data];
            } else {
                return parent::make($data->initElements())->getElements($data);
            }
        }

        return parent::make($data);
    }

    /**
     * Get the form elements.
     *
     * @param null|\Laravelayers\Foundation\Decorators\Decorator $source
     * @return $this
     */
    public function getElements($source = null)
    {
        if ($this->getForm() instanceof FormElementDecorator) {
            return $this;
        }

        return $this->decorateElements(
            $this->updateElements($source)
        );
    }

    /**
     * Get the form.
     *
     * @param string|null $key
     * @return array|\Laravelayers\Form\Decorators\FormElementDecorator
     */
    public function getForm($key = null)
    {
        return $key ? $this->form->getValue($key) : $this->form;
    }

    /**
     * Set the form.
     *
     * @param array|\Traversable $form
     * @return $this
     */
    public function setForm($form)
    {
        if ($form) {
            if ($form instanceof FormElementDecorator && !$form->getOriginal()) {
                $form = $form->get();
            }

            if (!$form instanceof FormElementDecorator) {
                $name = 'form';

                if ($form && isset(current($form)['value'])) {
                    $name = key($form);
                    $form = current($form);
                }

                if (empty($form['type']) || explode('.', $form['type'])[0] != 'form') {
                    $form['type'] = 'form';
                }

                $form['name'] = $form['name'] ?? $name;

                if (!isset($form['value'])) {
                    $form['value'] = [
                        'method' => $form['method'] ?? '',
                        'methodField' => $form['methodField'] ?? '',
                        'action' => $form['action'] ?? '',
                    ];

                    unset($form['method'], $form['methodField'], $form['action']);
                }

                $form['value']['enctype'] = $form['value']['enctype'] ?? 'multipart/form-data';
            }
        }

        $this->form = $form ?: [];

        return $this;
    }

    /**
     * Get the prefix of form elements.
     *
     * @param bool $dot
     * @return string
     */
    public function getElementsPrefix($dot = false)
    {
        if (!$dot) {
            $prefixes = explode('.', $this->elementsPrefix);

            $this->elementsPrefix = array_shift($prefixes);

            foreach ($prefixes as $value) {
                $this->elementsPrefix .= "[{$value}]";
            }
        }

        return $this->elementsPrefix;
    }

    /**
     * Set the prefix of form elements.
     *
     * @param int|string $id
     * @param string $name
     * @return $this
     */
    public function setElementsPrefix($id, $name = '')
    {
        if (!is_null($id)) {
            if (!$this->getForm() instanceof FormElementDecorator) {
                if (strlen($id)) {
                    $id = str_replace('.', '_', $id);

                    $name = $name ?: static::getElementsPrefixName();
                }

                $id = str_replace(["'", '[', ']'], '', str_replace(
                    '][', '.', $id
                ));

                $this->elementsPrefix = $name . ($id !== '' ? '.' . $id : '');
            }

            if ($this->first() instanceof FormElementDecorator) {
                if ($this->getForm() instanceof FormElementDecorator) {
                    $this->getForm()->setElementPrefix($this->getElementsPrefix());
                }

                foreach ($this as $key => $value) {
                    if ($value instanceof FormElementDecorator && $value->getOriginal()) {
                        $value->setElementPrefix($this->getElementsPrefix());
                    } else {
                        $this->put($key, $value->get());
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Get the prefix name of form elements.
     *
     * @return string
     */
    public static function getElementsPrefixName()
    {
        return static::$elementsPrefixName;
    }

    /**
     * Set the prefix name of form elements.
     *
     * @param string $value
     * @return void
     */
    public static function setElementsPrefixName($value)
    {
        static::$elementsPrefixName = $value;
    }


    /**
     * Get validation rules for form elements.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Add validation rules for the form element.
     *
     * @param string|array $name
     * @param string|array $rules
     * @return $this
     */
    public function addRules($name, $rules = '')
    {
        if ($rules || is_array($name)) {
            if (is_array($name)) {
                $this->rules = $name ? array_merge($this->rules, $name) : [];
            } else {
                $this->rules[$name] = $rules;
            }
        } else {
            unset($this->rules[$name]);
        }

        return $this;
    }

    /**
     * Get validation messages for the form.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set validation messages for the form.
     *
     * @param array $errors
     * @return $this
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Get validation errors for the form.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set validation errors for the form.
     *
     * @param array $errors
     * @return $this
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Set validation error for the form.
     *
     * @param string $name
     * @param string $error
     * @return $this
     */
    public function setError($name, $error)
    {
        if ($error) {
            $this->errors[$name] = $error;
        } else {
            unset($this->errors[$name]);
        }

        return $this;
    }

    /**
     * Get warning messages for the form.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getWarnings()
    {
        return collect($this->warnings);
    }

    /**
     * Set warning messages for the form.
     *
     * @param array $warnings
     * @return $this
     */
    public function setWarnings($warnings)
    {
        $this->warnings = $warnings;

        return $this;
    }

    /**
     * Set warning message for the form.
     *
     * @param string|array $warning
     * @return $this
     */
    public function setWarning($warning)
    {
        $this->warnings[] = $warning;

        return $this;
    }

    /**
     * Get success message for the form.
     *
     * @return string
     */
    public function getSuccess()
    {
        return !is_null($this->success) ? $this->success : trans('form::form.alerts.success');
    }

    /**
     * Set success message for the form.
     *
     * @param string $message
     * @return $this
     */
    public function setSuccess($message)
    {
        $this->success = $message;

        if ($this->getRequest()->hasSession()) {
            if ($this->success) {
                $this->getRequest()->session()
                    ->flash('success', $message);
            } else {
                $this->getRequest()->session()
                    ->forget('success');
            }
        }

        return $this;
    }

    /**
     * Render the form elements.
     *
     * @param string $string
     * @return \Illuminate\Support\HtmlString
     * @throws \Throwable
     */
    public function render($string = '')
    {
        if (!$string) {
            $string = $this->renderElements();
        }

        if ($string instanceof HtmlString) {
            $string = $string->toHtml();
        }

        if ($this->getForm()->isNotEmpty()) {
            $string = view($this->getForm()->view)
                ->with([
                    'slot' => new HtmlString($string),
                    'elements' => $this
                ])->render();
        }

        return new HtmlString($string);
    }

    /**
     * Render the form elements.
     *
     * @param string $string
     * @return \Illuminate\Support\HtmlString
     * @throws \Throwable
     */
    public function renderElements($string = '')
    {
        foreach ($this as $key => $element) {
            $string .= $this->renderByElement($element);
        }

        return new HtmlString($string);
    }

    /**
     * Render form elements by groups.
     *
     * @param \Laravelayers\Form\Decorators\FormElementDecorator $element
     * @return \Illuminate\Support\HtmlString
     * @throws \Throwable
     */
    protected function renderByElement(FormElementDecorator $element)
    {
        $group = $element->getGroup();
        $line = $element->getLine();

        if (!$group && !$line) {
            return $element->render();
        }

        $string = '';

        if (!$element->isRendered() && !$element->getHidden()) {
            if ($line) {
                $line = $this->where('line', $line)->where('hidden', false);

                foreach ($line as $lineElement) {
                    if (!$lineElement->getHidden()) {
                        $string .= view("form::layouts.fieldset.column")
                            ->with(['slot' => $lineElement->render(), 'element' => $element])
                            ->render();
                    }
                }

                if ($string) {
                    $string = view("form::layouts.fieldset.line")
                        ->with(['slot' => new HtmlString($string), 'element' => $element])
                        ->render();
                }
            }
            else {
                $group = $this->where('group', $group);

                foreach ($group as $groupElement) {
                    $string .= $groupElement->render();
                }
            }

            if ($group) {
                $string = view("form::layouts.fieldset.element")
                    ->with(['slot' => new HtmlString($string), 'element' => $element])
                    ->render();
            }
        }

        return new HtmlString($string);
    }

    /**
     * Run the validator's rules for form elements.
     *
     * @param array|\Traversable $items
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validate($items = [])
    {
        $validator = Validator::make($this->getRequest()->all(), $this->getRules(), $this->getMessages());

        if (!$this->getForm() instanceof FormElementDecorator) {
            return $validator;
        }

        foreach($items as $item) {
            $item->getElements()->validate();
        }

        $attributeNames = [];

        foreach($this->getData() as $element) {
            $attributeName = $element->getNameDot();

            if (empty($this->getRules()[$attributeName])) {
                $attributeName .= '.*';

                if (empty($this->getRules()[$attributeName])) {
                    $attributeName = '';
                }
            }

            if ($attributeName) {
                $attributeNames[$attributeName] = $element->getLabel()
                    ?: ($element->getAttributes('placeholder') ?: $element->get('name'));
            }
        }

        if ($attributeNames) {
            $validator->setAttributeNames($attributeNames);
        }

        if ($this->getErrors()) {
            $validator->after(function ($validator) {
                foreach($this->getErrors() as $key => $value) {
                    if (!empty($this->getData()[$key])) {
                        $key = $this->getData()[$key]->getNameDot();
                    }

                    if (!$validator->errors()->has($key)) {
                        $validator->errors()->add($key, $value);
                    }
                }
            });
        }

        $success = $this->getSuccess();

        $this->setSuccess(null);

        $validator->validate();

        $this->setSuccess($success);

        if ($this->redirectToPrevious) {
            $this->getRequest()->session()
                ->flash(PreviousUrl::getRedirectInputName(), 1);
        }

        return $validator;
    }

    /**
     * Get true if the button named submit is pressed and there must be a redirect to the previous page of the form,
     * or false.
     *
     * @return bool
     */
    public function getRedirectToPrevious()
    {
        return $this->redirectToPrevious;
    }

    /**
     * Set true if the button named submit is pressed and there must be a redirect to the previous page of the form,
     * or false.
     *
     * @param bool $value
     * @return $this
     */
    public function setRedirectToPrevious($value)
    {
        $this->redirectToPrevious = (bool) $value;

        return $this;
    }

    /**
     * Update the values of the form elements from the HTTP request.
     *
     * If the element contains an array or an object, then to update it,
     * you must pass the data source of the element containing the method for updating it.
     * The element update method contains the "set" prefix and the element name in the "CamelCase" style.
     *
     * @param Decorator|null $source
     * @return mixed
     */
    protected function updateElements(Decorator $source = null)
    {
        if (!is_null($input = $this->getRequestElements())) {
            $prefix = $this->getElementsPrefix(true);
            $all = $this->getRequestElements(true);

            if ($source && $this->getData()->isNotEmpty()) {
                foreach ($input as $name => $value) {
                    $element = $this->getData()->firstWhere('name', $name) ?: $this->getData()->get($name);

                    if (!$element) {
                        $setter = Str::camel("set_{$name}");

                        if (method_exists($source, $setter)) {
                            $source->{$setter}($value, []);
                        }
                    }
                }
            }

            $files = $this->getRequest()->file($prefix);

            if ($files) {
                $input = array_merge($input, $files);
            }

            foreach($this->getData() as $key => $element) {
                $name = $element['name'] ?? $key;

                $value = $input[$name] ?? '';
                $valueType = getType($value);

                if ($element instanceof FormElementDecorator) {
                    $element = current($element->getOriginal());
                }

                $type = current(explode('.', $element['type'] ?? '', 2));

                if ($type == 'button' && !$this->getRequest()->session()->has('is_submit')) {
                    if ($value == 'submit') {
                        $this->redirectToPrevious = true;
                    }
                }

                if (!isset($element['disabled']) && empty($element['hidden'])) {
                    $prefixedKey = $prefix ? "{$prefix}.{$name}" : $name;

                    if(!empty($element['rules'])) {
                        if ($type == 'file') {
                            if (!empty($element['multiple'])) {
                                $prefixedKey .= '.*';
                            }

                            if (!$files) {
                                $element['rules'] = '';
                            }
                        }

                        $this->addRules($prefixedKey, $element['rules']);
                    }

                    $setter = Str::camel("set_{$name}");

                    if (method_exists($source, $setter)) {
                        $elementValue = $source->{$setter}($value, $element);

                        if (is_array($elementValue)) {
                            $element = $elementValue;
                        } elseif ($elementValue instanceof Decorator) {
                            $element['value'] = $elementValue->{$name};
                        } elseif (is_string($elementValue) || is_int($elementValue)) {
                            $element['value'] = $elementValue;
                        } else {
                            $element['value'] = (string) $elementValue;
                        }

                        if (isset($element['value'])) {
                            $value = $element['value'];
                        }

                        $this->getData()->put($key, $element);
                    } elseif (isset($source[$name])) {

                        if ($source[$name] instanceof Carbon) {
                            $source->put($name, $source[$name]->setTimeFromTimeString($value));
                        } else {
                            $source->put($key, $value);
                        }

                        if (empty($element['hidden']) && key_exists('value', $element) && !is_iterable($element['value'])) {
                            $element['value'] = $value;

                            $this->getData()->put($key, $element);
                        }
                    }

                    if ($all && $this->getRequest()->has($prefixedKey) && gettype($value) == $valueType) {
                        Arr::set($all, $prefixedKey, $value);
                        $this->getRequest()->merge($all);
                    }
                }
            }
        }

        return $this->getData();
    }

    /**
     * Get a HTTP request with form elements.
     *
     * @param bool $all
     * @return array|null
     */
    protected function getRequestElements($all = false)
    {
        if ($this->getRequest()) {
            $method = 'POST';

            if ($form = $this->getForm()) {
                $method = $form['method'] ?? ($form['value']['method'] ?? $method);
            }

            if ($this->getRequest()->old()
                || ($this->getRequest()->all() && $this->getRequest()->getRealMethod() == $method)
            ) {
                $request = [];
                $prefix = $this->getElementsPrefix(true);

                if ($this->getRequest()->old()) {
                    $request = $this->getRequest()->old($prefix);
                } else {
                    if (!$all) {
                        $request = $this->getRequest()->input($prefix);
                    }

                    $request = $request ?: $this->getRequest()->all();
                }

                return $request;
            }
        }

        return null;
    }

    /**
     * Get a HTTP request.
     *
     * @return mixed
     */
    public function getRequest()
    {
        if (is_null($this->request)) {
            $this->setRequest(\Illuminate\Support\Facades\Request::instance());
        }

        return $this->request;

    }

    /**
     * Set a HTTP request.
     *
     * @param Request|null $request
     * @return $this
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request ?: '';

        return $this;
    }

    /**
     * Decorate the form elements.
     *
     * @param array|\Traversable $data
     * @return $this|array|static
     */
    protected function decorateElements($data)
    {
        if (!$this->getForm() instanceof FormElementDecorator) {
            $forms = $data->filter(function ($value) {
                return explode('.', $value['type'] ?? '')[0] == 'form' ?: false;
            });

            $data->forget($forms->keys()->all());

            $form = FormElementDecorator::make($this->setForm($forms->first() ?: $this->getForm())->getForm());

            $this->setForm($form->isNotEmpty() ? $form->getElement() : $form);

            if (!$this->getForm() instanceof FormElementDecorator) {
                $this->setForm(FormElementDecorator::make($this->getForm())->getElement());
            }
        }

        return FormElementDecorator::make($this->setData($data))
            ->setElementsPrefix('', $this->getElementsPrefix());
    }

    /**
     * Dynamically retrieve data from the decorator.
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($value = parent::get($key)) {
            return $value;
        }

        if ($this->getData() instanceof Collection && ($element = $this->firstWhere('name', $key))) {
            return $element;
        }

        return parent::get($key);
    }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     * @throws \Throwable
     */
    public function __toString()
    {
        return $this->render()->toHtml();
    }

    /**
     * Prepare the instance for cloning.
     *
     * @return void
     */
    public function __clone()
    {
        if ($this->getForm() instanceof FormElementDecorator) {
            $this->setForm(clone $this->getForm());
        }

        parent::__clone();
    }
}
