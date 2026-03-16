@extends('layouts.app')

@section('title', 'GST Report') {{-- Set the page title --}}

@section('content')
    {{-- Add Toastify CSS and JS --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    {{-- Add SweetAlert2 CSS and JS (Optional - Keep if you plan delete/confirmation actions) --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

    {{-- Custom Toastify styles --}}
    <style>
        .toastify {
            background: none !important;
            padding: 0 !important;
            box-shadow: none !important;
            opacity: 0;
            transform: translateX(100%);
            animation: slideIn 0.3s ease forwards;
        }
        .toastify.toastify-right { right: 16px; }
        @keyframes slideIn { to { opacity: 1; transform: translateX(0); } }
        .toast-content { backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }
        /* Sticky header styles */
        thead th { position: sticky; top: 0; z-index: 10; }
        /* Ensure table container allows scrolling */
        .table-container { max-height: 60vh; overflow-y: auto; }
    </style>

    <div class="mx-auto">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4 sm:p-6 lg:p-8">
                <form id="filterForm" action="{{ route('admin.reports.gst') }}" method="GET">
                    <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
                        <div class="flex items-center gap-4">
                            <h2 class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                                GST REPORT
                            </h2>                                                    
                        </div>
                        <div class="flex flex-col lg:flex-row flex-wrap gap-4 lg:gap-2">
                            <div class="flex flex-col sm:flex-row items-center gap-2">
                                <label class="text-sm font-medium text-gray-700 whitespace-nowrap">From:</label>
                                <input type="date" name="from_date" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm w-full sm:w-40" value="{{ request('from_date') }}">
                            </div>
                            <div class="flex flex-col sm:flex-row items-center gap-2">
                                <label class="text-sm font-medium text-gray-700 whitespace-nowrap">To:</label>
                                <input type="date" name="to_date" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm w-full sm:w-40" value="{{ request('to_date') }}">
                            </div>
                            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'inv_date') }}">
                            <input type="hidden" name="sort_direction" value="{{ request('sort_direction', 'desc') }}">
                            <input type="hidden" id="perPageInput" name="per_page" value="{{ request('per_page', 10) }}">
                            <button type="submit" class="w-full lg:w-auto px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                Show Results
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row justify-between items-center mt-6 gap-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" onclick="copyToClipboard()" class="inline-flex items-center px-4 py-2 bg-white text-blue-800 rounded-lg text-sm font-medium shadow-md border border-gray-200 hover:bg-gray-50">
                                Copy
                            </button>
                            {{-- Update export routes if needed --}}
                            <a href="{{ route('admin.reports.gst', ['export' => 'excel'] + request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-white text-blue-800 rounded-lg text-sm font-medium shadow-md border border-gray-200 hover:bg-gray-50">
                                Excel
                            </a>
                            <a href="{{ route('admin.reports.gst', ['export' => 'csv'] + request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-white text-blue-800 rounded-lg text-sm font-medium shadow-md border border-gray-200 hover:bg-gray-50">
                                CSV
                            </a>
                            <a href="{{ route('admin.reports.gst', ['export' => 'pdf'] + request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-white text-blue-800 rounded-lg text-sm font-medium shadow-md border border-gray-200 hover:bg-gray-50">
                                PDF
                            </a>
                        </div>
                        <div class="flex items-center gap-2 w-full lg:w-auto">
                            <label for="searchInput" class="text-sm font-medium text-gray-700">Search:</label>
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search report..." class="border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm flex-grow">
                        </div>
                    </div>
                </form>

                <script>
                    // Toast Notification Function (copied from users index)
                    function showToast(message, type = 'success') {
                        // ... (Keep the existing showToast function code here) ...
                        let bgColorClass, textColorClass, iconSvg, borderColorClass;

                        switch (type) {
                            case 'success':
                                bgColorClass = 'bg-emerald-500/95'; textColorClass = 'text-white'; borderColorClass = 'border-emerald-400';
                                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>`;
                                break;
                            case 'error':
                                bgColorClass = 'bg-red-500/95'; textColorClass = 'text-white'; borderColorClass = 'border-red-400';
                                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>`;
                                break;
                            case 'warning':
                                bgColorClass = 'bg-amber-500/95'; textColorClass = 'text-white'; borderColorClass = 'border-amber-400';
                                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>`;
                                break;
                            default: // Info
                                bgColorClass = 'bg-blue-500/95'; textColorClass = 'text-white'; borderColorClass = 'border-blue-400';
                                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>`;
                        }

                        const toastNode = document.createElement('div');
                        toastNode.className = `toast-content flex items-center w-full max-w-sm p-4 mb-4 ${bgColorClass} ${textColorClass} rounded-lg shadow-2xl border ${borderColorClass} backdrop-blur`;
                        toastNode.innerHTML = `
                            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${textColorClass}">${iconSvg}</div>
                            <div class="ml-3 text-sm font-normal">${message}</div>
                            <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8 ${textColorClass} hover:${bgColorClass.replace('/95', '')} focus:outline-none"><span class="sr-only">Close</span><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg></button>`;

                        const closeButton = toastNode.querySelector('button');
                        closeButton.addEventListener('click', function() {
                            const toast = this.closest('.toastify');
                            if (toast) { toast.style.opacity = '0'; toast.style.transform = 'translateX(100%)'; setTimeout(() => toast.remove(), 300); }
                        });

                        Toastify({ node: toastNode, duration: 5000, gravity: "top", position: "right", stopOnFocus: true }).showToast();
                    }

                    // Copy to Clipboard Function (adapted)
                    function copyToClipboard() {
                        const table = document.querySelector('table');
                        const rows = Array.from(table.querySelectorAll('tbody tr'));
                        if (rows.length === 1 && rows[0].querySelector('td[colspan="15"]')) { // Updated colspan
                            showToast('No data to copy.', 'warning'); return;
                        }
                        const header = Array.from(table.querySelectorAll('thead th'))
                            .map(th => th.innerText.trim())
                            // .slice(0, -1) // Remove this if there's no 'Action' column
                            .join('\t');
                        let text = header + '\n';
                        text += rows.map(row => {
                            if (row.querySelector('td[colspan="15"]')) return ''; // Updated colspan
                            return Array.from(row.querySelectorAll('td'))
                                // .slice(0, -1) // Remove this if there's no 'Action' column
                                .map(cell => cell.textContent.trim())
                                .join('\t');
                        }).filter(rowText => rowText !== '').join('\n');
                        navigator.clipboard.writeText(text).then(() => {
                            showToast('Table data copied to clipboard!', 'success');
                        }).catch(err => {
                            console.error('Failed to copy text: ', err);
                            showToast('Failed to copy data to clipboard.', 'error');
                        });
                    }

                    document.addEventListener('DOMContentLoaded', function () {
                        // Check for flashed session messages
                        @if(session('success')) showToast("{{ session('success') }}", 'success'); @endif
                        @if(session('error')) showToast("{{ session('error') }}", 'error'); @endif
                    });
                </script>

                @php
                    // Sorting Link Helper Function (copied and adapted)
                    function sortLink($column, $label) {
                        $sortBy = request('sort_by', 'inv_date'); // Default sort
                        $sortDirection = request('sort_direction', 'desc');
                        $newDirection = ($sortBy == $column && $sortDirection == 'asc') ? 'desc' : 'asc';
                        $queryParams = array_merge(request()->except('page'), ['sort_by' => $column, 'sort_direction' => $newDirection]);
                        $icon = '';
                        if ($sortBy == $column) {
                            $icon = $sortDirection == 'asc'
                                ? '<svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg>'
                                : '<svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>';
                        }
                        return '<a href="' . route('admin.reports.gst', $queryParams) . '" class="flex items-center hover:text-blue-700">' . $label . $icon . '</a>';
                    }
                    // Assume $gstReportData is passed from controller, similar to $users
                    $gstReportData = $gstData ?? collect(); // Use the passed data or an empty collection
                    if ($gstReportData instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                        $paginatedData = $gstReportData;
                    } else {
                        // Manually paginate if it's a simple collection/array (adjust as needed)
                        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                            $gstReportData->forPage(request('page', 1), request('per_page', 10)),
                            $gstReportData->count(),
                            request('per_page', 10),
                            request('page', 1),
                            ['path' => request()->url(), 'query' => request()->query()]
                        );
                        $paginatedData = $paginator;
                    }
                @endphp

                <div class="mt-8 overflow-x-auto table-container">
                    <div class="inline-block min-w-full align-middle">
                        <div class="shadow-sm ring-1 ring-black ring-opacity-5">
                            <table class="min-w-full divide-y divide-gray-200 relative">
                                <thead class="bg-blue-50">
                                    <tr>
                                        {{-- Adjust column headers and sort keys --}}
                                        <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">#</th>
                                        <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">{!! sortLink('inv_date', 'INV Date') !!}</th>
                                        <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">{!! sortLink('inv_no', 'INV #') !!}</th>
                                        <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">{!! sortLink('net_amount', 'Net Amount') !!}</th>
                                        <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">{!! sortLink('cgst', 'CGST') !!}</th>
                                        <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">{!! sortLink('sgst', 'SGST') !!}</th>
                                        <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">{!! sortLink('igst', 'IGST') !!}</th>
                                        <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">{!! sortLink('total_amount', 'Total Amount') !!}</th>
                                        <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">{!! sortLink('fullname', 'Fullname') !!}</th>
                                        <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">{!! sortLink('mobile', 'Mobile') !!}</th>
                                        <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">{!! sortLink('email', 'Email') !!}</th>
                                        <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">{!! sortLink('gst_no', 'GST No') !!}</th>
                                        <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">{!! sortLink('city', 'City') !!}</th>
                                        <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">{!! sortLink('state', 'State') !!}</th>
                                        {{-- Add Action column if needed --}}
                                        {{-- <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Action</th> --}}
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @forelse ($paginatedData as $index => $item)
                                        <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-colors duration-150">
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $paginatedData->firstItem() + $index }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item['inv_date'] ? \Carbon\Carbon::parse($item['inv_date'])->format('d/m/Y') : '' }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item['inv_no'] ?? '' }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($item['net_amount'] ?? 0, 2) }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($item['cgst'] ?? 0, 2) }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($item['sgst'] ?? 0, 2) }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($item['igst'] ?? 0, 2) }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($item['total_amount'] ?? 0, 2) }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item['fullname'] ?? '' }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item['mobile'] ?? '' }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ strtolower($item['email'] ?? '') }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item['gst_no'] ?? '' }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item['city'] ?? '' }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item['state'] ?? '' }}</td>                                                                                        
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="14" class="px-6 py-10 text-center"> {{-- Updated colspan --}}
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <p class="mt-4 text-gray-500 text-sm font-medium">No GST data found</p>
                                                    <p class="mt-1 text-gray-400 text-xs">Try adjusting your search or filter criteria.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Pagination Section --}}
                <div class="mt-6 px-4 sm:px-0 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-4">
                        @if ($paginatedData->total() > 0)
                            <p class="text-sm text-gray-500">
                                Showing <span class="font-medium">{{ $paginatedData->firstItem() }}</span> to <span class="font-medium">{{ $paginatedData->lastItem() }}</span> of <span class="font-medium">{{ $paginatedData->total() }}</span> results
                            </p>
                        @endif
                        @php $options = [10, 25, 50, 100]; @endphp
                        @if ($paginatedData->total() > ($options[0] ?? 10))
                            <div class="flex items-center gap-2">
                                <label for="perPageBottom" class="text-sm font-medium text-gray-700">Per Page:</label>
                                <select id="perPageBottom" onchange="document.getElementById('perPageInput').value = this.value; document.getElementById('filterForm').submit();" class="border border-gray-300 rounded-lg text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                                    @foreach ($options as $option)
                                        <option value="{{ $option }}" {{ request('per_page', 10) == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                    <div>
                        @if ($paginatedData->hasPages())
                            {{ $paginatedData->appends(request()->query())->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection