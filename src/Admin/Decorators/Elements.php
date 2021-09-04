<?php

namespace Laravelayers\Admin\Decorators;

use Illuminate\Http\Request;
use Laravelayers\Form\Decorators\Form;
use Laravelayers\Form\Decorators\FormDecorator;
use Laravelayers\Form\Decorators\FormElementDecorator;
use Laravelayers\Previous\PreviousUrl;

trait Elements
{
    use Form {
        Form::getElements as getElementsFromTrait;
    }

    /**
     * HTTP request.
     *
     * @var Request $request
     */
    protected static $request;

    /**
     * Translation key format for form elements.
     *
     * @var string
     */
    protected static $translationOfElements = 'admin::elements.%s';

    /**
     * Prepare form elements.
     *
     * @param array $elements
     * @return \Laravelayers\Form\Decorators\FormDecorator
     */
    protected function prepareElements($elements)
    {
        foreach ($elements as $key => $value) {
            if ($value instanceof FormElementDecorator) {
                $value = $value->get();
            }

            if (is_array($value)) {
                $value['name'] = $value['name'] ?? $key;

                $value = $this->prepareElement($value);
            }

            unset($elements[$key]);

            $key = is_int($key) && !empty($value['name']) ? $value['name'] : $key;

            $elements[$key] = $value;
        }

        $elements = app(FormDecorator::class, [
            array_merge($this->getCurrentForm(), $elements)
        ]);

        if (static::$request) {
            $elements->setRequest(static::$request);
        }

        return $elements;
    }

    /**
     * Set a HTTP request.
     *
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        static::$request = $request;

        return $this;
    }

    /**
     * Get the form for the current action.
     *
     * @return array
     */
    protected function getCurrentForm()
    {
        if ($value = $this->getCurrentFormAction()) {
            $form = ['form' => [
                'type' => 'form.js',
                'value' => $value
            ]];
        }

        return $form ?? [];
    }

    /**
     * Prepare the form element.
     *
     * @param $element
     * @return mixed
     */
    protected function prepareElement($element)
    {
        $element['_type'] = explode('.', strtolower($element['type'] ?? ''))[0];

        if ($element['_type'] != 'form') {
            $this->getElementLabel($element);
            $this->getElementPlaceholder($element);
            $this->getElementHelp($element);
            $this->getElementTooltip($element);
            $this->getElementError($element);
            $this->getElementGroup($element);
            $this->getElementValue($element);
            $this->getElementText($element);
            $this->getElementRequired($element);
        }

        unset($element['_type']);

        return $element;
    }

    /**
     * Get the form element label.
     *
     * @param array $element
     * @return array
     */
    protected function getElementLabel(&$element)
    {
        if (is_null($element['label'] ?? null)) {
            $element['label'] = static::transOfElement(
                "{$element['name']}_label", [], in_array($element['_type'], ['button', 'file']) ?: false
            );
        }

        return $element;
    }

    /**
     * Get the form element placeholder.
     *
     * @param array $element
     * @return array
     */
    protected function getElementPlaceholder(&$element)
    {
        if (is_null($element['placeholder'] ?? null)) {
            $element['placeholder'] = static::transOfElement("{$element['name']}_placeholder", [], true);

            if (!$element['placeholder']) {
                unset($element['placeholder']);
            }
        }

        return $element;
    }

    /**
     * Get the form element help.
     *
     * @param array $element
     * @return array
     */
    protected function getElementHelp(&$element)
    {
        if (is_null($element['help'] ?? null)) {
            $element['help'] = static::transOfElement("{$element['name']}_help", [], true);
        }

        return $element;
    }

    /**
     * Get the form element error message.
     *
     * @param array $element
     * @return array
     */
    protected function getElementError(&$element)
    {
        if (is_null($element['error'] ?? null)) {
            $element['error'] = static::transOfElement("{$element['name']}_error", [], true);
        }

        return $element;
    }

    /**
     * Get the form element tooltip.
     *
     * @param array $element
     * @return array
     */
    protected function getElementTooltip(&$element)
    {
        if (is_null($element['tooltip'] ?? null)) {
            $element['tooltip'] = static::transOfElement("{$element['name']}_tooltip", [], true);
        }

        return $element;
    }

    /**
     * Get the form element group.
     *
     * @param array $element
     * @return array
     */
    protected function getElementGroup(&$element)
    {
        if (!empty($element['hidden']) || empty($element['type']) || $element['_type'] == 'hidden') {
            $element['group'] = null;
        }

        if (!empty($element['group'])) {
            $element['group'] = static::transOfElement("{$element['group']}_group", [], true)
                ?: $element['group'];
        }

        return $element;
    }

    /**
     * Get the form element value.
     *
     * @param array $element
     * @return array
     */
    protected function getElementValue(&$element)
    {
        if (!empty($element['value']) && is_array($element['value'])) {
            foreach($element['value'] as $key => $value) {
                if (!is_array($value)) {
                    $value = ['value' => $value];
                    $element['value'][$key] = $value;
                }

                if (isset($value['type'])) {
                    if (!isset($value['label'])) {
                        $element['value'][$key]['label'] = static::transOfElement("{$key}_label");
                    }
                } else {
                    if (!isset($value['text'])) {
                        $element['value'][$key]['text'] = static::transOfElement("{$key}_text");
                    }
                }
            }
        } elseif (is_null($element['value'] ?? null)) {
            $element['value'] = $this->{$element['name']};
        }


        return $element;
    }

    /**
     * Get the form element text.
     *
     * @param array $element
     * @return array
     */
    protected function getElementText(&$element)
    {
        if (is_null($element['text'] ?? null)) {
            $element['text'] = static::transOfElement("{$element['name']}_text", [], true);
        }

        return $element;
    }

    /**
     * Get the "required" attribute of the form element.
     *
     * @param array $element
     * @return array
     */
    protected function getElementRequired(&$element)
    {
        if (!is_null($element['required'] ?? null)) {
            $element['rules'] = $element['rules'] ?? 'required';

            if (!is_array($element['rules'])) {
                $element['rules'] = explode('|', $element['rules']);
            }

            if (!in_array('required', $element['rules'])) {
                $element['rules'][] = 'required';
            }
        }

        return $element;
    }

    /**
     * Get the form submitters element.
     *
     * @param array $elements
     * @return array
     */
    protected function getSubmittersElement(&$elements)
    {
        if (isset($elements['submitters'])) {
            $submitters = $elements['submitters'];

            unset($elements['submitters']);
        }

        $submittersElement = $this->prepareSubmittersElement(
            $this->initSubmittersElement()[$this->getCurrentAction('action')] ?? []
        );

        if ($submittersElement) {
            $submitters = array_merge([
                'type' => count($submittersElement) > 1 ? 'button.group' : 'button',
                'value' => $submittersElement,
                'label' => '',
            ], ($submitters ?? []));
        }

        if (!empty($submitters['value'])) {
            $elements['submitters'] = $submitters;
        }

        return $elements;
    }

    /**
     * Prepare the form submitters element.
     *
     * @param array $submitters
     * @return array
     */
    protected function prepareSubmittersElement($submitters)
    {
        foreach($submitters as $key => $submitter) {
            if (!empty($submitter['link'])) {
                $submitters[$key] = $this->prepareActionLink($submitter);
            }

            if (!empty($submitters[$key]['hidden'])) {
                unset($submitters[$key]);
            }
        }

        return $submitters;
    }

    /**
     * Initialize the form submitters element.
     *
     * @return array
     */
    protected function initSubmittersElement()
    {
        return [
            'create' => $this->initSubmitterElementToStore(),
            'store' => $this->initSubmitterElementToStore(),
            'edit' => $this->initSubmitterElementToUpdate(),
            'update' => $this->initSubmitterElementToUpdate(),
            'editMultiple' => $this->initSubmitterElementToUpdate(),
            'updateMultiple' => $this->initSubmitterElementToUpdate(),
            'deleteMultiple' => $this->initSubmitterElementToDestroy(),
            'destroyMultiple' => $this->initSubmitterElementToDestroy(),
        ];
    }

    /**
     * Initialize the form submitter element to store.
     *
     * @return array
     */
    protected function initSubmitterElementToStore()
    {
        return [
            'submit' => [
                'type' => 'next',
                'value' => 'save',
                'text' => static::transOfElement("save_and_continue_button"),
                'class' => 'expanded'
            ]
        ];
    }

    /**
     * Initialize the form submitter element to update.
     *
     * @return array
     */
    protected function initSubmitterElementToUpdate()
    {
        return [
            'submit' => [
                'type' => 'back',
                'value' => 'submit',
                'text' => static::transOfElement("save_and_go_back_button"),
                'class' => 'expanded',
                'hidden' => !PreviousUrl::getInputName() ?: null,
            ],
            'save' => [
                'type' => 'submit',
                'value' => 'save',
                'text' => static::transOfElement("save_button"),
                'class' => 'expanded',
                'hidden' => !PreviousUrl::getInputName() || $this->getCurrentAction('multiple') ?: null,
            ]
        ];
    }

    /**
     * Initialize the form submitter element to destroy.
     *
     * @return array
     */
    protected function initSubmitterElementToDestroy()
    {
        return [
            'cancel' => [
                'type' => 'cancel',
                'link' => PreviousUrl::getUrl() ?: url()->previous(),
                'text' => static::transOfElement("cancel_button"),
                'class' => 'expanded',
            ],
            'submit' => [
                'type' => 'delete',
                'value' => 'submit',
                'text' => static::transOfElement("delete_button"),
                'class' => 'expanded'
            ]
        ];
    }

    /**
     * Render the text with the specified hint.
     *
     * @param string $text
     * @param string $hint
     * @param bool $nowrap
     * @return string
     */
    protected function renderAsHint($text, $hint, $nowrap = false)
    {
        $hint = view('admin::layouts.table.div', [
            'slot' => $hint, 'small' => true, 'nowrap' => $nowrap
        ]);

        return $text . $hint;
    }

    /**
     * Render the text with the specified link.
     *
     * @param string|array $text
     * @param string|array $url
     * @param bool $external
     * @param bool $nowrap
     * @param string|array $class
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function renderAsLink($text, $url, $external = false, $nowrap = false, $class = '')
    {
        if (is_array($text)) {
            $url = array_values($text);
            $text = array_values(array_flip($text));
        }

        return view('admin::layouts.table.a', [
            'slot' => $text, 'href' => $url, 'external' => (bool) $external, 'nowrap' => $nowrap, 'class' => $class
        ]);
    }

    /**
     * Render the icon.
     *
     * @param string $class
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function renderAsIcon($class)
    {
        return view('foundation::layouts.icon', [
            'class' => $class
        ]);
    }

    /**
     * Render the image.
     *
     * @param string $url
     * @param string $link
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function renderAsImage($url, $link = null)
    {
        return view('admin::layouts.table.img', [
            'slot' => $url, 'link' => $link
        ]);
    }

    /**
     * Render the list.
     *
     * @param array $list
     * @param bool $ol
     * @param bool $nowrap
     * @param string $class
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function renderAsList($list, $ol = false, $nowrap = false, $class = '')
    {
        return view('admin::layouts.table.ul', [
            'slot' => $list, 'ol' => $ol, 'nowrap' => $nowrap, 'class' => $class
        ]);
    }

    /**
     * Get a translation for the given element key.
     *
     * @param string $key
     * @param array $replace
     * @param bool $empty
     * @param string $locale
     * @return string
     */
    public static function transOfElement($key = null, $replace = [], $empty = false, $locale = null)
    {
        return static::trans(static::$translationOfElements, $key, $replace, $locale, $empty);
    }
}
