@extends('layouts.app')

@section('title', 'GST Data')

@section('content')

<div class="mx-auto">

    <div class="bg-white rounded-xl shadow-lg border border-gray-100">

        <div class="p-4 sm:p-6 lg:p-8">

            <form id="filterForm">

                <div class="flex flex-col lg:flex-row justify-between items-center mb-6">

                    <h2
                        class="text-xl sm:text-2xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        GST DATA
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

                    <table id="gst-table" class="min-w-full divide-y divide-gray-200 pt-5">

                        <thead class="bg-blue-50">

                            <tr>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mobile
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">City
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">State
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Invoice No
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Invoice
                                    Date
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Net
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">CGST
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">SGST
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">IGST
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Payment ID
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

    let table = $('#gst-table').DataTable({

        processing: true,
        serverSide: true,

        order: [
            [0, 'desc']
        ],

        ajax: {
            url: "{{ route('admin.gst.data') }}",
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
                data: 'customer_city',
                name: 'customer_city'
            },
            {
                data: 'customer_state',
                name: 'customer_state'
            },
            {
                data: 'inv_no',
                name: 'inv_no'
            },
            {
                data: 'inv_date',
                name: 'inv_date'
            },
            {
                data: 'net_amount',
                name: 'net_amount'
            },
            {
                data: 'cgst',
                name: 'cgst'
            },
            {
                data: 'sgst',
                name: 'sgst'
            },
            {
                data: 'igst',
                name: 'igst'
            },
            {
                data: 'total_amount',
                name: 'total_amount'
            },
            {
                data: 'application_order_paymentid',
                name: 'order.payment_id'
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