@extends('layouts.app')

@section('title', 'Lead Reports')

@section('content')

    <style>
        html,
        body {
            overflow-x: hidden;
        }

        .dataTables_wrapper,
        .dataTables_scroll,
        .dataTables_scrollBody {
            overflow: visible !important;
            max-height: none !important;
        }

        #lead-reports-table {
            width: 100% !important;
        }
    </style>

    <div class="mx-auto overflow-visible">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4 sm:p-4 lg:p-8">

                <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                    <h2
                        class="text-xl mb-3 sm:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Lead Report
                    </h2>
                </div>

                <div class="mt-4 text-sm text-gray-700">

                    <table id="lead-reports-table" class="min-w-full divide-y divide-gray-200 border-separate">
                        <thead class="bg-blue-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Year</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Month</th>
                                <th class="px-6 py-4 !text-right text-xs font-semibold text-gray-600 uppercase">Total Leads
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase w-40">
                                    Datewise</th>
                            </tr>
                        </thead>

                        <tbody></tbody>

                        <tfoot>
                            <tr>
                                <th colspan="2" class="!text-right font-bold">Grand Total</th>
                                <th id="grandTotal" class="!text-right font-bold">0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="monthModal"
        class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-start justify-center pt-10 z-50 p-4 lg:p-0">

        <div class="bg-white w-[500px] rounded-lg shadow-lg">

            <div class="flex justify-between items-center border-b p-3">
                <h2 class="text-lg font-bold">Monthly Lead Details</h2>
                <button onclick="closeModal()" class="text-gray-500 font-bold">X</button>
            </div>

            <!-- SCROLL ENABLED HERE -->
            <div id="modalContent" class="p-3 max-h-[70vh] overflow-y-auto">
                Loading...
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function closeModal() {
            $('#monthModal').addClass('hidden');
        }

        $(document).ready(function() {

            $('#lead-reports-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                paging: false,
                searching: false,
                info: false,

                scrollY: false,
                scrollX: false,
                scrollCollapse: false,

                order: [
                    [0, 'desc']
                ],

                ajax: {
                    url: "{{ route('admin.report.lead') }}",
                    dataSrc: function(json) {
                        $('#grandTotal').html(json.grand_total ?? 0);
                        return json.data;
                    }
                },

                columns: [{
                        data: 'year'
                    },
                    {
                        data: 'month_name'
                    },
                    {
                        data: 'total',
                        className: "text-right"
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                        <div class="flex justify-center">
                            <button class="view-month px-4 py-1 text-sm font-medium text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition"
                                data-year="${row.year}"
                                data-month="${row.month_no}">
                                View Datewise
                            </button>
                        </div>
                    `;
                        }
                    }
                ]
            });
        });

        $(document).on('click', '.view-month', function() {

            let year = $(this).data('year');
            let month = $(this).data('month');

            $('#monthModal').removeClass('hidden');
            $('#modalContent').html("Loading...");

            $.ajax({
                url: "{{ route('admin.report.lead.month.details') }}",
                type: "GET",
                dataType: "json",
                data: {
                    year,
                    month
                },
                success: function(res) {

                    let html = `
                <table class="w-full border">
                    <thead>
                        <tr>
                            <th class="border !text-left p-4">Date</th>
                            <th class="border !text-right p-4">Leads</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

                    if (res.data && res.data.length > 0) {
                        res.data.forEach(row => {
                            html += `
                        <tr>
                            <td class="border p-4">${row.date ?? '-'}</td>
                            <td class="border p-4 !text-right">${row.total ?? 0}</td>
                        </tr>
                    `;
                        });
                    } else {
                        html += `
                    <tr>
                        <td colspan="2" class="border p-4 text-center">No data found</td>
                    </tr>
                `;
                    }

                    html += `
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="border p-4 !text-right">Total</th>
                            <th class="border p-4 !text-right">${res.grand_total ?? 0}</th>
                        </tr>
                    </tfoot>
                </table>
            `;

                    $('#modalContent').html(html);
                },
                error: function() {
                    $('#modalContent').html('<p class="text-red-500">Failed to load data.</p>');
                }
            });
        });
    </script>
@endpush
