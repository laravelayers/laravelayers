'use strict';

import $ from 'jquery';

/**
 * FormDatetime plugin.
 * @module foundation.formDatetime
 */
class FormDatetime {
    /**
     * Creates a new instance of FormDatetime.
     * @class
     * @name FormDatetime
     * @fires FormDatetime#init
     * @param {Object} element - jQuery object to add the trigger to.
     * @param {Object} options - Overrides to the default plugin settings.
     */
    constructor(element, options = {}) {
        this.$element = element;
        this.options  = $.extend(true, {}, FormDatetime.defaults, this.$element.data(), options);

        this.className = 'FormDatetime'; // ie9 back compat

        this._init();

        Foundation.registerPlugin(this, this.className);
    }

    /**
     * Initializes the FormDatetime plugin.
     * @private
     */
    _init() {
        this.$input = this.$element.prop('tagName') === 'INPUT'
            ? this.$element
            : this.$element.find('input');

        this.datetimePicker();
    }

    /**
     * Call the datetime picker function.
     * See {@link https://github.com/flatpickr/flatpickr}.
     */
    datetimePicker() {
        if (typeof window.flatpickr === 'undefined') {
            require('flatpickr');
        }

        if (typeof flatpickr.l10ns[this._getOptions().locale] === 'undefined') {
            console.log(
                "FormDatetime: All the flatpickr module locale files are loaded. " +
                `Because it is not loaded locale ${this._getOptions().locale}.`
            );

            require('flatpickr/dist/l10n/');
        }

        this.$picker = this.$input.flatpickr(this._getOptions());
    }

    /**
     * Get the configuration parameters.
     * @private
     * @returns {object}
     */
    _getOptions() {
        var $options = {};

        $options.locale = this.options.lang
            ? this.options.lang
            : (window.Laravel.lang || document.documentElement.lang);

        if (this.options.dateFormat) {
            $options.dateFormat = this.options.dateFormat;

            if (!this.options.altFormat) {
                this.options.altFormat = $options.dateFormat;
            }
        }

        $options.altInput = true;
        $options.altFormat = this.options.altFormat;

        if (this.options.defaultDate) {
            $options.defaultDate = typeof this.options.defaultDate === 'string'
                ? this.options.defaultDate.split(',')
                : this.options.defaultDate;
        }

        if (!this.options.enableDate) {
            $options.noCalendar = true;
        }

        if (this.options.enableTime) {
            $options.enableTime = true;
            $options.time_24hr = true;

            if (this.options.enableSeconds) {
                $options.enableSeconds = true;
            }
        }

        if (this.options.weekNumbers || $options.altFormat === 'W') {
            $options.weekNumbers = true;
        }

        if (this.options.minDate) {
            $options.minDate = this.options.minDate;
        }

        if (this.options.maxDate) {
            $options.maxDate = this.options.maxDate;
        }

        if (this.options.minTime) {
            $options.minTime = this.options.minTime;
        }

        if (this.options.maxTime) {
            $options.maxTime = this.options.maxTime;
        }

        if (this.options.multipleDates) {
            $options.mode = 'multiple';
        }

        if (this.options.datesSeparator) {
            $options.conjunction = this.options.datesSeparator;
        }

        if (this.options.dateRange) {
            $options.mode = 'range';
        }

        if (this.options.disableDates) {
            $options.disable = typeof this.options.disableDates === 'string'
                ? this.options.disableDates.split(',')
                : this.options.disableDates;
        }

        if (this.options.enableDates) {
            $options.enable = typeof this.options.enableDates === 'string'
                ? this.options.enableDates.split(',')
                : this.options.enableDates;
        }

        if (typeof this.options.pickerOptions === 'object') {
            $.extend($options, this.options.pickerOptions);
        }

        return $options;
    }

    /**
     * Destroys an instance of FormDatetime.
     * @function
     */
    _destroy() {
        var val = this.$input.val();

        this.$picker.destroy();

        this.$input.val(val);

        Foundation.unregisterPlugin(this, this.className);
    }
}

/**
 * Default settings for plugin
 */
FormDatetime.defaults = {
    /**
     * Manual localization.
     * @option
     * @type {string}
     * @default ''
     */
    lang: '',

    /**
     * The original date and time format.
     * @option
     * @type {string}
     * @default ''
     */
    dateFormat: '',

    /**
     * The displayed date and time format.
     * @option
     * @type {string}
     * @default ''
     */
    altFormat: '',

    /**
     * Date or list of default dates separated by commas.
     * @option
     * @type {string}
     * @default ''
     */
    defaultDate: '',

    /**
     * Enable date picker.
     * @option
     * @type {boolean}
     * @default true
     */
    enableDate: true,

    /**
     * Enable time picker.
     * @option
     * @type {boolean}
     * @default true
     */
    enableTime: true,

    /**
     * Enables seconds in the time picker.
     * @option
     * @type {boolean}
     * @default true
     */
    enableSeconds: false,

    /**
     * Enables display of week numbers in calendar.
     * @option
     * @type {boolean}
     * @default true
     */
    weekNumbers: false,

    /**
     * Minimum date.
     * @option
     * @type {string}
     * @default ''
     */
    minDate: '',

    /**
     * Maximum date.
     * @option
     * @type {string}
     * @default ''
     */
    maxDate: '',

    /**
     * Minimum time.
     * @option
     * @type {string}
     * @default ''
     */
    minTime: '',

    /**
     * Maximum time.
     * @option
     * @type {string}
     * @default ''
     */
    maxTime: '',

    /**
     * Allow multiple dates to be selected.
     * @option
     * @type {boolean}
     * @default false
     */
    multipleDates: false,

    /**
     * Separator for multiple dates.
     * @option
     * @type {string}
     * @default ''
     */
    datesSeparator: '',

    /**
     * Allow selection of date range.
     * @option
     * @type {boolean}
     * @default false
     */
    dateRange: false,

    /**
     * List of disabled dates separated by commas.
     * @option
     * @type {string}
     * @default ''
     */
    disableDates: '',

    /**
     * List of enabled dates separated by commas.
     * @option
     * @type {string}
     * @default ''
     */
    enableDates: '',

    /**
     * List of options in JSON format.
     * @option
     * @type {string}
     * @default ''
     */
    pickerOptions: ''
};

export {FormDatetime};
