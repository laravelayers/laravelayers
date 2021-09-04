'use strict';

import $ from 'jquery';

/**
 * FormCheckbox plugin.
 * @module foundation.FormCheckbox
 */
class FormCheckbox {
    /**
     * Creates a new instance of FormCheckbox.
     * @class
     * @name FormCheckbox
     * @fires FormCheckbox#init
     * @param {Object} element - jQuery object to add the trigger to.
     * @param {Object} options - Overrides to the default plugin settings.
     */
    constructor(element, options = {}) {
        this.$element = element;
        this.options  = $.extend(true, {}, FormCheckbox.defaults, this.$element.data(), options);

        this.className = 'FormCheckbox'; // ie9 back compat
        this._init();

        Foundation.registerPlugin(this, this.className);
    }

    /**
     * Initializes the FormCheckbox plugin.
     * @private
     */
    _init() {
        this._events();
    }

    /**
     * Initializes events for FormCheckbox.
     * @private
     */
    _events() {
        this.selectAll();
    }

    /**
     * Select all checkboxes.
     */
    selectAll() {
        if ($(this.$element).prop('tagName') === 'TABLE') {
            this.htmlCheckboxAll = $(this.$element).find(
                this.options.htmlCheckboxAll ? this.options.htmlCheckboxAll :  'thead input:checkbox'
            );

            if (!this.options.allowDisabled) {
                this.htmlCheckboxAll = this.htmlCheckboxAll.not(":disabled");
            }

            this.htmlCheckbox = $(this.$element).find(
                this.options.htmlCheckbox ? this.options.htmlCheckbox : 'tbody td:first-child input:checkbox'
            );
        } else {
            this.htmlCheckboxAll = $(this.$element).find(
                this.options.htmlCheckboxAll ? this.options.htmlCheckboxAll : 'input:checkbox'
            ).first();

            if (!this.options.allowDisabled) {
                this.htmlCheckboxAll = this.htmlCheckboxAll.not(":disabled");
            }

            this.htmlCheckbox = $(this.$element).find(
                this.options.htmlCheckbox ? this.options.htmlCheckbox : 'input:checkbox:not(:first)'
            );
        }

        if (this.htmlCheckbox !== undefined) {
            if (!this.options.allowDisabled) {
                this.htmlCheckbox = this.htmlCheckbox.not(":disabled");
            }

            if (this.htmlCheckboxAll.attr('type') === undefined) {
                this.htmlCheckbox.attr('type', 'radio').first().prop('checked', true);

                this.htmlCheckbox.on('change.foundation.formCheckboxOne', (e) => {
                    this.htmlCheckbox.not(e.target).prop('checked', false);
                });
            } else {
                this.htmlCheckboxAll.on('click.foundation.formCheckbox', () => {
                    if (this.htmlCheckboxAll.is(":checked")) {
                        this.htmlCheckbox.prop('checked', true);
                    } else {
                        this.htmlCheckbox.prop('checked', false);
                    }
                });
            }
        }
    }

    /**
     * Destroys an instance of FormCheckbox.
     * @function
     */
    _destroy() {
        Foundation.unregisterPlugin(this, this.className);
    }
}

/**
 * Default settings for plugin
 */
FormCheckbox.defaults = {
    /**
     * HTML element for the checkbox to select all checkboxes.
     * @option
     * @type {string} - Example: "table" or "thead input:checkbox".
     * @default ''
     */
    htmlCheckboxAll: '',

    /**
     * HTML element for one of all selected checkboxes.
     * @option
     * @type {string} - Example: "" or "tbody td:first-child input:checkbox".
     * @default ''
     */
    htmlCheckbox: '',

    /**
     * Allow to select disabled checkboxes.
     * @option
     * @type {boolean}
     * @default true
     */
    allowDisabled: false
};

export {FormCheckbox};
