@extends('layouts.app')

@section('title', 'Schedule Slots')

@section('content')

    <div class="mx-auto">

        <div class="bg-white rounded-xl shadow-lg border border-gray-100">

            <div class="p-4 sm:p-6 lg:p-8">

                <form id="filterForm">

                    <div class="flex flex-col 2xl:flex-row justify-between items-center mb-6">
                        <div class="flex items-center gap-4">
                            <h2
                                class="text-xl sm:text-2xl mb-3 md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                                SCHEDULE SLOTS
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

                            <div>
                                <label class="text-sm">Language</label>
                                <select id="language" class="border rounded-lg px-3 py-2 text-sm sm:w-32">
                                    <option value="">All</option>
                                    <option value="1">Hindi</option>
                                    <option value="2">English</option>
                                    <option value="3">Gujarati</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm">Status</label>
                                <select id="status" class="border rounded-lg px-3 py-2 text-sm sm:w-32">
                                    <option value="">All</option>
                                    <option value="1">Scheduled</option>
                                    <option value="2">Completed</option>
                                    <option value="3">Cancelled</option>
                                    <option value="4">Not Reachable</option>
                                </select>
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

                        <table id="schedule-slots-table" class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-blue-50">

                                <tr>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                        Mobile</th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Service
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date &
                                        Time
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Launguage
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Remarks
                                    </th>

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

@endsection

@push('scripts')
    <script>
        $(function() {

            let table = $('#schedule-slots-table').DataTable({

                processing: true,
                serverSide: true,

                responsive: false,
                scrollX: true,
                autoWidth: false,

                order: [
                    [0, 'desc']
                ],

                ajax: {
                    url: "{{ route('admin.schedule-slots.data') }}",
                    data: function(d) {
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.language = $('#language').val();
                        d.status = $('#status').val();
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
                        data: 'service_name',
                        name: 'service_name'
                    },
                    {
                        data: 'date_time',
                        name: 'date_time'
                    },
                    {
                        data: 'language',
                        name: 'language'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks',
                        className: 'whitespace-normal break-words',
                        width: '500px'
                    },
                    {
                        data: 'status',
                        name: 'status'
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
    <script>
        $(document).on('change', '.change-status', function() {

            let status = $(this).val();
            let id = $(this).data('id');

            $.ajax({
                url: "{{ url('admin/schedule-slots') }}/" + id + "/status",
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {

                    if (response.success) {

                        toast(response.message, 'success');

                        $('#schedule-slots-table').DataTable().ajax.reload(null, false);
                    }
                },
                error: function() {

                    toast('Something went wrong.', 'error');
                }
            });

        });
    </script>
@endpush
