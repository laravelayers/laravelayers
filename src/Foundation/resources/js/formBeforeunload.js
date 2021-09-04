'use strict';

import $ from 'jquery';

/**
 * FormBeforeunload module.
 * @module foundation.beforeunload
 */
class FormBeforeunload {
    /**
     * Creates a new instance of FormBeforeunload.
     * @class
     * @name FormBeforeunload
     * @fires FormBeforeunload#init
     * @param {Object} element - jQuery object to add the trigger to.
     * @param {Object} options - Overrides to the default plugin settings.
     */
    constructor(element, options = {}) {
        this.$element = element;
        this.options  = $.extend(true, {}, FormBeforeunload.defaults, this.$element.data(), options);

        this.className = 'FormBeforeunload'; // ie9 back compat

        this._init();

        Foundation.registerPlugin(this, this.className);
    }

    /**
     * Initializes the FormBeforeunload plugin.
     * @private
     */
    _init() {
        this.$form = (() => {
            this.$form = !$(this.$element).is('form')
                ? this.$element.find('form')
                : this.$element;

            return this.$form.attr('data-unsaved', this.options.unsaved);
        })();

        this.$input = this.$form.find(':input');

        this.$reset = (() => {
            this.$reset = this.$form.find('input[type="reset"]');

            if (this.$reset.length) {
                this.$reset.attr('disabled', true);
            }

            return this.$reset;
        })();

        if (this.$form.attr('data-confirm') !== undefined) {
            if (!this.$form.attr('data-confirm')
                || this.$form.attr('data-confirm') === '0'
                || this.$form.attr('data-confirm') === 'false'
            ) {
                this.options.confirm = false;
            }
        }

        this._submit();

        this._events();
    }

    /**
     * Initializes events for FormBeforeunload.
     * @private
     */
    _events() {
        var _this = this;

        this.$input.on('input.foundation.beforeunload', () => {
            _this._change();
        });

        this.$form.on({
            'reset.foundation.beforeunload': () => {
                _this._reset();
            },
            'submit.foundation.beforeunload': () => {
                _this._unsaved(false);
            }
        });

        if (this.options.confirm) {
            $(window).on('beforeunload.foundation.beforeunload', () => {
                if (_this.$form.data('unsaved')) {
                    return _this.options.messageBeforeunload;
                }
            });
        }
    }

    /**
     * Mark that the value of the form elements has changed.
     * @private
     * @param $element
     */
    _change() {
        this._unsaved(true);

        if (this.$reset.length) {
            this.$reset.removeAttr('disabled');
        }
    }

    /**
     * Remove the mark that the value of the form elements has changed.
     * @private
     */
    _reset() {
        this._unsaved(false);

        if (this.$reset.length) {
            this.$reset.attr('disabled', true);
        }
    }

    /**
     * Automatically submit the form after closing the container.
     * @private
     */
    _submit() {
        if (this.options.formContainer && this.options.formContainerEvent) {
            var $container = this.$form.parents(this.options.formContainer);

            if ($container.length) {
                let _this = this;

                $container.bind(this.options.formContainerEvent, function () {
                    if (_this.$form.data('unsaved')) {
                        _this.$form.submit();
                    }
                });
            }
        }
    }

    /**
     * Mark that the value of the form elements has changed.
     * @param {boolean} yes
     * @private
     */
    _unsaved(yes = true) {
        this.options.unsaved = yes;
        this.$form.attr('data-unsaved', this.options.unsaved);
        this.$form.data('unsaved', this.options.unsaved);
    }

    /**
     * Destroys an instance of FormBeforeunload.
     * @function
     */
    _destroy() {
        Foundation.unregisterPlugin(this, this.className);
    }
}

/**
 * Default settings for plugin
 */
FormBeforeunload.defaults = {
    /**
     * Mark that the value of the form elements has changed.
     * @option
     * @default false
     */
    unsaved: false,

    /**
     * Call for confirmation when leaving the page if the changes are not saved.
     * @option
     * @type {boolean|string}
     * @default true
     */
    confirm: true,

    /**
     * Message before unload.
     * @option
     * @type {string}
     * @default ''
     */
    messageBeforeunload: '',

    /**
     * The class of the form container, after closing which form will be sent automatically.
     * @option
     * @type {string}
     * @default '.reveal'
     */
    formContainer: '.reveal',

    /**
     * Form container close event.
     * @option
     * @type {string}
     * @default 'closed.zf.reveal'
     */
    formContainerEvent: 'closed.zf.reveal'
};

export {FormBeforeunload};
