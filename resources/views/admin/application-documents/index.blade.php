@extends('layouts.app')

@section('title', 'Application Documents')

@section('content')

<div class="mx-auto">

    <div class="bg-white rounded-xl shadow-lg border border-gray-100">

        <div class="p-4 sm:p-6 lg:p-8">

            <form id="filterForm">

                <div class="flex flex-col lg:flex-row justify-between items-center mb-6">
                    <div class="flex items-center gap-4">
                        <h2
                            class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                            APPLICATION DOCUMENTS
                        </h2>
                    </div>
                    <div class="flex flex-wrap gap-3">

                        <div>
                            <label class="text-sm">From</label>
                            <input type="date" id="from_date" value="{{ now()->subDays(1)->format('Y-m-d') }}"
                                class="border rounded-lg px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="text-sm">To</label>
                            <input type="date" id="to_date" value="{{ now()->format('Y-m-d') }}"
                                class="border rounded-lg px-3 py-2 text-sm">
                        </div>

                        <div class="flex items-end">
                            <button type="button" id="filter"
                                class="w-full sm:w-auto px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                Show Results
                            </button>
                        </div>

                    </div>

                </div>

            </form>


            <div class="mt-4 overflow-x-auto">
                <div class="text-sm text-gray-700">

                    <table id="application-documents-table" class="min-w-full divide-y divide-gray-200 pt-5">

                        <thead class="bg-blue-50">

                            <tr>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mobile
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Document Name
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Document
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created
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

@endsection

@push('scripts')
<script>
$(function() {

    let table = $('#application-documents-table').DataTable({

        processing: true,
        serverSide: true,

        order: [
            [0, 'desc']
        ],

        ajax: {
            url: "{{ route('admin.application-documents.data') }}",
            data: function(d) {
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
            }
        },

        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'customer_name',
                name: 'customer_name'
            },
            {
                data: 'customer_mobile',
                name: 'customer_mobile'
            },
            {
                data: 'document_type_name',
                name: 'document_type_name'
            },
            {
                data: 'document',
                name: 'document',
                orderable: false,
                searchable: false
            },
            {
                data: 'is_verified',
                name: 'is_verified'
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false
            }
        ],

        dom: 'Blfrtip',

        buttons: [{
                extend: 'copy',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'excel',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdf',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ],

        pageLength: 10

    });

    $('#filter').click(function() {
        table.draw();
    });

});
</script>
@endpush