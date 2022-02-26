'use strict';

import $ from 'jquery';
import { MediaQuery } from 'foundation-sites/js/foundation.util.mediaQuery';

/**
 * FormSearch module.
 * @module foundation.formSearch
 */
class FormSearch {
    /**
     * Creates a new instance of FormSearch.
     * @class
     * @name FormSearch
     * @fires FormSearch#init
     * @param {Object} element - jQuery object to add the trigger to.
     * @param {Object} options - Overrides to the default plugin settings.
     */
    constructor(element, options = {}) {
        this.$element = element;
        this.options  = $.extend(true, {}, FormSearch.defaults, this.$element.data(), options);

        this.className = 'FormSearch'; // ie9 back compat

        this._init();

        Foundation.registerPlugin(this, this.className);
    }

    /**
     * Initializes the FormSearch plugin.
     * @private
     */
    _init() {
        MediaQuery._init();

        this.$input = this.$element.prop('tagName') === 'INPUT'
            ? this.$element
            : this.$element.find('input');

        this.$form = this.$element.parents('form');

        this._createWrapper();

        this.$pane = this.$wrapper.find('.dropdown-pane');

        this.$pane.children().css('margin', '-0.5rem');

        this._events();
    }

    /**
     * Initializes events for FormSearch.
     * @private
     */
    _events() {
        var _this = this;

        this.$input.on('click.foundation.formSearch.search', (event) => {
            if (!_this.$wrapper.hasClass('active')) {
                event.stopPropagation();
            }
        });

        this.$input.on('input.foundation.formSearch.search', () => {
            _this._find(_this.$input.val());
        });
    }

    /**
     * Create a wrapper.
     * @private
     */
    _createWrapper() {
        this.$wrapper = this.$element.parents('.form-search-wrapper');

        if (!this.$wrapper.length) {
            let parent = this.$input.parent();

            if (parent.hasClass('input-group')) {
                parent = parent.parent();
            }

            parent.wrap('<div class="form-search-wrapper"></div>');

            this.$wrapper = parent.parent('.form-search-wrapper');
        }
    }

    /**
     * Find items by value entered search field.
     * @param {string} value
     * @private
     */
    _find(value) {
        if (!this.options.ajaxUrl) {
            this.options.ajaxUrl = this.$form.attr('action')
                ? this.$form.attr('action')
                : window.location.href;
        }

        let values = value.split(this.options.listSeparator.trim());

        values = values.map(function(item) {
            return item.trim();
        });

        if (values.length > 1) {
            value = values[values.length-1];
        }

        values.splice(values.length-1);

        this.text = values.join(this.options.listSeparator).trim();

        let _this = this;

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: this.options.ajaxUrl,
            data: 'text=' + value,
            dataType: "html"
        }).done(function (data) {
            _this.$pane.empty();

            if (data) {
                if (!_this.$wrapper.hasClass('active')) {
                    _this.$wrapper.addClass('active');
                    _this.$pane.foundation('open');
                }

                _this.$pane.prepend(data);

                _this.$pane.children('.menu').css('margin', _this.options.searchPaneMargin);

                if (_this.options.searchElement) {
                    _this.$pane.find(_this.options.searchElement).each(function (index, element) {
                        if (!$(this).find('a').length) {
                            $(this).html('<a>' + $(this).html() + '</a>');
                        }

                        $(this).on('click', function () {
                            let text = $(this).text().trim();

                            let duplicate = _this.text.search(
                                new RegExp(
                                    '(' + _this.options.listSeparator.trim() + ')?[\s]?'
                                    + text + '[\s]?(' + _this.options.listSeparator.trim() + ')?',
                                    'i'
                                )
                            );

                            if (_this.text) {
                                if (duplicate === -1) {
                                    _this.text += _this.options.listSeparator + text;
                                }
                            } else {
                                _this.text = text;
                            }

                            _this.$input.val(_this.text);
                            _this.$wrapper.removeClass('active');
                            _this.$pane.foundation('close');
                        });
                    });
                }
            } else {
                if (_this.$wrapper.hasClass('active')) {
                    _this.$wrapper.removeClass('active');
                    _this.$pane.foundation('close');
                }
            }
        });
    }

    /**
     * Destroys an instance of FormSearch.
     * @function
     */
    _destroy() {
        Foundation.unregisterPlugin(this, this.className);
    }
}

/**
 * Default settings for plugin
 */
FormSearch.defaults = {
    /**
     * URL to load data using Ajax.
     * @option
     * @type {string}
     * @default ''
     */
    ajaxUrl: '',

    /**
     * An element with a search text option.
     * @option
     * @type {string}
     * @default 'li'
     */
    searchElement: 'li',

    /**
     * Multiple selection.
     * @option
     * @type {boolean}
     * @default true
     */
    multiple: false,

    /**
     * The separator of the list of selected elements in the text field.
     * @option
     * @type {string}
     * @default ','
     */
    listSeparator: '/',

    /**
     * Search pane margin.
     * @option
     * @type {string}
     * @default ','
     */
    searchPaneMargin: '-0.5rem',
};

export {FormSearch};
