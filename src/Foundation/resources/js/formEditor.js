'use strict';

import $ from 'jquery';
import SimpleMDE from 'simplemde';
import Quill from 'quill';

/**
 * FormEditor module.
 * @plugin foundation.formText
 */
class FormEditor {
    /**
     * Creates a new instance of FormEditor.
     * @class
     * @name FormEditor
     * @fires FormEditor#init
     * @param {Object} element - jQuery object to add the trigger to.
     * @param {Object} options - Overrides to the default plugin settings.
     */
    constructor(element, options = {}) {
        this.$element = element;
        this.options  = $.extend(true, {}, FormEditor.defaults, this.$element.data(), options);

        this.className = 'FormEditor'; // ie9 back compat
        this._init();

        Foundation.registerPlugin(this, this.className);
    }

    /**
     * Initializes the FormEditor plugin.
     * @private
     */
    _init() {
        if (['simple', 'medium', 'full'].indexOf(this.options.editorToolbar) === -1) {
            this.options.editorToolbar = '';
        }

        if (this.options.editorHeight) {
            if (typeof this.options.editorHeight === 'number' || this.options.editorHeight.search(/^[0-9]+$/) !== -1) {
                this.options.editorHeight += 'px';
            }
        }

        this.$galleryContainer = $('#gallery_container_' + this.$element.attr('id'));
        this.$editorGallery = $('#gallery_' + this.$element.attr('id'));

        this.$element.parents('label').on('click', () => {
            event.stopPropagation();
        });

        if (this.options.editorType === 'html') {
            this.htmlEditor();
        } else {
            this.markdownEditor();
        }
    }

    /**
     * Call the Markdown editor.
     * See {@link https://github.com/sparksuite/simplemde-markdown-editor}.
     */
    markdownEditor() {
        let $element = this.$element,
            $galleryContainer = this.$galleryContainer,
            $editorGallery = this.$editorGallery;

        let $editor = new SimpleMDE(this._getMarkdownOptions());

        $element.show().css('visibility', 'hidden').css('position', 'absolute').css('width', 0);

        let drawImage = function(url) {
            let image = $editor.options.insertTexts.image;

            image[1] = url + ')';

            $editor.codemirror.replaceSelection(image.join(''));
        };

        if (this.options.editorGallery) {
            let $toolbarImage = function() {
                for (var key in $editor.toolbar) {
                    if ($editor.toolbar[key].name === 'image') {
                        return $editor.toolbar[key];
                    }
                }
            }();

            $toolbarImage.action = function customFunction() {
                $galleryContainer.find('.callout').hide();
                $galleryContainer.foundation('open');
            };

            $.get(this.options.editorGalleryUrl, function (data) {
                $editorGallery.html(data).foundation();
            });

            $editorGallery.on('click.foundation.editorGallery', 'img.thumbnail', function (e) {
                event.stopPropagation();

                drawImage($(this).attr('src'));

                $galleryContainer.foundation('close');
            });
        }

        if (this.options.editorHeight) {
            $element.parent().find('.CodeMirror').css('height', this.options.editorHeight);

            if (this.options.editorHeight && this.options.editorHeight.search(/^auto$/i) === -1) {
                $element.parent().find('.CodeMirror, .CodeMirror-scroll').css('min-height', this.options.editorHeight);
            }
        }

        if ($element.css('min-width')) {
            $element.parent().find('.CodeMirror').css('min-width', $element.css('min-width'));
        }

        if ($element.hasClass('is-invalid-input')) {
            $element.parent().find('.CodeMirror').addClass('is-invalid-input');
        }

        $editor.codemirror.on("focus", function() {
            $element.parent().find('.CodeMirror').addClass('form-editor-focus');
            $element.parent().find('.editor-toolbar').addClass('form-editor-toolbar-focus');

            if ($element.parent().find('.CodeMirror').hasClass('is-invalid-input')) {
                $element.parent().find('.CodeMirror').removeClass('is-invalid-input');
            }
        });

        $editor.codemirror.on("blur", function() {
            $element.parent().find('.CodeMirror').removeClass('form-editor-focus');
            $element.parent().find('.editor-toolbar').removeClass('form-editor-toolbar-focus');

            if ($element.hasClass('is-invalid-input')) {
                $element.parent().find('.CodeMirror').addClass('is-invalid-input');
            } else {
                $element.parent().find('.CodeMirror').removeClass('is-invalid-input');
            }
        });

        $editor.codemirror.on("change", function() {
            $element.val($editor.value()).change();

            $element.parents('form').attr('data-unsaved', true);
        });
    }

    /**
     * Get the configuration parameters of the Markdown editor.
     * @private
     * @returns {object}
     */
    _getMarkdownOptions() {
        let $options = {
            element: this.$element[0],
            promptURLs: true,
            spellChecker: false,
            status: false,
        };

        if (this.options.editorToolbar) {
            $options.toolbar = [
                "bold", "italic", "strikethrough", "|", "unordered-list", "ordered-list", "|",
                "preview", "side-by-side", "fullscreen"
            ];

            if (this.options.editorToolbar !== 'simple') {
                $options.toolbar.splice(3, 0, 'heading');
                $options.toolbar.splice(5, 0, 'quote', 'code');
                $options.toolbar.splice(9, 0, '|', 'link', 'image', 'table', 'horizontal-rule');
            }

            if (this.options.editorToolbar === 'full') {
                $options.toolbar.splice(4, 0, 'heading-2', 'heading-3');
                $options.toolbar.splice(11, 0, 'clean-block');
            }
        }

        if (typeof this.options.editorOptions === 'object') {
            $.extend($options, this.options.editorOptions);
        }

        return $options;
    }

    /**
     * Call the Quill editor.
     * See {@link https://github.com/quilljs/quill}.
     */
    htmlEditor() {
        let $element = this.$element,
            $galleryContainer = this.$galleryContainer,
            $editorGallery = this.$editorGallery;

        let quillId = $element.attr('id') + '_ql';

        $element.after('<div class="ql-wrapper"><div id="' + quillId + '"></div></div>');

        quillId = '#' + quillId;

        let $editor = new Quill(quillId, this._getHtmlOptions());
        let $qlEditor = $(quillId).find('.ql-editor');

        $element.show().css('visibility', 'hidden').css('position', 'absolute').css('width', 0);

        if (this.options.editorGallery) {
            $editor.getModule('toolbar').addHandler('image', function() {
                $galleryContainer.find('.callout').hide();
                $galleryContainer.foundation('open');
            });

            $.get(this.options.editorGalleryUrl, function (data) {
                $editorGallery.html(data).foundation();
            });

            $editorGallery.on('click.foundation.editorGallery', 'img.thumbnail', function () {
                event.stopPropagation();

                $editor.insertEmbed($editor.getSelection(true).index, 'image', $(this).attr('src'));

                $galleryContainer.foundation('close');
            });
        } else {
            $editor.getModule('toolbar').addHandler('image',  function () {
                let range = this.quill.getSelection();
                let value = prompt('What is the image URL');

                if(value){
                    this.quill.insertEmbed(range.index, 'image', value, Quill.sources.USER);
                }
            });
        }

        if (this.options.editorHeight) {
            $element.parent().find('.ql-container').css('min-height', this.options.editorHeight);

            if (this.options.editorHeight && this.options.editorHeight.search(/^auto$/i) === -1) {
                $element.parent().find('.ql-editor').css('min-height', this.options.editorHeight);
                $qlEditor.addClass('ql-editor-fixed');
            }
        } else {
            $qlEditor.addClass('ql-editor-fixed');
        }

        $editor.setContents($editor.clipboard.convert($element.val()));

        if ($element.hasClass('is-invalid-input')) {
            $qlEditor.addClass('is-invalid-input');
        }

        $qlEditor.on('focus', () => {
            $qlEditor.addClass('form-editor-focus');
            $element.parent().find('.ql-toolbar').addClass('form-editor-toolbar-focus');
        });

        $qlEditor.on('blur', () => {
            $qlEditor.removeClass('form-editor-focus');
            $element.parent().find('.ql-toolbar').removeClass('form-editor-toolbar-focus');

            if ($element.hasClass('is-invalid-input')) {
                $qlEditor.addClass('is-invalid-input');
            } else {
                $qlEditor.removeClass('is-invalid-input');
            }
        });

        $editor.on('text-change', () => {
            $element.val($editor.getLength() > 1 ? $editor.root.innerHTML : '').change();

            $element.parents('form').attr('data-unsaved', true);
        });
    }

    /**
     * Get the configuration parameters of the Html editor.
     * @private
     * @returns {object}
     */
    _getHtmlOptions() {
        let toolbarOptions = [];

        if (this.options.editorToolbar) {
            toolbarOptions = [
                ['bold', 'italic', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ];

            if (this.options.editorToolbar !== 'simple') {
                toolbarOptions.push(['blockquote', 'code-block']);
                toolbarOptions.push(['link', 'image', 'video']);
                toolbarOptions.push([{ 'script': 'sub'}, { 'script': 'super' }]);
            }

            if (this.options.editorToolbar === 'full') {
                toolbarOptions[0].push('underline');
                toolbarOptions.push([{ 'indent': '-1'}, { 'indent': '+1' }]);
                toolbarOptions.push([{ 'direction': 'rtl' }, { 'align': [] }]);
                toolbarOptions.push([{ 'header': 1 }, { 'header': 2 }, { 'header': [1, 2, 3, 4, 5, 6, false] }, { 'size': ['small', false, 'large', 'huge'] }]);
                toolbarOptions.push([{ 'color': [] }, { 'background': [] }]);
                toolbarOptions.push([{ 'font': [] }]);

            }

            toolbarOptions.push(['clean']);
        }

        let $options = {
            placeholder: this.$element.attr('placeholder') !== undefined ? this.$element.attr('placeholder') : '',
            theme: 'snow',
        };

        if (toolbarOptions) {
            $options.modules = {
                toolbar: toolbarOptions,
            };
        }

        if (typeof this.options.editorOptions === 'object') {
            $.extend($options, this.options.editorOptions);

        }

        return $options;
    }

    /**
     * Destroys an instance of FormEditor.
     * @function
     */
    _destroy() {
        Foundation.unregisterPlugin(this, this.className);
    }
}

/**
 * Default settings for plugin
 */
FormEditor.defaults = {
    /**
     * Editing mode.
     * Options: 0 - markdown, 1 - html.
     * @option
     * @type {string|number}
     * @default 'markdown'
     */
    editorType: 'markdown',

    /**
     * The height of the editor's text area.
     * Options: 'auto' or number.
     * @option
     * @type {string|number}
     * @default 0
     */
    editorHeight: 0,

    /**
     * Editor toolbar.
     * Options: 0 - simple, 1 - medium, 2 - full.
     * @option
     * @type {string|number}
     * @default 'simple'
     */
    editorToolbar: 'simple',

    /**
     * List of options in JSON format.
     * @option
     * @type {Object|string}
     * @default ''
     */
    editorOptions: '',

    /**
     * Use image gallery to insert images.
     * @option
     * @type {boolean}
     * @default false
     */
    editorGallery: false,

    /**
     * URL to load image gallery data using Ajax.
     * @option
     * @type {string|Object}
     * @default window.location.href
     */
    editorGalleryUrl: window.location.href,
};

export {FormEditor};
