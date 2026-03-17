@extends('layouts.app')

@section('title', 'Customers')

@section('content')

<div class="mx-auto">

    <div class="bg-white rounded-xl shadow-lg border border-gray-100">

        <div class="p-4 sm:p-6 lg:p-8">

            <form id="filterForm">

                <div class="flex flex-col lg:flex-row justify-between items-center mb-6">

                    <h2
                        class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        CUSTOMERS
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

                        <div>
                            <label class="text-sm">Status</label>
                            <select id="status" class="border rounded-lg px-3 py-2 text-sm sm:w-32">
                                <option value="">All</option>
                                <option value="paid">Paid</option>
                                <option value="lead">Lead</option>
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

                    <table id="customers-table" class="min-w-full divide-y divide-gray-200 pt-5">

                        <thead class="bg-blue-50">

                            <tr>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mobile
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created
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

    let table = $('#customers-table').DataTable({

        processing: true,
        serverSide: true,

        order: [
            [0, 'desc']
        ],

        ajax: {
            url: "{{ route('admin.customers.data') }}",
            data: function(d) {
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.status = $('#status').val();
            }
        },

        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'name',
                name: 'first_name'
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
                data: 'status',
                name: 'is_paid',
                render: function(data) {
                    return data === 'paid' ?
                        '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">Paid</span>' :
                        '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">Lead</span>';
                }
            },
            {
                data: 'created_at',
                name: 'created_at'
            }
        ],

        dom: 'Blfrtip',

        buttons: ['copy', 'excel', 'csv', 'pdf'],

        pageLength: 10

    });

    $('#filter').click(function() {
        table.draw();
    });

});
</script>
@endpush