(function ($, wp, _) {
    'use strict';

    if (!window.MivamaMediaFolders) {
        return;
    }

    var config = window.MivamaMediaFolders;
    var taxonomy = config.taxonomy || 'mivama_media_folder';
    var fieldKey = config.fieldKey || 'mivama_media_folder_term_id';
    var terms = Array.isArray(config.terms) ? config.terms : [];
    var labels = config.labels || {};
    var $activeFolderSelect = null;

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getTermName(term) {
        return term && term.name ? term.name : '';
    }

    function buildFolderOptions(placeholder, selectedId) {
        var html = '<option value="0">' + escapeHtml(placeholder || '') + '</option>';
        selectedId = parseInt(selectedId || 0, 10);

        terms.forEach(function (term) {
            var termId = parseInt(term.id || 0, 10);
            html += '<option value="' + termId + '"' + (termId === selectedId ? ' selected' : '') + '>' + escapeHtml(getTermName(term)) + '</option>';
        });

        return html;
    }

    function buildFilterOptions(selectedValue) {
        selectedValue = String(selectedValue || '');
        var html = '<option value=""' + (selectedValue === '' ? ' selected' : '') + '>' + escapeHtml(labels.allFolders || 'All folders') + '</option>';
        html += '<option value="-1"' + (selectedValue === '-1' ? ' selected' : '') + '>' + escapeHtml(labels.unassigned || 'Unassigned') + '</option>';

        terms.forEach(function (term) {
            var termId = String(parseInt(term.id || 0, 10));
            html += '<option value="' + termId + '"' + (termId === selectedValue ? ' selected' : '') + '>' + escapeHtml(getTermName(term)) + '</option>';
        });

        return html;
    }

    function setStatus($control, message, type) {
        var $status = $control.find('.mivama-folder-status').first();
        if (!$status.length) {
            return;
        }

        $status.removeClass('is-error is-success is-loading');
        if (type) {
            $status.addClass('is-' + type);
        }
        $status.text(message || '');
    }

    function ensureModal() {
        if ($('#mivama-folder-modal').length) {
            return;
        }

        $('body').append(
            '<div id="mivama-folder-modal" class="mivama-folder-modal" aria-hidden="true">' +
                '<div class="media-modal-backdrop" data-mivama-close-folder-modal></div>' +
                '<div class="media-modal wp-core-ui mivama-folder-dialog" role="dialog" aria-modal="true" aria-labelledby="mivama-folder-modal-title">' +
                    '<button type="button" class="media-modal-close" data-mivama-close-folder-modal>' +
                        '<span class="media-modal-icon"><span class="screen-reader-text">Close</span></span>' +
                    '</button>' +
                    '<div class="media-modal-content">' +
                        '<div class="media-frame-title"><h1 id="mivama-folder-modal-title">' + escapeHtml(labels.createFolder || 'Create folder') + '</h1></div>' +
                        '<form id="mivama-folder-create-form">' +
                            '<div class="media-frame-content mivama-folder-dialog-content">' +
                                '<div class="form-field form-required">' +
                                    '<label for="mivama-folder-name">' + escapeHtml(labels.folderName || 'Folder name') + '</label>' +
                                    '<input type="text" id="mivama-folder-name" name="name" autocomplete="off" required>' +
                                '</div>' +
                                '<div class="form-field">' +
                                    '<label for="mivama-folder-parent">' + escapeHtml(labels.parentFolder || 'Parent folder') + '</label>' +
                                    '<select id="mivama-folder-parent" name="parent"></select>' +
                                '</div>' +
                                '<div class="mivama-folder-modal__message" aria-live="polite"></div>' +
                            '</div>' +
                            '<div class="media-frame-toolbar">' +
                                '<button type="button" class="button" data-mivama-close-folder-modal>' + escapeHtml(labels.cancel || 'Cancel') + '</button>' +
                                '<button type="submit" class="button button-primary mivama-folder-create-submit">' + escapeHtml(labels.createFolder || 'Create folder') + '</button>' +
                            '</div>' +
                        '</form>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
    }

    function openModal($selectTarget) {
        ensureModal();
        $activeFolderSelect = $selectTarget && $selectTarget.length ? $selectTarget : null;
        $('#mivama-folder-parent').html(buildFolderOptions(labels.noParent || 'No parent folder', 0));
        $('#mivama-folder-name').val('');
        $('.mivama-folder-modal__message').removeClass('is-error is-success').text('');
        $('#mivama-folder-modal').addClass('is-visible').attr('aria-hidden', 'false');
        window.setTimeout(function () {
            $('#mivama-folder-name').trigger('focus');
        }, 50);
    }

    function closeModal() {
        $('#mivama-folder-modal').removeClass('is-visible').attr('aria-hidden', 'true');
        $activeFolderSelect = null;
    }

    function refreshSelects(selectedTermId) {
        selectedTermId = parseInt(selectedTermId || 0, 10);

        $('#mivama-media-folder-filter').each(function () {
            var selected = $(this).val() || '';
            $(this).html(buildFilterOptions(selected));
        });

        $('#mivama-bulk-folder-target').each(function () {
            var selected = parseInt($(this).val() || 0, 10);
            $(this).html(buildFolderOptions(labels.bulkTargetFolder || 'Bulk target folder', selected));
        });

        $('.mivama-media-folder-select, .compat-field-' + fieldKey + ' select').each(function () {
            var $select = $(this);
            var selected = parseInt($select.val() || 0, 10);

            if ($activeFolderSelect && $select.is($activeFolderSelect)) {
                selected = selectedTermId;
            }

            $select.html(buildFolderOptions(labels.noFolder || 'No folder', selected));
            $select.val(String(selected));
        });
    }

    function refreshMediaToolbarFilters() {
        if (!wp || !wp.media || !wp.media.frame || !wp.media.frame.content) {
            return;
        }

        try {
            var view = wp.media.frame.content.get();
            if (!view || !view.toolbar || !view.toolbar.get) {
                return;
            }

            var filter = view.toolbar.get('mivamaMediaFolderFilter');
            if (filter && filter.createFilters && filter.render) {
                filter.createFilters();
                filter.render();
            }
        } catch (error) {
        }
    }

    function refreshMediaCollections() {
        if (!wp || !wp.media || !wp.media.frame) {
            return;
        }

        var seen = [];

        function refreshCollection(collection) {
            if (!collection || seen.indexOf(collection) !== -1) {
                return;
            }

            seen.push(collection);

            if (typeof collection._requery === 'function') {
                collection._requery();
                return;
            }

            if (collection.props && typeof collection.props.trigger === 'function') {
                collection.props.trigger('change');
            }
        }

        try {
            var view = wp.media.frame.content && wp.media.frame.content.get ? wp.media.frame.content.get() : null;

            if (view && view.collection) {
                refreshCollection(view.collection);
            }
        } catch (error) {
        }

        try {
            var state = typeof wp.media.frame.state === 'function' ? wp.media.frame.state() : null;

            if (state && state.get) {
                refreshCollection(state.get('library'));
                refreshCollection(state.get('selection'));
            }
        } catch (error) {
        }
    }

    function updateTermsFromResponse(data) {
        if (data && Array.isArray(data.terms)) {
            terms = data.terms;
            config.terms = terms;
        }
    }

    function saveAttachmentFolder($select) {
        if (!$select || !$select.length) {
            return;
        }

        var attachmentId = parseInt($select.data('attachment-id') || $select.closest('.mivama-folder-control').data('attachment-id') || 0, 10);
        var folderId = parseInt($select.val() || 0, 10);
        var $control = $select.closest('.mivama-folder-control');
        var $buttons = $control.find('.mivama-save-folder-button');

        if (!attachmentId) {
            return;
        }

        $buttons.prop('disabled', true);
        setStatus($control, labels.saving || 'Saving…', 'loading');

        $.ajax({
            url: config.ajaxUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'mivama_set_attachment_folder',
                nonce: config.nonce,
                attachmentId: attachmentId,
                folderId: folderId
            }
        }).done(function (response) {
            var data = response && response.data ? response.data : {};

            if (!response || !response.success) {
                setStatus($control, data.message || labels.requestFailed || 'Request failed. Please try again.', 'error');
                return;
            }

            setStatus($control, data.message || labels.saved || 'Folder saved.', 'success');

            if (wp && wp.media && wp.media.attachment) {
                try {
                    wp.media.attachment(attachmentId).fetch();
                } catch (error) {}
            }

            refreshMediaCollections();
        }).fail(function (xhr) {
            var message = labels.requestFailed || 'Request failed. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                message = xhr.responseJSON.data.message;
            }
            setStatus($control, message, 'error');
        }).always(function () {
            $buttons.prop('disabled', false);
        });
    }

    $(document).on('click', '.mivama-new-folder-trigger', function (event) {
        event.preventDefault();
        var $select = $(this).closest('.mivama-folder-control').find('.mivama-media-folder-select').first();
        openModal($select);
    });

    $(document).on('click', '[data-mivama-close-folder-modal]', function (event) {
        event.preventDefault();
        closeModal();
    });

    $(document).on('keydown', function (event) {
        if (event.key === 'Escape' && $('#mivama-folder-modal').hasClass('is-visible')) {
            closeModal();
        }
    });

    $(document).on('click', '.mivama-save-folder-button', function (event) {
        event.preventDefault();
        var $select = $(this).closest('.mivama-folder-control').find('.mivama-media-folder-select').first();
        saveAttachmentFolder($select);
    });

    $(document).on('change', '.mivama-media-folder-select', function () {
        saveAttachmentFolder($(this));
    });

    $(document).on('submit', '#mivama-folder-create-form', function (event) {
        event.preventDefault();

        var $form = $(this);
        var $submit = $form.find('.mivama-folder-create-submit');
        var $message = $form.find('.mivama-folder-modal__message');
        var name = $.trim($('#mivama-folder-name').val());
        var parent = parseInt($('#mivama-folder-parent').val() || 0, 10);

        if (!name) {
            $message.addClass('is-error').removeClass('is-success').text(labels.nameRequired || 'Please enter a folder name.');
            return;
        }

        $submit.prop('disabled', true).text(labels.creating || 'Creating…');
        $message.removeClass('is-error is-success').text('');

        $.ajax({
            url: config.ajaxUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'mivama_create_media_folder',
                nonce: config.nonce,
                name: name,
                parent: parent
            }
        }).done(function (response) {
            var data = response && response.data ? response.data : {};
            var termId = data.term && data.term.id ? parseInt(data.term.id, 10) : 0;

            if (!response || !response.success) {
                $message.addClass('is-error').removeClass('is-success').text(data.message || labels.requestFailed || 'Request failed. Please try again.');
                return;
            }

            updateTermsFromResponse(data);
            refreshSelects(termId);
            refreshMediaToolbarFilters();
            $message.addClass('is-success').removeClass('is-error').text(data.message || labels.created || 'Folder created.');

            if ($activeFolderSelect && termId) {
                $activeFolderSelect.val(String(termId));
                saveAttachmentFolder($activeFolderSelect);
            }

            window.setTimeout(function () {
                closeModal();
            }, 450);
        }).fail(function (xhr) {
            var message = labels.requestFailed || 'Request failed. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                message = xhr.responseJSON.data.message;
            }
            $message.addClass('is-error').removeClass('is-success').text(message);
        }).always(function () {
            $submit.prop('disabled', false).text(labels.createFolder || 'Create folder');
        });
    });

    if (wp && wp.media && wp.media.view && wp.media.view.AttachmentFilters && wp.media.view.AttachmentsBrowser) {
        var FolderFilter = wp.media.view.AttachmentFilters.extend({
            id: 'media-attachment-mivama-folder-filter',

            createFilters: function () {
                var filters = {
                    all: {
                        text: labels.allFolders || 'All folders',
                        props: {},
                        priority: 10
                    },
                    unassigned: {
                        text: labels.unassigned || 'Unassigned',
                        props: {},
                        priority: 11
                    }
                };

                filters.all.props[taxonomy] = '';
                filters.unassigned.props[taxonomy] = '-1';

                _.each(terms, function (term, index) {
                    var key = 'mivama-folder-' + term.id;
                    var props = {};
                    props[taxonomy] = String(term.id);

                    filters[key] = {
                        text: getTermName(term),
                        props: props,
                        priority: 20 + index
                    };
                });

                this.filters = filters;
            }
        });

        var AttachmentsBrowser = wp.media.view.AttachmentsBrowser;

        wp.media.view.AttachmentsBrowser = AttachmentsBrowser.extend({
            createToolbar: function () {
                AttachmentsBrowser.prototype.createToolbar.apply(this, arguments);

                if (!this.toolbar || !this.collection || !this.collection.props) {
                    return;
                }

                this.toolbar.set('mivamaMediaFolderFilter', new FolderFilter({
                    controller: this.controller,
                    model: this.collection.props,
                    priority: -79
                }).render());

                if (wp.media.view.Button) {
                    this.toolbar.set('mivamaNewFolderButton', new wp.media.view.Button({
                        text: '+ ' + (labels.newFolder || 'New folder'),
                        className: 'button mivama-new-folder-trigger mivama-new-folder-media-button',
                        priority: -78,
                        click: function () {
                            openModal(null);
                        }
                    }).render());
                }
            }
        });
    }
})(jQuery, window.wp, window._);
