'use strict';

import $ from 'jquery';
import { MediaQuery } from 'foundation-sites/js/foundation.util.mediaQuery';

/**
 * FormSelect module.
 * @module foundation.formSelect
 */
class FormSelect {
    /**
     * Creates a new instance of FormSelect.
     * @class
     * @name FormSelect
     * @fires FormSelect#init
     * @param {Object} element - jQuery object to add the trigger to.
     * @param {Object} options - Overrides to the default plugin settings.
     */
    constructor(element, options = {}) {
        this.$element = element;
        this.options  = $.extend(true, {}, FormSelect.defaults, this.$element.data(), options);

        this.className = 'FormSelect'; // ie9 back compat

        this._init();

        Foundation.registerPlugin(this, this.className);
    }

    /**
     * Initializes the FormSelect plugin.
     * @private
     */
    _init() {
        MediaQuery._init();

        this.$input = this.$element.prop('tagName') === 'INPUT'
            ? this.$element
            : this.$element.find('input');

        this.inputName = this.$input.attr('name');
        this.$input.removeAttr('name');

        this.$container = $('#container_' + this.$input.attr('id'));

        this.$wrapper = $('#wrapper_' + this.$input.attr('id'));
        this.$search = $(this.$wrapper).children('input[type="search"]').first();

        this.$callout = $(this.$wrapper).children('.callout');

        this.$list = $(this.$wrapper).children('ul');

        this.isChanged = false;

        if (typeof this.options.disabledIds === 'string' || typeof this.options.disabledIds === 'number') {
            this.options.disabledIds = String(this.options.disabledIds).split(',');
        }

        if (this.options.ajaxUrl) {
            this._ajax();
        } else {
            this._delayedInit();
            this._add();
        }
    }

    /**
     * Delayed initialization.
     * @param {boolean} status
     * @private
     */
    _delayedInit(status = false) {
        this.isDelayedInit = status;

        this._createPlaceholder();

        this._createLinkToAdd();

        this._createElementsContainer();

        this._events();
    }

    /**
     * Get elements using ajax.
     * @private
     */
    _ajax() {
        this.$wrapper.children('.preloader').removeClass('hide');
        this.$list.html('');

        let data = (this.options.multiple && this.options.ajaxUrl.search(/[?|&]+multiple=/i) === -1) ? 'multiple=1' : '';

        if (this.options.ajaxUrl.search(/[?|&]+name=/i) === -1) {
            let namePattern = new RegExp(/^([^\[]+)[\[]([^\]]+)[\]][\[]([^\]]+)[\]].*/);

            if (this.inputName.search(namePattern) !== -1) {
                data += '&prefixName=' + this.inputName.replace(namePattern, "$1");
                data += '&prefix=' + this.inputName.replace(namePattern, "$2");
                data += '&name=' + this.inputName.replace(namePattern, "$3");

                var inputName = this.inputName.replace(namePattern, "$1")
                    + '[' + this.inputName.replace(namePattern, "$2") + ']'
                    + '[' + this.inputName.replace(namePattern, "$3") + '_text]';
            } else {
                var inputName = this.inputName.replace(/^([^\[]+).*/, "$1");

                data += '&name=' + inputName;
            }
        }

        if (this.options.allowInputName && this.$input.parents('form').attr('method').toLowerCase() === 'post') {
            this.$input.attr('name', inputName);
        }

        let _this = this;

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: _this.options.ajaxUrl,
            data: data,
            dataType: "html"
        }).done(function (data) {
            _this.$wrapper.children('.preloader').detach();

            let $list = $(data).closest('.menu');

            if (!$list.length) {
                $list = $(data).find('.menu').first();

                if (!$list.length) {
                    $list = $(data).find('ul').first();
                }
            }

            $list.find('input').attr('data-abide-ignore', '');

            let listClass = $list.attr('class').split(' ');

            for (var i = 0; i < listClass.length; i++) {
                _this.$list.addClass(listClass[i]);
            }

            _this.$list.html($list.html());

            if (!_this.options.linkToCreate) {
                _this.options.linkToCreate = $list.attr('data-link-to-create') || _this.options.linkToCreate;
            }

            if (!_this.options.linkTextToCreate) {
                _this.options.linkTextToCreate = $list.attr('data-link-text-to-create') || _this.options.linkTextToCreate;
            }

            let isOpen = !_this.$container.is(':hidden');

            _this._delayedInit(true);

            if (_this.$input.val()) {
                _this._change();
            } else {
                _this._add();
            }

            _this.isDelayedInit = false;

            if (isOpen) {
                _this.$container.foundation('close').foundation('open');
            }
        });
    }

    /**
     * Create a placeholder.
     * @private
     */
    _createPlaceholder() {
        if (!this.$input.attr('placeholder')) {
            this.$input.attr('placeholder', this.options.placeholder);
        }

        this.$header = $('#' + this.$container.attr('aria-labelledby'));

        var $label = this.$header.children('label');

        var text = $label.text();

        if (this.options.containerHeader) {
            text = String(this.options.containerHeader);
        }

        $label.text(text.toUpperCase());
    }

    /**
     * Create a link to add a new element.
     * @private
     */
    _createLinkToAdd() {
        if (this.options.linkToCreate) {
            let $link = this.$callout.find('a.hide');

            $link.removeClass('hide').attr('href', this.options.linkToCreate);

            if (this.options.linkTextToCreate) {
                $link.text(this.options.linkTextToCreate);
            }
        }
    }

    /**
     * Create a container to add the selected elements to the form.
     * @private
     */
    _createElementsContainer() {
        var id = 'from_' + this.$container.attr('id');

        this.$element.parent().after($('<span>', { id: id}));

        this.$from = $('#' + id);
    }

    /**
     * Initializes events for FormSelect.
     * @private
     */
    _events() {
        var _this = this;

        this.$container.on('closed.zf.reveal', function(event) {
            _this.$search.val('').trigger('input.foundation.formSelect.search');
        });

        this.$header.children('input').on('click.foundation.formSelect.search', () => {
            if (this.options.multiple) {
                _this._selectAll();
            }
        });

        this.$search.on('click.foundation.formSelect.search', () => {
            _this._show(_this.$search.val());
        });

        this.$search.on('dblclick.foundation.formSelect.search', () => {
            _this._show(_this.$search.val(), false);
        });

        this.$search.on('input.foundation.formSelect.search', () => {
            _this._find(_this.$search.val());
        });

        this.$list.find('li').each(function(i, element) {
            _this._select(element);
        });

        this.$input.on('mousedown.foundation.formSelect.button', (event) => {
            if(event.button == 2) {
                _this.$input.select();
            }
        }).on('input.foundation.formSelect.button', () => {
            if (this.$input.val()) {
                _this._change();
            }
        }).on('change.foundation.formSelect.button', () => {
            if (!_this.$input.val()) {
                _this._change();
            }
        });
    }

    /**
     * Find items by value entered search field.
     * @param {string} value
     * @private
     */
    _find(value) {
        let isResult = false;

        this.$list.find('li').each(function (i, data) {
            if ($(data).text().toUpperCase().indexOf(value.toUpperCase()) < 0) {
                $(data).addClass('is-hidden');
            } else {
                $(data).removeClass('is-hidden');

                var $text = $(data).children('a');

                if (value && $text.text().toUpperCase().indexOf(value.toUpperCase()) <= 0) {
                    $text.find('label').css('opacity', '0.5');
                } else {
                    $text.find('label').css('opacity', '');
                }

                isResult = true;
            }
        });

        if (!isResult) {
            this.$callout.removeClass('is-hidden');
        } else {
            this.$callout.addClass('is-hidden');
        }
    }

    /**
     * Show selected items or all if no search query is entered.
     * @param {string} value
     * @param {boolean} selected
     * @private
     */
    _show(value, selected = true) {
        if (!value) {
            if (selected) {
                if (this.$list.find('input:checked').length) {
                    this.$list.find('li').each(function (i, data) {
                        if (!$(data).find('input').is(':checked')) {
                            $(data).addClass('is-hidden');
                        }
                    });
                }
            } else {
                this.$list.find('li.is-hidden').removeClass('is-hidden');
            }
        }
    }

    /**
     * Select a list element.
     * @param {object} element
     * @private
     */
    _select(element) {
        var _this = this;

        var $link = $(element).children('a');
        var $input = $link.find('input');
        var $label = $link.find('label');
        var $children = $(element).find('li');

        if ($(element).hasClass('is-subtree-parent')) {
            if (!this.options.selectParent) {
                $(element).addClass('is-disabled');
                $input.hide().prop('checked', false);
            }
        }

        if (this.options.disabledIds.includes($input.val())) {
            $(element).addClass('is-disabled');
            $input.attr('disabled', true).prop('checked', false);
        }

        $input.on('click.foundation.formSelect.checkbox', () => {
            $input.prop('checked', !$input.is(":checked"));
        });

        $label.on('click.foundation.formSelect.label', (event) => {
            if ($(element).hasClass('is-subtree-parent') && _this.options.multiple) {
                var $visibleChildren = $children.not(':hidden').not('.is-disabled');
                var $childrenInput = $visibleChildren.find('input').not(':hidden');

                if ($visibleChildren.find('input:checked').length) {
                    $visibleChildren.removeClass('active');
                    $childrenInput.prop('checked', false);
                } else {
                    $visibleChildren.addClass('active');
                    $childrenInput.prop('checked', true);
                }
            } else {
                event.preventDefault();
            }
        });

        $link.on('click.foundation.formSelect.element', () => {
            if (!_this.options.multiple) {
                _this.$list.find('li').filter('.active').removeClass('active');
            }

            if (_this.options.selectParent || !$(element).is(':has(ul)')) {
                if ($input.is(':checked')) {
                    $input.prop('checked', false);
                    $(element).removeClass('active');
                }
                else {
                    if (!$(element).hasClass('is-disabled')) {
                        $input.prop('checked', true);
                        $(element).addClass('active');
                    }
                }
            }

            _this._add();

            _this.$input.change();

            if (!_this.options.multiple && $input.is(":checked") && !_this.isDelayedInit) {
                $('#' + _this.$container.attr('id')).foundation('close');
            }
        });
    }

    /**
     * Select all elements in the list.
     * @private
     */
    _selectAll() {
        this.$list.find('li').not('.is-disabled').children('a').click();
    }

    /**
     * Change the text field containing the list of selected elements.
     * @private
     */
    _change() {
        var values = this.$input.val().split(this.options.listSeparator);

        if (this.$list) {
            let selectById = this.options.selectById;

            this.$list.find('li').filter('.active').children('a').click();

            for (var i = 0; i < values.length; i++) {
                this.$list.find('li').each(function (key, element) {
                    let $link = $(element).children('a'),
                        value = values[i].trim().toUpperCase();

                    if ($link.text().trim().toUpperCase() === value
                        || (selectById && $link.find('input').val() === value)
                    ) {
                        $link.click();
                    }
                });
            }
        }
    }

    /**
     * Add selected elements to the form.
     * @private
     */
    _add() {
        var $active = this.$wrapper.find('li.active').not('.is-disabled');

        if (this.isChanged) {
            this.$element.parents('form').attr('data-unsaved', true);
        }

        if ($active.length) {
            var values = '';

            var listSeparator = this.options.listSeparator;

            $active.each(function(i, value) {
                values = (values ? values + listSeparator + ' ' : '')
                    + $(value).children('a').text().trim();
            });

            this.$input.val(values);

            $active = $active.children('a').find('input').clone();

            $active.each(function(index, element){
                $(element).attr('id', $(element).attr('id'))
                    .attr('type', 'hidden')
                    .removeAttr('checked');
            });

            $(this.$from).html($active);
        } else {
            if (this.$input.val()) {
                this.$input.val('');
            }

            $(this.$from).html('');
        }

        this.isChanged = true;
    }

    /**
     * Destroys an instance of FormSelect.
     * @function
     */
    _destroy() {
        Foundation.unregisterPlugin(this, this.className);
    }
}

/**
 * Default settings for plugin
 */
FormSelect.defaults = {
    /**
     * Allow select parent items.
     * @option
     * @type {boolean}
     * @default true
     */
    selectParent: true,

    /**
     * Multiple selection.
     * @option
     * @type {boolean}
     * @default true
     */
    multiple: false,

    /**
     * Item IDs, separated by commas, to be disabled for selection.
     * @option
     * @type {string}
     * @default ''
     */
    disabledIds: '',

    /**
     * Allow the selection of elements by the ID specified in the text field.
     * @option
     * @type {boolean}
     * @default false
     */
    selectById: true,

    /**
     * The separator of the list of selected elements in the text field.
     * @option
     * @type {string}
     * @default ','
     */
    listSeparator: ',',

    /**
     * Text for the header of the container if the element label is empty.
     * @option
     * @type {string}
     * @default ''
     */
    containerHeader: '',

    /**
     * Link to create a new list item.
     * @option
     * @type {string}
     * @default ''
     */
    linkToCreate: '',

    /**
     * Link text to create a new list item.
     * @option
     * @type {string}
     * @default ''
     */
    linkTextToCreate: '',

    /**
     * URL to load data using Ajax.
     * @option
     * @type {string}
     * @default ''
     */
    ajaxUrl: '',

    /**
     * Allow the use of the "name" attribute for the text field.
     * @option
     * @type {boolean}
     * @default false
     */
    allowInputName: false,

    /**
     * Default placeholder for the text field.
     * @option
     * @type {string}
     * @default '...'
     */
    placeholder: '...',
};

export {FormSelect};
