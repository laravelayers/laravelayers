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
};

export {FormSearch};
