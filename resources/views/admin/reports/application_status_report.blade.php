@extends('layouts.app')

@section('title', 'Application Status Reports')

@section('content')

    <style>
        html,
        body {
            overflow-x: hidden;
        }

        #application-status-reports-table {
            width: 100% !important;
        }
    </style>

    <div class="mx-auto">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">

            <div class="p-4 lg:p-8">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                    <h2
                        class="text-xl mb-3 sm:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        APPLICATION STATUS REPORTS
                    </h2>
                </div>

                <table id="application-status-reports-table"
                    class="min-w-full divide-y divide-gray-200 border-separate text-sm">

                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Year</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Month</th>
                            <th class="px-4 py-3 !text-right text-xs font-semibold text-gray-600 uppercase">In Process</th>
                            <th class="px-4 py-3 !text-right text-xs font-semibold text-gray-600 uppercase">Appointment
                                Scheduled</th>
                            <th class="px-4 py-3 !text-right text-xs font-semibold text-gray-600 uppercase">POV Success</th>
                            <th class="px-4 py-3 !text-right text-xs font-semibold text-gray-600 uppercase">POV Failed</th>
                            <th class="px-4 py-3 !text-right text-xs font-semibold text-gray-600 uppercase">POV Insufficient
                                Documents</th>
                            <th class="px-4 py-3 !text-center text-xs font-semibold text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>

                    <tbody></tbody>

                    <tfoot>
                        <tr>

                            <th colspan="2" class="!px-4 !py-3 !text-right text-gray-600 font-bold">Grand Total</th>
                            <th id="grandInProcess" class="!px-4 !py-3 !text-right text-gray-600 font-bold">0</th>
                            <th id="grandAppointmentScheduled" class="!px-4 !py-3 !text-right text-gray-600 font-bold">0
                            </th>
                            <th id="grandPaymentSuccess" class="!px-4 !py-3 !text-right text-gray-600 font-bold">0</th>
                            <th id="grandPaymentFailed" class="!px-4 !py-3 !text-right text-gray-600 font-bold">0</th>
                            <th id="grandInsufficientDocuments" class="!px-4 !py-3 !text-right text-gray-600 font-bold">0
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div id="monthModal"
        class="fixed inset-0 hidden bg-black bg-opacity-50
           flex items-start justify-center
           pt-5 sm:pt-10 px-2 sm:px-4
           z-50 overflow-y-auto">

        <div class="bg-white w-full max-w-5xl
                rounded-lg shadow-lg
                my-3 sm:my-5">

            {{-- Header --}}
            <div class="flex justify-between items-center
                    border-b p-3 sm:p-4">
                <h2 class="text-base sm:text-lg font-bold text-gray-800">
                    Monthly Application Status Details
                </h2>
                <button type="button" onclick="closeModal()"
                    class="text-gray-500 hover:text-red-500
                       font-bold text-lg sm:text-xl
                       px-2 py-1">
                    &times;
                </button>
            </div>
            <div id="modalContent"
                class="p-3 sm:p-4
                   max-h-[75vh]
                   overflow-y-auto
                   overflow-x-auto">
                Loading...
            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        function closeModal() {
            $('#monthModal').addClass('hidden');
            $('#modalContent').html('Loading...');
        }

        $(document).ready(function() {

            $('#application-status-reports-table').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                searching: false,
                info: false,
                ordering: false,
                responsive: false,
                scrollX: true,
                autoWidth: false,

                ajax: {
                    url: "{{ route('admin.report.application-status') }}",
                    type: "GET",
                    dataSrc: function(json) {

                        const grandTotal = json.grand_total || {};

                        $('#grandInProcess').text(
                            Number(
                                grandTotal.in_process || 0
                            ).toLocaleString()
                        );

                        $('#grandAppointmentScheduled').text(
                            Number(
                                grandTotal.appointment_scheduled || 0
                            ).toLocaleString()
                        );

                        $('#grandPaymentSuccess').text(
                            Number(
                                grandTotal.payment_success || 0
                            ).toLocaleString()
                        );

                        $('#grandPaymentFailed').text(
                            Number(
                                grandTotal.payment_failed || 0
                            ).toLocaleString()
                        );

                        $('#grandInsufficientDocuments').text(
                            Number(
                                grandTotal.insufficient_documents || 0
                            ).toLocaleString()
                        );

                        return json.data || [];

                    },

                    error: function(xhr) {

                        console.error(
                            'Application Status Report Error:',
                            xhr.responseText
                        );

                    }

                },

                columns: [{
                        data: 'year',
                        name: 'year',
                        className: 'text-gray-700'
                    },
                    {
                        data: 'month_name',
                        name: 'month_name',
                        className: 'text-gray-700'
                    },
                    {
                        data: 'in_process',
                        name: 'in_process',
                        className: 'text-gray-700 text-right',
                        render: function(data) {
                            return Number(
                                data || 0
                            ).toLocaleString();
                        }
                    },
                    {
                        data: 'appointment_scheduled',
                        name: 'appointment_scheduled',
                        className: 'text-gray-700 text-right',
                        render: function(data) {
                            return Number(
                                data || 0
                            ).toLocaleString();
                        }
                    },
                    {
                        data: 'payment_success',
                        name: 'payment_success',
                        className: 'text-gray-700 text-right',
                        render: function(data) {
                            return Number(
                                data || 0
                            ).toLocaleString();
                        }
                    },
                    {
                        data: 'payment_failed',
                        name: 'payment_failed',
                        className: 'text-gray-700 text-right',
                        render: function(data) {
                            return Number(
                                data || 0
                            ).toLocaleString();
                        }
                    },
                    {
                        data: 'insufficient_documents',
                        name: 'insufficient_documents',
                        className: 'text-gray-700 text-right',
                        render: function(data) {
                            return Number(
                                data || 0
                            ).toLocaleString();
                        }
                    },
                    {
                        data: null,
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                            <button
                                type="button"
                                class="view-month px-4 py-1 text-sm font-medium
                                       text-blue-600 border border-blue-600
                                       rounded-lg hover:bg-blue-600
                                       hover:text-white transition"
                                data-year="${row.year}"
                                data-month="${row.month_no}">
                                View Datewise
                            </button>
                        `;
                        }
                    }
                ]
            });

            $(document).on('click', '.view-month', function(e) {

                e.preventDefault();
                const year = $(this).data('year');
                const month = $(this).data('month');
                $('#monthModal').removeClass('hidden');
                $('#modalContent').html(`
                    <div class="text-center py-6 text-gray-500">
                        Loading...
                    </div>
                `);
                $.ajax({

                    url: "{{ route('admin.report.application-status.month.details') }}",
                    type: "GET",
                    data: {
                        year: year,
                        month: month
                    },
                    success: function(res) {
                        const rows = res.data || [];
                        const grandTotal = res.grand_total || {};
                        const monthName = new Date(
                            year,
                            month - 1,
                            1
                        ).toLocaleString(
                            'default', {
                                month: 'long'
                            }
                        );
                        let html = `
                                <table class="w-full border text-sm">
                                    <thead class="bg-blue-50">
                                        <tr>
                                            <th class="border text-gray-600 font-semibold p-2 text-left uppercase">Date</th>
                                            <th class="border text-gray-600 font-semibold p-2 text-left uppercase">In Process</th>
                                            <th class="border text-gray-600 font-semibold p-2 text-left uppercase">Appointment Scheduled</th>
                                            <th class="border text-gray-600 font-semibold p-2 text-left uppercase">POV Success</th>
                                            <th class="border text-gray-600 font-semibold p-2 text-left uppercase">POV Failed</th>
                                            <th class="border text-gray-600 font-semibold p-2 text-left uppercase">POV Insufficient Documents</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        if (rows.length > 0) {

                            rows.forEach(function(row) {
                                html += `
                                    <tr>
                                        <td class="border p-2 text-gray-700">${row.report_date ?? '-'}</td>
                                        <td class="border p-2 text-gray-700 text-right">${Number(row.in_process || 0).toLocaleString()}</td>
                                        <td class="border p-2 text-gray-700 text-right">${Number(row.appointment_scheduled || 0).toLocaleString()}</td>
                                        <td class="border p-2 text-gray-700 text-right">${Number(row.payment_success || 0).toLocaleString()}</td>
                                        <td class="border p-2 text-gray-700 text-right">${Number(row.payment_failed || 0).toLocaleString()}</td>
                                        <td class="border p-2 text-gray-700 text-right">${Number(row.insufficient_documents || 0).toLocaleString()}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            html += `
                                <tr>
                                    <td colspan="5" class="border p-3 text-center">No data found</td>
                                </tr>
                            `;
                        }
                        html += `
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-bold bg-gray-100">

                                            <td class="border text-gray-600 font-bold p-2 text-right">
                                                Grand Total
                                            </td>
                                            <td class="border text-gray-600 font-bold p-2 text-right">
                                                ${Number(
                                                    grandTotal.in_process || 0
                                                ).toLocaleString()}
                                            </td>
                                            <td class="border text-gray-600 font-bold p-2 text-right">
                                                ${Number(
                                                    grandTotal.appointment_scheduled || 0
                                                ).toLocaleString()}
                                            </td>
                                            <td class="border text-gray-600 font-bold p-2 text-right">
                                                ${Number(
                                                    grandTotal.payment_success || 0
                                                ).toLocaleString()}
                                            </td>
                                            <td class="border text-gray-600 font-bold p-2 text-right">
                                                ${Number(
                                                    grandTotal.payment_failed || 0
                                                ).toLocaleString()}
                                            </td>
                                            <td class="border text-gray-600 font-bold p-2 text-right">
                                                ${Number(
                                                    grandTotal.insufficient_documents || 0
                                                ).toLocaleString()}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        `;
                        $('#modalContent').html(html);
                    },

                    error: function(xhr) {
                        console.error(
                            'Monthly Details Error:',
                            xhr.responseText
                        );

                        $('#modalContent').html(`
                            <div class="text-center py-6">
                                <p class="text-red-500 font-medium">
                                    Failed to load data.
                                </p>
                                <button
                                    type="button"
                                    onclick="closeModal()"
                                    class="mt-3 px-4 py-2 bg-gray-200
                                           rounded-lg hover:bg-gray-300">
                                    Close
                                </button>
                            </div>
                        `);
                    }
                });
            });

            $('#monthModal').on('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        });
    </script>
@endpush
