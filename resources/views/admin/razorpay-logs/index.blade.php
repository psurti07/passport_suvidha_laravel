@extends('layouts.app')

@section('title', 'Razorpay Logs')

@section('content')

<div class="mx-auto">

    <div class="bg-white rounded-xl shadow-lg border border-gray-100">

        <div class="p-4 sm:p-6 lg:p-8">

            <form id="filterForm">

                <div class="flex flex-col lg:flex-row justify-between items-center mb-6">
                    <div class="flex items-center gap-4">
                        <h2
                            class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                            RAZORPAY LOGS
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
                <div class="whitespace-nowrap text-sm text-gray-700">

                    <table id="razorpay-logs-table" class="min-w-full divide-y divide-gray-200 pt-5">

                        <thead class="bg-blue-50">

                            <tr>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mobile
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order ID
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order
                                    Amount</th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order Note
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reference
                                    ID</th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Payment
                                    Mode</th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created At
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

    let table = $('#razorpay-logs-table').DataTable({

        processing: true,
        serverSide: true,

        order: [
            [0, 'desc']
        ],

        ajax: {
            url: "{{ route('admin.razorpay-logs.data') }}",
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
                data: 'customer_email',
                name: 'customer_email'
            },
            {
                data: 'customer_mobile_number',
                name: 'customer_mobile_number'
            },
            {
                data: 'order_id',
                name: 'order_id'
            },
            {
                data: 'order_amount',
                name: 'order_amount'
            },
            {
                data: 'order_note',
                name: 'order_note'
            },
            {
                data: 'reference_id',
                name: 'reference_id'
            },
            {
                data: 'payment_mode',
                name: 'payment_mode'
            },
            {
                data: 'created_at',
                name: 'created_at'
            }
        ],

        dom: 'Blfrtip',

        buttons: [{
                extend: 'copy'
            },
            {
                extend: 'excel'
            },
            {
                extend: 'csv'
            },
            {
                extend: 'pdf'
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