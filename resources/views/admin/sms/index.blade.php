@extends('layouts.app')

@section('title', 'SMS Messages')

@section('content')

    <div class="mx-auto">

        <div class="bg-white rounded-xl shadow-lg border border-gray-100">

            <div class="p-4 sm:p-6 lg:p-8">

                <form id="filterForm">

                    <div class="flex flex-col lg:flex-row justify-between items-center mb-6">
                        <div class="flex items-center gap-4">
                            <h2
                                class="text-xl sm:text-2xl mb-3 md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                                SMS MESSAGES
                            </h2>
                        </div>
                        <div class="flex flex-wrap gap-3">

                            <div>
                                <label class="text-sm">From</label>
                                <input type="date" id="from_date" class="border rounded-lg px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="text-sm">To</label>
                                <input type="date" id="to_date" class="border rounded-lg px-3 py-2 text-sm">
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
                    <div class="whitespace-nowrap text-sm text-gray-700">

                        <table id="sms-messages-table" class="min-w-full divide-y divide-gray-200 pt-5">

                            <thead class="bg-blue-50">

                                <tr>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">SMS Type
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                        SMS Message</th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Updated
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

            let table = $('#sms-messages-table').DataTable({

                processing: true,
                serverSide: true,

                responsive: true,
                scrollX: true,

                order: [
                    [0, 'desc']
                ],

                ajax: {
                    url: "{{ route('admin.sms.data') }}",
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
                        data: 'option_key',
                        name: 'option_key'
                    },
                    {
                        data: 'option_value',
                        name: 'option_value',
                        className: 'whitespace-normal break-words',
                        width: '500px'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
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
