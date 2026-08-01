@extends('layouts.app')

@section('title', 'Service Reports')

@section('content')

    <style>
        html,
        body {
            overflow-x: hidden;
        }

        #service-reports-table {
            width: 100% !important;
        }
    </style>

    <div class="mx-auto">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">

            <div class="p-4 lg:p-8">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                    <h2
                        class="text-xl mb-3 sm:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        SERVICE REPORTS
                    </h2>
                </div>

                <table id="service-reports-table" class="min-w-full divide-y divide-gray-200 border-separate text-sm">

                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Year</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Month</th>
                            <th class="px-4 py-3 !text-right text-xs font-semibold text-gray-600 uppercase">NP36</th>
                            <th class="px-4 py-3 !text-right text-xs font-semibold text-gray-600 uppercase">NP60</th>
                            <th class="px-4 py-3 !text-right text-xs font-semibold text-gray-600 uppercase">TP36</th>
                            <th class="px-4 py-3 !text-right text-xs font-semibold text-gray-600 uppercase">TP60</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-40">Action</th>
                        </tr>
                    </thead>

                    <tbody></tbody>

                    <tfoot>
                        <tr>
                            <th colspan="2" class="!text-right text-gray-600 font-bold ">Grand Total</th>
                            <th id="grandNp36" class="!px-4 !py-3 !text-right text-gray-600 font-bold ">0</th>
                            <th id="grandNp60" class="!px-4 !py-3 !text-right text-gray-600 font-bold ">0</th>
                            <th id="grandTp36" class="!px-4 !py-3 !text-right text-gray-600 font-bold ">0</th>
                            <th id="grandTp60" class="!px-4 !py-3 !text-right text-gray-600 font-bold ">0</th>
                            <th></th>
                        </tr>
                    </tfoot>

                </table>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div id="monthModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-start justify-center pt-10 z-50">

        <div class="bg-white w-[500px] rounded-lg shadow-lg">

            <div class="flex justify-between items-center border-b p-3">
                <h2 class="text-lg font-bold">Monthly Service Details</h2>
                <button onclick="closeModal()" class="text-gray-500 font-bold">X</button>
            </div>

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

            $('#service-reports-table').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                searching: false,
                info: false,

                ajax: {
                    url: "{{ route('admin.report.service') }}",
                    dataSrc: function(json) {

                        let np36 = 0,
                            np60 = 0,
                            tp36 = 0,
                            tp60 = 0;

                        json.data.forEach(row => {
                            np36 += parseInt(row.np36 || 0);
                            np60 += parseInt(row.np60 || 0);
                            tp36 += parseInt(row.tp36 || 0);
                            tp60 += parseInt(row.tp60 || 0);
                        });

                        $('#grandNp36').text(np36);
                        $('#grandNp60').text(np60);
                        $('#grandTp36').text(tp36);
                        $('#grandTp60').text(tp60);

                        return json.data;
                    }
                },

                columns: [{
                        data: 'year',
                        className: 'text-gray-700'
                    },
                    {
                        data: 'month_name',
                        className: 'text-gray-700'
                    },

                    {
                        data: 'np36',
                        className: 'text-gray-700 text-right'
                    },
                    {
                        data: 'np60',
                        className: 'text-gray-700 text-right'
                    },
                    {
                        data: 'tp36',
                        className: 'text-gray-700 text-right'
                    },
                    {
                        data: 'tp60',
                        className: 'text-gray-700 text-right'
                    },

                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                        <button
                            class="view-month px-4 py-1 text-sm font-medium text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition"
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


        // MODAL AJAX
        $(document).on('click', '.view-month', function() {

            let year = $(this).data('year');
            let month = $(this).data('month');

            $('#monthModal').removeClass('hidden');
            $('#modalContent').html("Loading...");

            $.ajax({
                url: "{{ route('admin.report.service.month.details') }}",
                type: "GET",
                data: {
                    year,
                    month
                },

                success: function(res) {

                    let html = `
                <table class="w-full border text-sm">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="border text-gray-600 font-semibold p-2 text-left uppercase">Date</th>
                            <th class="border text-gray-600 font-semibold p-2 text-right uppercase">NP36</th>
                            <th class="border text-gray-600 font-semibold p-2 text-right uppercase">NP60</th>
                            <th class="border text-gray-600 font-semibold p-2 text-right uppercase">TP36</th>
                            <th class="border text-gray-600 font-semibold p-2 text-right uppercase">TP60</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

                    if (res.data.length > 0) {

                        res.data.forEach(row => {
                            html += `
                        <tr>
                            <td class="border p-2 text-gray-700 ">${row.report_date ?? '-'}</td>
                            <td class="border p-2 text-gray-700 text-right">${row.np36 ?? 0}</td>
                            <td class="border p-2 text-gray-700 text-right">${row.np60 ?? 0}</td>
                            <td class="border p-2 text-gray-700 text-right">${row.tp36 ?? 0}</td>
                            <td class="border p-2 text-gray-700 text-right">${row.tp60 ?? 0}</td>
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

                    // ✅ GRAND TOTAL FOOTER ADDED
                    html += `
                    </tbody>
                    <tfoot>
                        <tr class="font-bold bg-gray-100">
                            <td class="border text-gray-600 font-bold p-2 text-right">Grand Total</td>
                            <td class="border text-gray-600 font-bold p-2 text-right">${res.grand_total.np36}</td>
                            <td class="border text-gray-600 font-bold p-2 text-right">${res.grand_total.np60}</td>
                            <td class="border text-gray-600 font-bold p-2 text-right">${res.grand_total.tp36}</td>
                            <td class="border text-gray-600 font-bold p-2 text-right">${res.grand_total.tp60}</td>
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
