@extends('layouts.app')

@section('title', 'Refunds')

@section('content')

    <div class="mx-auto">

        <div class="bg-white rounded-xl shadow-lg border border-gray-100">

            <div class="p-4 sm:p-6 lg:p-8">

                <form id="filterForm">

                    <div class="flex flex-col lg:flex-row justify-between items-center mb-6">

                        <h2
                            class="text-xl sm:text-2xl mb-3 md:mb-6 md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                            REFUNDS
                        </h2>
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
                    <div class="whitespace-nowrap text-sm text-gray-700">

                        <table id="refund-table" class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-blue-50">

                                <tr>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mobile
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Refund No
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Payment ID
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Refund ID
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status

                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Refunded
                                        At
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Remark
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

            let table = $('#refund-table').DataTable({

                processing: true,
                serverSide: true,

                responsive: false,
                scrollX: true,
                autoWidth: false,

                order: [
                    [0, 'desc']
                ],

                ajax: {
                    url: "{{ route('admin.refund.data') }}",

                    data: function(d) {
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                    }
                },

                columns: [

                    {
                        data: null,
                        name: 'sr_no',
                        orderable: false,
                        searchable: false,

                        render: function(data, type, row, meta) {

                            let total = meta.settings._iRecordsDisplay;

                            let index =
                                meta.row +
                                meta.settings._iDisplayStart;

                            return total - index;
                        }
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
                        data: 'refund_no',
                        name: 'refund_no'
                    },

                    {
                        data: 'payment_id',
                        name: 'payment_id'
                    },

                    {
                        data: 'refund_id',
                        name: 'refund_id'
                    },

                    {
                        data: 'amount',
                        name: 'amount'
                    },

                    {
                        data: 'status',
                        name: 'status'
                    },

                    {
                        data: 'refunded_at',
                        name: 'refunded_at'
                    },

                    {
                        data: 'remark',
                        name: 'remark'
                    }
                ],

                dom: 'Blfrtip',

                buttons: [

                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },

                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },

                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },

                    {
                        extend: 'pdf',
                        orientation: 'landscape',
                        pageSize: 'A3',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ],

                lengthMenu: [
                    [50, 100, 250, 500],
                    [50, 100, 250, 500]
                ]

            });

            $('#filter').click(function() {
                table.draw();
            });

        });
    </script>
@endpush
