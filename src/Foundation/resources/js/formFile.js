'use strict';

import $ from 'jquery';
import Cropper from 'cropperjs/dist/cropper';

/**
 * FormFile plugin.
 * @plugin foundation.formFile
 */
class FormFile {
    /**
     * Creates a new instance of FormFile.
     * @class
     * @name FormFile
     * @fires FormFile#init
     * @param {Object} element - jQuery object to add the trigger to.
     * @param {Object} options - Overrides to the default plugin settings.
     */
    constructor(element, options = {}) {
        this.$element = element;
        this.options  = $.extend(true, {}, FormFile.defaults, this.$element.data(), options);

        this.className = 'FormFile'; // ie9 back compat
        this._init();

        Foundation.registerPlugin(this, this.className);
    }

    /**
     * Initializes the FormFile plugin.
     * @private
     */
    _init() {
        this.$label = $('#label_file_' + this.$element.attr('id'));

        this.$wrapper = $('#file_wrapper_' + this.$element.attr('id'));
        this.$block = $('#file_block_' + this.$element.attr('id'));
        this.$link = $('#file_link_' + this.$element.attr('id'));
        this.$preview = $('#file_preview_' + this.$element.attr('id'));

        this.$imagePreview = $('#image_preview_' + this.$element.attr('id'));
        this.$container = $('#image_container_' + this.$element.attr('id'));

        this.$imagesPreview = $('#images_preview_' + this.$element.attr('id'));
        this.$deleteImages = $('#delete_images_' + this.$element.attr('id'));

        this.$progressBar = this.$imagesPreview.find('.progress');

        this._getFileExtensions();

        if (this.$preview.length || this.$imagesPreview.length) {
            this._showPreviewFile();
        }

        this._events();
    }

    /**
     * Initializes events for FormSelect.
     * @private
     */
    _events() {
        let _this = this;

        this.$element.off().on('change.foundation.formFile.input', function(event) {
            if (!_this.$label.find('span').length) {
                _this.$label.append($('<span>', {text: '(0)'}));
            }
            _this.$label.find('span').last().text(`(${event.target.files.length})`);

            if (_this.$preview.length || _this.$imagesPreview.length) {
                _this._changeFile(event.target.files);
            } else {
                _this.$link.find('input[type=checkbox]').prop('checked', true).attr('disabled', true);
            }
        });

        this.$imagePreview.find('input').on('click.foundation.formFile.select', function(event) {
            event.stopPropagation();

            if (_this.$imagePreview.find('input:checked').length) {
                _this.$block.hide();
                _this.$deleteImages.removeClass('hide');
            } else {
                _this.$block.show();
                _this.$deleteImages.addClass('hide');
            }
        });

        this.$deleteImages.find('button').on('click.foundation.formFile.delete', function(event) {
            event.stopPropagation();
            event.preventDefault();

            _this._removeImages();
        });
    }

    /**
     * Get images using ajax.f
     * @private
     * @param data {string}
     */
    _getImages(data = '') {
        let $wrapper = this.$wrapper.parent();

        if ($(data).find('#' + this.$imagesPreview.attr('id')).length) {
            result(data)
        } else {
            $.get(this.options.ajaxUrlToGet, result);
        }

        function result(data) {
            $wrapper.find('[data-open]').each(function(i, val) {
                $('#' + $(val).attr('data-open')).parent('.reveal-overlay').remove();
            });

            $wrapper.html(data).foundation();

            $wrapper.find('.callout.success').show();
            $wrapper.find('.callout.alert').hide();
        }
    }

    /**
     * Remove images using ajax.
     * @private
     */
    _removeImages() {
        let _this = this;

        if (confirm($(this).attr('title') ? $(this).attr('title') : 'Delete?')) {
            let $preloader = _this.$imagesPreview.find('.preloader').removeClass('hide').show();

            let formData = new FormData();

            _this.$imagePreview.find('input:checked').each(function (i, val) {
                formData.append($(val).attr('name'), $(val).val());
            });

            formData.append('_method', 'PUT');

            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: _this.options.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    _this._getImages(data);
                },
                error: function (data) {
                    if (_this.options.debugAjax) {
                        console.log(data.responseJSON)
                    }

                    let $error = _this.$imagesPreview.find('.callout.alert').show();

                    $error.find('span').first().hide();
                    $error.find('.hide').removeClass('hide').show();

                    _this.$imagesPreview.find('.callout.success').hide();
                },
                complete: function () {
                    $preloader.addClass('hide').hide();
                },
            });
        }
    }

    /**
     * Change file and show preview.
     * @private
     * @param {Object} files - jQuery object of the selected files in the form.
     */
    _changeFile(files) {
        let _this = this;

        if (this.options.ajaxUrl || (this.$element.attr('multiple') !== undefined && this.$element.attr('multiple'))) {
            this.options.imageMode = 0;
        }

        if (files && files.length > 0) {
            let file = files[0];

            if (URL) {
                if (this.options.ajaxUrl) {
                    let $progressBar = this.$imagePreview.find('.progress').removeClass('hide').show();

                    let formData = new FormData();

                    for (let i = 0; i < files.length; i++) {
                        formData.append(_this.$element.attr('name'), files[i], files[i]['name']);
                    }

                    formData.append('_method', 'PUT');

                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: _this.options.ajaxUrl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        xhr: function () {
                            var xhr = new XMLHttpRequest();

                            xhr.upload.onprogress = function (e) {
                                let percent = '0';
                                let percentage = '0%';

                                if (e.lengthComputable) {
                                    percent = Math.round((e.loaded / e.total) * 100);
                                    percentage = percent + '%';

                                    $progressBar.attr('aria-valuenow', percent);
                                    $progressBar.find('.progress-meter').width(percentage);
                                    $progressBar.find('.progress-meter-text').removeClass('hide').text(percentage);
                                }
                            };

                            return xhr;
                        },
                        success: function (data) {
                            _this._getImages(data);
                        },
                        error: function (data) {
                            if (_this.options.debugAjax) {
                                console.log(data.responseJSON)
                            }

                            _this.$imagesPreview.find('.callout.alert').show();
                            _this.$imagesPreview.find('.callout.success').hide();
                        },
                        complete: function () {
                            $progressBar.addClass('hide').hide();
                        },
                    });
                } else {
                    this._showPreviewFile(file['name'], URL.createObjectURL(file));
                }
            } else if (FileReader) {
                let reader = new FileReader();

                reader.onload = function (e) {
                    _this._showPreviewFile(file['name'], reader.result);
                };

                reader.readAsDataURL(file);
            }

            this.$link.find('input[type=checkbox]').prop('checked', false).attr('disabled', true);
        }

        if (this.$imagePreview.length && !this.$imagePreview.hasClass('hide') && this.options.imageMode >= 2) {
            let $cropper = this.cropper(this.$containerImage[0]);

            this.$container.on('closed.zf.reveal', function() {
                if ($cropper) {
                    let $url = $cropper.getCroppedCanvas().toDataURL();

                    _this.$image.attr('src', $url);
                    _this.$containerImage.attr('src', $url);

                    let name = _this.$element.attr('name').search(/^(.*)[\]]$/) !== -1
                        ? _this.$element.attr('name').replace(/^(.*)\[([^\]]+)\]$/, '$1[cropped_$2]')
                        : 'cropped_' + _this.$element.attr('name');

                    let id = name.replace(/[\[]/g, '_').replace(/[\]|\'|\"]/g, '');

                    let value = JSON.stringify($cropper.getData());

                    if (!$('#' + id).val(value).length) {
                        _this.$label.after($('<input>', {type: 'hidden', name: name, id: id, value: value}));
                    }

                    $cropper.destroy();
                    $cropper = null;

                    if (!_this.$editor_menu.hasClass('hide')) {
                        _this.$editor_menu.addClass('hide').hide();
                    }
                }
            });

            this.$container.foundation('open');
        }
    }

    /**
     * Change file and show preview.
     * @private
     * @param {string} name - File name.
     * @param {string} src - File URL.
     */
    _showPreviewFile(name = '', src = '') {
        if (!name || !src) {
            name = this.$link.length ? this.$link.find('a').text() : '';
            src = this.$link.length ? this.$link.find('a').attr('href') : '';
        }

        let type = this._getFileType(name);

        this.$image = this.$wrapper.find('img.thumbnail');
        this.$containerImage = this.$container.find('img');

        if (!this.options.imageMode || type !== 'image') {
            if (name) {
                this.$preview.removeClass('hide').show();
                this.$imagePreview.addClass('hide').hide();

                if (this.options.fileIconPrefix) {
                    let previewClass = this.$preview.find('i').attr('class').trim().split(' ');
                    let currentClass = this.options.fileIconPrefix;

                    for (let i = 0; i < previewClass.length; i++) {
                        if (previewClass[i].search(new RegExp(this.options.fileIconPrefix + '-[^\s]+')) !== -1) {
                            currentClass = previewClass[i].trim();
                        }
                    }

                    this.$preview.find('i').removeClass(currentClass)
                        .addClass(this.options.fileIconPrefix + (type ? '-' + type : ''));
                }
            }
        } else {
            this.$preview.addClass('hide').hide();
            this.$imagePreview.removeClass('hide').show();

            if (this.$image.attr('src') !== src) {
                this.$image.attr('src', src).removeClass('hide').show();
                this.$containerImage.attr('src', src);
            } else {
                this.$image.attr('src', src + '?t=' + Date.now());
            }
        }
    }

    /**
     * Get file type by name.
     * @private
     * @param {string} name - File name.
     * @returns {string}
     */
    _getFileType(name) {
        let fileExtension = name.trim().split('.').pop().trim();

        for (let key in this.options.fileExtensions) {
            if (this.options.fileExtensions.hasOwnProperty(key) && this.options.fileExtensions[key].indexOf(fileExtension) !== -1) {
                return key;
            }
        }

        return '';
    }

    /**
     * Get file extensions.
     */
    _getFileExtensions() {
        this.options.fileExtensions = Object.assign({
            "image": ["jpg", "jpeg", "png", "gif", "svg"],
            "word": ["doc", "docx"],
            "excel": ["xls", "xlsx"],
            "powerpoint": ["ppt", "pptx"],
            "pdf": ["pdf"],
            "archive": ["zip","rar"]
        }, this.options.fileExtensions);
    }

    /**
     * Crop and edit the image.
     * See {@link https://github.com/fengyuanchen/cropperjs}.
     * @param {string} image - HTMLImageElement.
     * @returns {object}
     */
    cropper(image) {
        let $cropper = new Cropper(image, {
            autoCrop: false,
            viewMode: 2,
            aspectRatio: this.options.imageAspectRatio
        });

        this.$editor_menu = $('#editor_menu_' + this.$element.attr('id'));

        if (this.options.imageMode >= 3) {
            this.$editor_menu.removeClass('hide').show();
        }

        $('#editor_undo_' + this.$element.attr('id')).on('click.foundation.formFile.undo', function() {
            $cropper.rotate(-45);
        });

        $('#editor_redo_' + this.$element.attr('id')).on('click.foundation.formFile.redo', function() {
            $cropper.rotate(45);
        });

        $('#editor_h_' + this.$element.attr('id')).on('click.foundation.formFile.h', function() {
            $cropper.scaleX($cropper.getData().scaleX === 1 ? -1 : 1);
        });

        $('#editor_v_' + this.$element.attr('id')).on('click.foundation.formFile.v', function() {
            $cropper.scaleY($cropper.getData().scaleY === 1 ? -1 : 1);
        });

        $('#editor_sync_' + this.$element.attr('id')).on('click.foundation.formFile.sync', function() {
            $cropper.reset();
            $cropper.clear();
        });

        let $container = this.$container;

        $('#editor_check_' + this.$element.attr('id')).on('click.foundation.formFile.check', function() {
            $container.foundation('close');
        });

        return $cropper;
    }

    /**
     * Destroys an instance of FormFile.
     * @function
     */
    _destroy() {
        Foundation.unregisterPlugin(this, this.className);
    }
}

/**
 * Default settings for plugin
 */
FormFile.defaults = {
    /**
     * Image preview and editing mode.
     * Options: 1 - preview; 2 - cropper; 3 - editor.
     * @option
     * @type {number}
     * @default 0
     */
    imageMode: 0,

    /**
     * Define the fixed aspect ratio of the crop box.
     * @option
     * @type {number}
     * @default 0
     */
    imageAspectRatio: NaN,

    /**
     * File extensions are grouped by the type used to check and display file icons.
     * @option
     * @type {Object} Example: {"image":["jpg","jpeg"],"word":["doc","docx"]}
     * @default 0
     */
    fileExtensions: {},

    /**
     * Prefix file icons.
     * @option
     * @type {string}
     * @default 'icon-file'
     */
    fileIconPrefix: 'icon-file',

    /**
     * URL to load data using Ajax.
     * @option
     * @type {string}
     * @default ''
     */
    ajaxUrl: '',

    /**
     * URL to get data using Ajax.
     * @option
     * @type {string}
     * @default window.location.href
     */
    ajaxUrlToGet: window.location.href,

    /**
     * Enable output of ajax error messages to the console.
     * @option
     * @type {boolean}
     * @default false
     */
    debugAjax: false,
};

export {FormFile};
