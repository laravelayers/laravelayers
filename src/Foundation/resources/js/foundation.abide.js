'use strict';

import $ from 'jquery';
import { AsYouType, parsePhoneNumberFromString} from 'libphonenumber-js/max';
import validator from 'validator';

/**
 * Configuring plugin {@see Foundation.Abide}.
 */
;(function () {
    Foundation.Abide.defaults.liveValidate = true;

    /**
     * Initialization function for all custom validators.
     * @returns {*}
     * @private
     */
    Foundation.Abide.defaults.validators['_init'] = function (
        $el,      /* jQuery element to validate */
        required, /* is the element required according to the `[required]` attribute */
        parent    /* parent of the jQuery element `$el` */
    ) {
        var $init = {
            'isGood': false,
            'options': $el.data('validatorOptions') || {}
        };

        if (!$el.attr('required') && $el.val() === '') {
            $init.isGood = true;
        }

        if ($el.data('validator').toLowerCase() === '_init') {
            return $init.isGood;
        }

        if (typeof $init.options === 'string') {
            $init.options = $init.options.trim();

            if ($init.options.search(/^\/.*\/[a-z]?$/i) !== -1) {
                let flags = $init.options.replace(/.*\/([gimy]*)$/, '$1');
                let pattern = $init.options.replace(new RegExp('^/(.*?)/'+flags+'$'), '$1');

                $init.options = new RegExp(pattern, flags);
            }
            else if ($init.options.search(/^\{.*\}$/) !== -1) {
                $init.options = $init.options.replace(/^\{(.*)\}$/, '$1');

                let $options = $init.options.split(',');

                $init.options = '';

                for (let i = 0; i < $options.length; i++) {
                    let name = $options[i].split(':');

                    name[0] = `"${name[0].replace(/'/g, '').trim()}"`;
                    name[1] = name[1].trim();

                    if (name[1].charAt(0) === "'") {
                        name[1] = `"${name[1].replace(/'/g, '').trim()}"`;
                    }

                    $init.options += ($init.options ? ',' : '') + `${name[0]}:${name[1]}`;
                }

                $init.options = JSON.parse(`{${$init.options}}`);
            }
        }

        return $init;
    };

    /**
     * Check if the string is a phone number.
     * See {@link https://github.com/googlei18n/libphonenumber}.
     * See {@link https://github.com/catamphetamine/libphonenumber-js}.
     * @returns {boolean}
     */
    Foundation.Abide.defaults.validators['phone'] = function (
        $el,      /* jQuery element to validate */
        required, /* is the element required according to the `[required]` attribute */
        parent    /* parent of the jQuery element `$el` */
    ) {
        let $init = Foundation.Abide.defaults.validators['_init']($el, required, parent);

        if ($init.isGood) {
            return true;
        }

        $init.options.country = ($init.options.country !== undefined
            ? $init.options.country
            : window.Laravel.country)
            .toUpperCase();

        $init.options.isMobilePhone = !!$init.options.isMobilePhone;
        $init.options.isNotTollFreePhone = $init.options.isNotTollFreePhone !== false;

        const asYouType = new AsYouType($init.options.country);

        if (!$init.options.country) {
            $el.val('+' + $el.val().replace(/\+/, ''));
        }

        $el.val(asYouType.input($el.val()));

        const phoneNumber = parsePhoneNumberFromString($el.val(), $init.options.country);

        if (phoneNumber && phoneNumber.isValid()) {
            let isValid = true;

            if ($init.options.isMobilePhone && phoneNumber.getType() !== 'MOBILE'
                || $init.options.isNotTollFreePhone && phoneNumber.getType() === 'TOLL_FREE'
            ) {
                isValid = false;
            }

            if ($init.options.country && $init.options.country !== phoneNumber.country) {
                isValid = false;
            }

            if (isValid) {
                if (phoneNumber.getType() !== 'TOLL_FREE') {
                    $el.val(phoneNumber.formatInternational());
                }

                if ($init.options.country) {
                    $el.prop('maxlength', $el.val().length);
                }

                return true;
            }
        }

        return false;
    };

    /**
     * Check if the string is a url.
     * See {@link https://github.com/chriso/validator.js}.
     * @returns {boolean}
     */
    Foundation.Abide.defaults.validators['url'] = function (
        $el,      /* jQuery element to validate */
        required, /* is the element required according to the `[required]` attribute */
        parent    /* parent of the jQuery element `$el` */
    ) {
        let $init = Foundation.Abide.defaults.validators['_init']($el, required, parent);

        if ($init.isGood) {
            return true;
        }

        if ($init.options.require_protocol === undefined) {
            $init.options.require_protocol = true;
        }

        $el.val($el.val().toLowerCase());

        if ($init.options.require_protocol && $el.val().search(/(^\w+:|^)\/\//) < 0) {
            if (($el.val().length > 4 && $el.val().search(/^(http|ftp:)/) < 0)
                || $el.val().length > 7) {

                let protocol = $init.options.protocols === undefined
                    ? 'https'
                    : $init.options.protocols[0];

                $el.val(`${protocol}://${$el.val().replace(/(\w+|^):?\/\/?/, '')}`);
            }
        }

        return (validator['isURL']($el.val(), $init.options));
    };

    /**
     * Check if the string is a number.
     * See {@link https://github.com/chriso/validator.js}.
     * @returns {boolean}
     */
    Foundation.Abide.defaults.validators['number'] = function (
        $el,      /* jQuery element to validate */
        required, /* is the element required according to the `[required]` attribute */
        parent    /* parent of the jQuery element `$el` */
    ) {
        let $init = Foundation.Abide.defaults.validators['_init']($el, required, parent);

        if ($init.isGood) {
            return true;
        }

        $init.options.min = $init.options.min || 0;
        $init.options.max = $init.options.max || 999;

        if (Number($el.val()) < $init.options.min) {
            $el.val($init.options.min);
        }
        if (Number($el.val()) > $init.options.max) {
            $el.val($init.options.max);
        }

        return (validator['isInt']($el.val(), $init.options));
    };

    /**
     * Check if the string is the type of the specified validator.
     * See {@link https://github.com/chriso/validator.js}.
     * @returns {boolean}
     */
    Foundation.Abide.defaults.validators['validator'] = function (
        $el,      /* jQuery element to validate */
        required, /* is the element required according to the `[required]` attribute */
        parent    /* parent of the jQuery element `$el` */
    ) {
        let $init = Foundation.Abide.defaults.validators['_init']($el, required, parent);

        if ($init.isGood) {
            return true;
        }

        let validatorName = $el.data('validatorName').replace(/^is/i, '');

        if (!validator[validatorName]) {
            validatorName = 'is' + validatorName.charAt(0).toUpperCase() + validatorName.substr(1);
        }

        return (validator[validatorName]($el.val(), $init.options));
    };

    /**
     * Scroll the page to the first field with an error.
     */
    Foundation.Abide.prototype.scrollToError = function() {
        this.$element.on('submit.zf.abide', (event) => {
            let id = $(event.target).find('.is-invalid-input').first().attr('id') || 0;

            if (id) {
                let label_id = '#label_' + id;

                let top = $(label_id).length
                    ? $(label_id).offset().top
                    : $('#' + id).offset().top;

                let $topBar = $('.sticky');

                if ($topBar.hasClass('is-at-top') && $topBar.height() && $topBar.is(":visible")) {
                    top -= $topBar.height();
                }

                $('html, body').animate({scrollTop: top}, 400);
            }
        });
    };

    /**
     * The form field must be present and not empty if the another field field is empty.
     * @param field
     * @param another
     */
    Foundation.Abide.prototype.requiredIfNot = function(field, another) {
        let $field = $('#' + field);
        let $another = $('#' + another);

        $(document).ready(function() {
            if ($field.val()) {
                $field.change();
            }
        });

        $field.change(function() {
            if ($(this).val()) {
                $another.removeAttr('required');

                if (!$another.val()) {
                    $another.change();
                }
            } else {
                $another.attr('required', 'required');
            }
        });
    };

    /**
     * Add a hidden field at the end of the form with the name and value of the button that was clicked to fix the bug.
     * TODO: TODO-WHEN-UPDATING-FOUNDATION
     * @see https://github.com/foundation/foundation-sites/issues/12066
     */
    Foundation.Abide.prototype.saveButtonValue = function() {
        if (this.$submits) {
            this.$submits.off('click.zf.abide keydown.zf.abide')
                .on('click.zf.abide keydown.zf.abide', (e) => {
                    if (!e.key || (e.key === ' ' || e.key === 'Enter')) {
                        $(e.currentTarget).closest("form").append(
                            $('<input>').attr('type', 'hidden')
                                .attr('name', $(e.currentTarget).attr('name'))
                                .attr('value', $(e.currentTarget).attr('value'))
                        );

                        e.preventDefault();

                        this.formnovalidate = e.target.getAttribute('formnovalidate') !== null;

                        this.$element.submit();
                    }
                });
        }
    };
}());
