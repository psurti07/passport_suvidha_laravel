@extends('layouts.app')

@section('title', 'Leads')

@section('content')

<div class="mx-auto">

    <div class="bg-white rounded-xl shadow-lg border border-gray-100">

        <div class="p-4 sm:p-6 lg:p-8">

            <div class="flex flex-col lg:flex-row justify-between items-center mb-6">

                <h2
                    class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                    TODAY'S LEADS
                </h2>
                <div class="flex flex-wrap gap-3">

                    <div>
                        <label class="text-sm">Service</label>
                        <select id="service" class="border rounded-lg px-3 py-2 text-sm sm:w-32">
                            <option value="">All</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}">
                                {{ $service->service_name }}
                            </option>
                            @endforeach
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

            <div class="mt-4 overflow-x-auto">
                <div class="whitespace-nowrap text-sm text-gray-700">

                    <table id="today-table" class="min-w-full divide-y divide-gray-200 pt-5">

                        <thead class="bg-blue-50">

                            <tr>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Service
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mobile
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

    let table = $('#today-table').DataTable({

        processing: true,
        serverSide: true,

        order: [
            [0, 'desc']
        ],

        ajax: {
            url: "{{ route('admin.leads.today.data') }}",
            data: function(d) {
                d.service = $('#service').val();
            }
        },

        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'service_name',
                name: 'service_name'
            },
            {
                data: 'customer_name',
                name: 'customer_name'
            },
            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'mobile_number',
                name: 'mobile_number'
            },
            {
                data: 'is_paid',
                name: 'is_paid'
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
                    columns: ':not(:last-child)',
                    format: {
                        body: function(data, row, column, node) {
                            if (column === 1) {
                                return $(node).text().replace('🟢', '').replace('⚪', '').trim();
                            }
                            return $(node).text();
                        }
                    }
                }
            },
            {
                extend: 'excel',
                exportOptions: {
                    columns: ':not(:last-child)',
                    format: {
                        body: function(data, row, column, node) {
                            if (column === 1) {
                                return $(node).text().replace('🟢', '').replace('⚪', '').trim();
                            }
                            return $(node).text();
                        }
                    }
                }
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':not(:last-child)',
                    format: {
                        body: function(data, row, column, node) {
                            if (column === 1) {
                                return $(node).text().replace('🟢', '').replace('⚪', '').trim();
                            }
                            return $(node).text();
                        }
                    }
                }
            },
            {
                extend: 'pdf',
                exportOptions: {
                    columns: ':not(:last-child)',
                    format: {
                        body: function(data, row, column, node) {
                            if (column === 1) {
                                return $(node).text().replace('🟢', '').replace('⚪', '').trim();
                            }
                            return $(node).text();
                        }
                    }
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