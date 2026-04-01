@extends('layouts.app')

@section('title', 'Final Details')

@section('content')
    {{-- Add Toastify CSS and JS in the head --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    {{-- Add SweetAlert2 CSS and JS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Override Toastify default styles and add animations --}}
    <style>
        .toastify {
            background: none !important;
            padding: 0 !important;
            box-shadow: none !important;
            opacity: 0;
            transform: translateX(100%);
            animation: slideIn 0.3s ease forwards;
        }

        .toastify.toastify-right {
            right: 16px;
        }

        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .toast-content {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
    </style>

    <div class="mx-auto">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4 sm:p-6 lg:p-8">
                <form id="filterForm" action="{{ route('admin.final-details.index') }}" method="GET">
                    <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 sm:space-y-0">
                        <div class="flex items-center gap-4">
                            <h2
                                class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                                FINAL DETAILS
                            </h2>
                        </div>
                        <div class="flex items-center gap-4">

                            <label for="approvalStatus" class="text-sm font-medium text-gray-700">Approval Status:</label>
                            <select id="approvalStatus" name="approval_status"
                                onchange="document.getElementById('filterForm').submit()"
                                class="border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                                <option value="">All</option>
                                <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>
                                    Approved</option>
                                <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>
                                    Pending</option>
                            </select>

                            <label for="searchInput" class="text-sm font-medium text-gray-700">Search:</label>
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                placeholder="Search by mobile number"
                                class="border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm w-64"
                                onkeypress="handleSearchKeyPress(event)">

                            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'id') }}">
                            <input type="hidden" name="sort_direction" value="{{ request('sort_direction', 'desc') }}">
                            <input type="hidden" id="perPageInput" name="per_page" value="{{ request('per_page', 10) }}">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Search
                            </button>
                        </div>
                    </div>
                </form>

                <script>
                    function showToast(message, type = 'success') {
                        let bgColorClass, textColorClass, iconSvg, borderColorClass;

                        switch (type) {
                            case 'success':
                                bgColorClass = 'bg-emerald-500/95';
                                textColorClass = 'text-white';
                                borderColorClass = 'border-emerald-400';
                                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>`;
                                break;
                            case 'error':
                                bgColorClass = 'bg-red-500/95';
                                textColorClass = 'text-white';
                                borderColorClass = 'border-red-400';
                                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>`;
                                break;
                            case 'warning':
                                bgColorClass = 'bg-amber-500/95';
                                textColorClass = 'text-white';
                                borderColorClass = 'border-amber-400';
                                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                </svg>`;
                                break;
                            default: // Info
                                bgColorClass = 'bg-blue-500/95';
                                textColorClass = 'text-white';
                                borderColorClass = 'border-blue-400';
                                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>`;
                        }

                        const toastNode = document.createElement('div');
                        toastNode.className =
                            `toast-content flex items-center w-full max-w-sm p-4 mb-4 ${bgColorClass} ${textColorClass} rounded-lg shadow-2xl border ${borderColorClass} backdrop-blur`;
                        toastNode.innerHTML = `
                            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${textColorClass}">
                                ${iconSvg}
                            </div>
                            <div class="ml-3 text-sm font-normal">${message}</div>
                            <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8 ${textColorClass} hover:${bgColorClass.replace('/95', '')} focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </button>`;

                        const closeButton = toastNode.querySelector('button');
                        closeButton.addEventListener('click', function() {
                            const toast = this.closest('.toastify');
                            if (toast) {
                                toast.style.opacity = '0';
                                toast.style.transform = 'translateX(100%)';
                                setTimeout(() => toast.remove(), 300);
                            }
                        });

                        Toastify({
                            node: toastNode,
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            stopOnFocus: true,
                        }).showToast();
                    }

                    function handleSearchKeyPress(event) {
                        if (event.key === 'Enter' || event.keyCode === 13) {
                            event.preventDefault();
                            document.getElementById('filterForm').submit();
                        }
                    }

                    document.addEventListener('DOMContentLoaded', function() {
                        // Check for flashed session messages and show toasts
                        @if (session('success'))
                            showToast("{{ session('success') }}", 'success');
                        @endif
                        @if (session('error'))
                            showToast("{{ session('error') }}", 'error');
                        @endif
                    });
                </script>

                @php
                    $sortBy = request('sort_by', 'id');
                    $sortDirection = request('sort_direction', 'desc');
                    function sortLink($column, $label)
                    {
                        $sortBy = request('sort_by', 'id');
                        $sortDirection = request('sort_direction', 'desc');
                        $newDirection = $sortBy == $column && $sortDirection == 'desc' ? 'asc' : 'desc';
                        $queryParams = array_merge(request()->except('page'), [
                            'sort_by' => $column,
                            'sort_direction' => $newDirection,
                        ]);
                        $icon = '';
                        if ($sortBy == $column) {
                            $icon =
                                $sortDirection == 'asc'
                                    ? '<svg class="w-3 h-3 ms-1 inline" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.12 2.12 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/></svg>'
                                    : '<svg class="w-3 h-3 ms-1 inline" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.12 2.12 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/></svg>';
                        }
                        return '<a href="' .
                            route('admin.final-details.index', $queryParams) .
                            '" class="text-gray-700 group flex items-center">' .
                            $label .
                            ' ' .
                            $icon .
                            '</a>';
                    }
                @endphp

                <div class="mt-8 overflow-x-auto">
                    <div class="inline-block min-w-full align-middle max-h-[60vh] overflow-y-auto">
                        <div class="shadow-sm ring-1 ring-black ring-opacity-5">
                            <table class="min-w-full divide-y divide-gray-200 relative">
                                <thead class="bg-blue-50">
                                    <tr>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            {!! sortLink('id', 'ID') !!}
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            {!! sortLink('customer_id', 'Customer') !!}
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            {!! sortLink('upload_date', 'Upload Date') !!}
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            {!! sortLink('uploaded_by', 'Uploaded By') !!}
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            {!! sortLink('is_approved', 'Status') !!}
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            {!! sortLink('approved_date', 'Approved Date') !!}
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            Approved By
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            Document
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($finalDetails as $finalDetail)
                                        <tr
                                            class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-colors duration-150">
                                            <th scope="row"
                                                class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $finalDetail->id }}
                                            </th>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $finalDetail->customer->full_name ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $finalDetail->upload_date ? $finalDetail->upload_date->format('d M Y, h:i A') : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $finalDetail->uploader->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if ($finalDetail->is_approved)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Approved
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $finalDetail->approved_date ? $finalDetail->approved_date->format('d M Y, h:i A') : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $finalDetail->approverName }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-blue-600">
                                                <a href="{{ Storage::url($finalDetail->file_path) }}" target="_blank"
                                                    class="hover:underline inline-flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </a>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('admin.final-details.show', $finalDetail) }}"
                                                        class="text-blue-600 hover:text-blue-900" title="View Details">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('admin.final-details.edit', $finalDetail) }}"
                                                        class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>

                                                    @if (!$finalDetail->is_approved)
                                                        <form action="{{ route('admin.final-details.approve', $finalDetail) }}"
                                                            method="POST" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                class="text-green-600 hover:text-green-900 flex"
                                                                title="Approve">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                                Approve
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('admin.final-details.unapprove', $finalDetail) }}"
                                                            method="POST" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                class="text-yellow-600 hover:text-yellow-900 flex"
                                                                title="Unapprove">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                                Unapprove
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="px-6 py-10 text-center">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-300" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <p class="mt-4 text-gray-500 text-sm font-medium">No final details found
                                                    </p>
                                                    @if (request('search') || request('approval_status'))
                                                        <p class="mt-1 text-gray-400 text-xs">Try adjusting your search or
                                                            status filters</p>
                                                        <a href="{{ route('admin.final-details.index') }}"
                                                            class="mt-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                            Clear Filters
                                                        </a>
                                                    @else
                                                        <p class="mt-1 text-gray-400 text-xs">No final details available at
                                                            this time</p>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-6 px-4 sm:px-0 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-4">
                        @if ($finalDetails->total() > 0)
                            <p class="text-sm text-gray-500">
                                Showing
                                <span class="font-medium">{{ $finalDetails->firstItem() }}</span>
                                to
                                <span class="font-medium">{{ $finalDetails->lastItem() }}</span>
                                of
                                <span class="font-medium">{{ $finalDetails->total() }}</span>
                                results
                            </p>
                        @endif
                    </div>
                    <div>
                        {{ $finalDetails->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
