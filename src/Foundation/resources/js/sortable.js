'use strict';

import $ from 'jquery';
import { FormCheckbox } from "../foundation/formCheckbox";
import "jquery-ui/ui/widgets/sortable";

/**
 * Sortable plugin.
 * @module foundation.Sortable
 */
class Sortable {
    /**
     * Creates a new instance of Sortable.
     * @class
     * @name Sortable
     * @fires Sortable#init
     * @param {Object} element - jQuery object to add the trigger to.
     * @param {Object} options - Overrides to the default plugin settings.
     */
    constructor(element, options = {}) {
        this.$element = element;
        this.options  = $.extend(true, {}, Sortable.defaults, this.$element.data(), options);

        this.className = 'Sortable'; // ie9 back compat
        this._init();

        Foundation.registerPlugin(this, this.className);
    }

    /**
     * Initializes the Sortable plugin.
     * @private
     */
    _init() {
        this._events();

        new FormCheckbox(this.$element.find('table'));
    }

    /**
     * Initializes events for Sortable.
     * @private
     */
    _events() {
        if (this.options.isSortable) {
            this.sortable();
        }
    }

    /**
     * Sort table rows.
     * See {@link https://snipp.ru/view/69}.
     */
    sortable()
    {
        let $table = this.$element.find('table'),
            $thead = $table.find('thead'),
            $tbody = $table.find('tbody'),
            $tbodyTr = $tbody.find('tr'),
            $tfoot = $table.find('tfoot');

        $tbody.sortable({
            helper: function (e, ui) {
                ui.children().each(function () {
                    $(this).width($(this).width());
                });

                return ui;
            },
            items: "tr:not(.ui-sortable-disabled)",
            cancel: '.ui-sortable-disabled'
        });

        $thead.find('tr').prepend('<th><i class="icon icon-bars"></i></th>');
        $tbodyTr.not('.ui-sortable-disabled').prepend('<td><i class="icon icon-bars"></i></td>');
        $tbodyTr.filter('.ui-sortable-disabled').prepend('<td><i class="icon icon-bars secondary"></i></td>');
        $tfoot.find('tr').prepend('<td></td>');

        $tbodyTr.each(function (i, element) {
            $(element).on('mouseover.foundation.sortable.tbodyTr', (event) => {
                if (!$(event.currentTarget).hasClass('ui-sortable-disabled')) {
                    $(event.currentTarget).css('cursor', 'move');
                    $(event.currentTarget).css('cursor', 'move');
                }
            });
        });
    }

    /**
     * Destroys an instance of sortable.
     * @function
     */
    _destroy() {
        Foundation.unregisterPlugin(this, this.className);
    }
}

/**
 * Default settings for plugin
 */

Sortable.defaults = {
    /**
     * Enable sorting table rows.
     * @option
     * @default false
     */
    isSortable: false,
};

export {Sortable};
