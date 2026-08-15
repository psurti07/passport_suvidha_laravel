@extends('layouts.app')

@section('title', 'Invoices')

@section('content')

    <div class="mx-auto">

        <div class="bg-white rounded-xl shadow-lg border border-gray-100">

            <div class="p-4 sm:p-6 lg:p-8">

                <form id="filterForm">

                    <div class="flex flex-col lg:flex-row justify-between items-center mb-6">

                        <h2
                            class="text-xl sm:text-2xl mb-3 md:mb-6 md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                            INVOICES
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

                        <table id="invoice-table" class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-blue-50">

                                <tr>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mobile
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Invoice No
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Invoice
                                        Date
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total
                                        Amount
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Payment ID
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

    <!-- Refund Modal -->
    <div id="refundModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">

        <div class="w-full max-w-md rounded-xl bg-white shadow-2xl">

            <!-- Header -->
            <div class="flex items-center justify-between border-b px-6 py-4">

                <h3 class="text-lg font-semibold text-gray-800">
                    Process Refund
                </h3>

                <button type="button" onclick="closeRefundModal()" class="text-gray-400 hover:text-gray-600">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />

                    </svg>
                </button>

            </div>

            <!-- Body -->
            <form id="refundForm" action="{{ route('admin.refund.store') }}" method="POST">
                @csrf

                <input type="hidden" id="refund_invoice_id" name="invoice_id">

                <div class="space-y-5 px-6 py-5">

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Payment ID
                        </label>

                        <input type="text" id="payment_id" name="payment_id" readonly
                            class="w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Refund Remark
                        </label>

                        <textarea id="refund_remark" name="remark" rows="4" required placeholder="Enter reason for refund..."
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200"></textarea>
                    </div>

                </div>

                <div class="flex justify-end gap-3 border-t bg-gray-50 px-6 py-4">
                    <button type="button" onclick="closeRefundModal()"
                        class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100">
                        Cancel
                    </button>

                    <button type="submit" id="refundSubmitButton"
                        onclick="this.disabled=true; this.innerText='Processing...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                        class="rounded-lg bg-purple-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-purple-700 disabled:cursor-not-allowed disabled:opacity-50">
                        Refund
                    </button>
                </div>
            </form>
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        function openRefundModal(invoiceId, paymentId, amount) {

            $('#refund_invoice_id').val(invoiceId);
            $('#payment_id').val(paymentId);
            $('#refund_remark').val('');

            $('#refundModal')
                .removeClass('hidden')
                .addClass('flex');
        }

        function closeRefundModal() {

            $('#refundModal')
                .removeClass('flex')
                .addClass('hidden');

            $('#refundForm')[0].reset();
        }
        $(function() {

            let table = $('#invoice-table').DataTable({

                processing: true,
                serverSide: true,

                responsive: false,
                scrollX: true,
                autoWidth: false,

                order: [
                    [4, 'desc']
                ],

                ajax: {
                    url: "{{ route('admin.invoices.data') }}",
                    data: function(d) {
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                    }
                },

                columns: [{
                        data: null,
                        name: 'sr_no',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {

                            let total = meta.settings._iRecordsDisplay;
                            let index = meta.row + meta.settings._iDisplayStart;

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
                        data: 'inv_no',
                        name: 'inv_no'
                    },
                    {
                        data: 'inv_date',
                        name: 'inv_date'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'application_order_paymentid',
                        name: 'order.payment_id'
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
