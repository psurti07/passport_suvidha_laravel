@extends('layouts.app')

@section('title', 'Website Links')

@section('content')
    <div class="mx-auto">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4 sm:p-6 lg:p-8">

                {{-- Heading --}}
                <div class="flex flex-col sm:flex-row justify-between
                        items-center mb-6 gap-3">
                    <h2
                        class="text-xl sm:text-2xl font-bold
                    bg-gradient-to-r from-blue-600 to-blue-800
                    bg-clip-text text-transparent">
                        WEBSITE LINKS
                    </h2>

                    <button type="button" id="addWebsiteLink"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600
                    to-blue-800 text-white rounded-lg text-sm font-semibold
                    hover:from-blue-700 hover:to-blue-900 shadow-md">
                        + Add Website Link
                    </button>
                </div>

                <div id="pageMessage" class="hidden mb-4 p-3 rounded-lg text-sm"></div>

                {{-- DataTable --}}
                <div class="mt-4 overflow-x-auto">
                    <div class="whitespace-nowrap text-sm text-gray-700">
                        <table id="website-links-table" class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-blue-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sr. No.
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Short Link
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Link</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add / Edit Modal --}}
    <div id="websiteLinkModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50">

        <div class="min-h-full flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl">

                <div class="flex justify-between items-center
                px-6 py-4 border-b border-gray-200">

                    <h3 id="modalTitle" class="text-lg sm:text-xl font-bold text-gray-800">
                        Add Website Link
                    </h3>

                    <button type="button" id="closeWebsiteLinkModal" class="text-gray-500 hover:text-gray-800 text-2xl"
                        aria-label="Close">&times;</button>
                </div>

                <form id="websiteLinkForm">
                    @csrf

                    <div class="p-6 space-y-5">

                        <div id="formErrors"
                            class="hidden rounded-lg bg-red-50 text-red-700
                        border border-red-200 p-3 text-sm">
                        </div>

                        {{-- Link Type --}}
                        <div>
                            <label class="block text-sm font-semibold
                            text-gray-700 mb-2">
                                Link Type <span class="text-red-500">*</span>
                            </label>

                            <div class="flex flex-wrap gap-6">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="link_type" value="website" required checked
                                        class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Website</span>
                                </label>

                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="link_type" value="social_media" required
                                        class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Social Media</span>
                                </label>
                            </div>
                        </div>

                        {{-- Title --}}
                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>

                            <input type="text" id="title" name="title" required maxlength="255"
                                placeholder="Enter title"
                                class="w-full border border-gray-300 rounded-lg
                            px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>

                        {{-- Original Link --}}
                        <div>
                            <label for="link" class="block text-sm font-semibold text-gray-700 mb-2">
                                Link <span class="text-red-500">*</span>
                            </label>

                            <input type="url" id="link" name="link" required maxlength="2048"
                                placeholder="https://example.com"
                                class="w-full border border-gray-300 rounded-lg
                            px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>

                        {{-- Manually entered Short Link --}}
                        <div>
                            <label for="short_link_display" class="block text-sm font-semibold text-gray-700 mb-2">
                                Short Link
                                {{-- <span class="text-red-500">*</span> --}}
                            </label>

                            <textarea id="short_link_display" name="short_link" rows="2" maxlength="255" placeholder="Enter short link"
                                class="w-full border border-gray-300 rounded-lg
                            px-4 py-3 text-sm bg-white text-gray-700
                            resize-y focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>

                            <select id="status" name="status" required
                                class="w-full border border-gray-300 rounded-lg
                            px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="active">Active</option>
                                <option value="inactive">Deactive</option>
                            </select>
                        </div>
                    </div>

                    <div
                        class="flex justify-end gap-3 px-6 py-4
                    border-t border-gray-200 bg-gray-50 rounded-b-xl">

                        <button type="button" id="cancelWebsiteLink"
                            class="px-5 py-2.5 border border-gray-300 rounded-lg
                        text-sm font-semibold text-gray-700 hover:bg-gray-100">
                            Cancel
                        </button>

                        <button type="submit" id="saveWebsiteLink"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-600
                        to-blue-800 text-white rounded-lg text-sm font-semibold
                        hover:from-blue-700 hover:to-blue-900 disabled:opacity-50">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            let editingId = null;

            const urls = {
                data: @json(route('admin.website-links.data')),
                store: @json(route('admin.website-links.store')),
                edit: @json(route('admin.website-links.edit', ['websiteLink' => '__ID__'])),
                update: @json(route('admin.website-links.update', ['websiteLink' => '__ID__'])),
                destroy: @json(route('admin.website-links.destroy', ['websiteLink' => '__ID__']))
            };

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const $modal = $('#websiteLinkModal');
            const $form = $('#websiteLinkForm');
            const $saveButton = $('#saveWebsiteLink');

            function showMessage(message, type = 'success') {
                $('#pageMessage')
                    .removeClass('hidden bg-green-50 text-green-700 bg-red-50 text-red-700')
                    .addClass(type === 'success' ?
                        'bg-green-50 text-green-700' :
                        'bg-red-50 text-red-700')
                    .text(message);
            }

            function showFormErrors(errors) {
                const $box = $('#formErrors').empty().removeClass('hidden');

                $.each(errors, function(field, messages) {
                    $.each(messages, function(_, message) {
                        $('<div>').text(message).appendTo($box);
                    });
                });
            }

            function itemUrl(template, id) {
                return template.replace('__ID__', encodeURIComponent(id));
            }

            function resetForm() {
                $form[0].reset();
                editingId = null;

                $('#formErrors').empty().addClass('hidden');
                $('#modalTitle').text('Add Website Link');

                $('input[name="link_type"][value="website"]').prop('checked', true);
                $('#status').val('active');

                $form.find('input, select, textarea').prop('disabled', false);
                $saveButton.removeClass('hidden').prop('disabled', false).text('Save');
            }

            function openModal() {
                $modal.removeClass('hidden');
            }

            function closeModal() {
                $modal.addClass('hidden');
            }

            // Export readable field values, not HTML markup.
            function formatWebsiteLinkExport(data, node) {
                const $node = $(node);
                const $textarea = $node.find('textarea');

                if ($textarea.length) {
                    return $textarea.val().trim();
                }

                const $link = $node.find('a');

                if ($link.length) {
                    return $link.attr('href') || $link.text().trim();
                }

                return $node.text().trim();
            }

            const table = $('#website-links-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                scrollX: true,
                autoWidth: false,
                order: [
                    [2, 'desc']
                ],

                ajax: {
                    url: urls.data,
                    type: 'GET',
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        showMessage('Unable to load website links.', 'error');
                    }
                },

                columns: [{
                        data: null,
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.settings._iDisplayStart + meta.row + 1;
                        }
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'date',
                        name: 'created_at'
                    },
                    {
                        data: 'link_type_badge',
                        name: 'link_type',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'short_url',
                        name: 'short_link',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'view_button',
                        name: 'link',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: 'Blfrtip',

                buttons: ['copy', 'excel', 'csv', 'pdf'].map(function(buttonName) {
                    return {
                        extend: buttonName,
                        exportOptions: {
                            columns: ':not(:last-child)',
                            format: {
                                body: function(data, row, column, node) {
                                    return formatWebsiteLinkExport(data, node);
                                }
                            }
                        }
                    };
                }),

                lengthMenu: [
                    [50, 100, 250, 500],
                    [50, 100, 250, 500]
                ],
            });

            $('#addWebsiteLink').on('click', function() {
                resetForm();
                openModal();
            });

            $('#closeWebsiteLinkModal, #cancelWebsiteLink').on('click', function() {
                closeModal();
                resetForm();
            });

            $modal.on('click', function(event) {
                if (event.target === this) {
                    closeModal();
                    resetForm();
                }
            });

            // Create or update.
            $form.on('submit', function(event) {
                event.preventDefault();

                $('#formErrors').empty().addClass('hidden');

                const isEditing = editingId !== null;
                const recordId = editingId;

                $saveButton.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: isEditing ? itemUrl(urls.update, recordId) : urls.store,
                    type: isEditing ? 'PUT' : 'POST',
                    data: $form.serialize(),

                    success: function(response) {
                        closeModal();
                        resetForm();
                        table.ajax.reload(null, false);

                        // showMessage(response.message ||
                        //     (isEditing ?
                        //         'Website link updated successfully.' :
                        //         'Website link added successfully.'));
                        showToast(response.message, 'success');
                    },

                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            showFormErrors(xhr.responseJSON.errors);
                        } else {
                            showMessage(
                                xhr.responseJSON?.message || 'Unable to save website link.',
                                'error'
                            );
                        }
                    },

                    complete: function() {
                        $saveButton.prop('disabled', false);
                        if (editingId !== null) {
                            $saveButton.text('Update');
                        } else {
                            $saveButton.text('Save');
                        }
                    }
                });
            });

            // Edit: prefill the same modal.
            $('#website-links-table').on('click', '.edit-link', function() {
                const id = $(this).data('id');

                resetForm();
                $('#modalTitle').text('Edit Website Link');
                $('#saveWebsiteLink').text('Loading...').prop('disabled', true);
                openModal();

                $.ajax({
                    url: itemUrl(urls.edit, id),
                    type: 'GET',

                    success: function(response) {
                        const item = response.data;

                        editingId = item.id;

                        $('input[name="link_type"][value="' + item.link_type + '"]')
                            .prop('checked', true);

                        $('#title').val(item.title);
                        $('#link').val(item.link);
                        $('#short_link_display').val(item.short_link);
                        $('#status').val(item.status);

                        $('#saveWebsiteLink').text('Update').prop('disabled', false);
                    },

                    error: function(xhr) {
                        console.error(xhr.responseText);
                        closeModal();
                        resetForm();
                        showMessage('Unable to load website link details.', 'error');
                    }
                });
            });

            // Delete.
            $('#website-links-table').on('click', '.delete-link', function() {
                const id = $(this).data('id');
                const title = $(this).data('title') || 'this website link';

                // Reuse your existing SweetAlert confirmation helper.
                confirmDelete(title, {
                    submit: function() {
                        $.ajax({
                            url: itemUrl(urls.destroy, id),
                            type: 'DELETE',

                            success: function(response) {
                                table.ajax.reload(null, false);

                                // showMessage(
                                //     response.message ||
                                //     'Website link deleted successfully.',
                                //     'success'
                                // );
                                showToast(response.message, 'success');
                            },

                            error: function(xhr) {
                                showMessage(
                                    xhr.responseJSON?.message ||
                                    'Unable to delete website link.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
