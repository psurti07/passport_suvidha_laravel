@extends('layouts.app')

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
                <form id="filterForm" action="{{ route('admin.users.index') }}" method="GET">
                    <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 sm:space-y-0">
                        <div class="flex items-center gap-4">
                            <h2
                                class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                                USERS
                            </h2>
                            <a href="{{ route('admin.users.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                New User
                            </a>
                        </div>
                        <div class="flex flex-row lg:flex-row flex-wrap gap-4 space-y-2 lg:space-y-0 lg:space-x-2">
                            <div
                                class="flex flex-col lg:flex-row gap-4 w-full sm:w-auto space-y-0 lg:space-y-0 lg:space-x-2">
                                <div class="flex flex-row sm:flex-row items-center gap-2 space-x-2">
                                    <label class="text-sm font-medium text-gray-700 whitespace-nowrap">From:</label>
                                    <input type="date" name="from_date"
                                        class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm w-full sm:w-40"
                                        value="{{ request('from_date') }}">
                                </div>
                                <div class="flex flex-row sm:flex-row items-center gap-2 space-x-2">
                                    <label class="text-sm font-medium text-gray-700 whitespace-nowrap">To:</label>
                                    <input type="date" name="to_date"
                                        class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm w-full sm:w-40"
                                        value="{{ request('to_date') }}">
                                </div>
                            </div>
                            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'id') }}">
                            <input type="hidden" name="sort_direction" value="{{ request('sort_direction', 'desc') }}">
                            <input type="hidden" id="perPageInput" name="per_page" value="{{ request('per_page', 10) }}">
                            <button type="submit"
                                class="w-full sm:w-auto px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                Show Results
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row justify-between items-center mt-6 gap-4">
                        <div class="flex flex-wrap items-center gap-2 space-x-2">
                            <button type="button" onclick="copyToClipboard()"
                                class="inline-flex items-center px-4 py-2 bg-white text-blue-800 rounded-lg text-sm font-medium shadow-md">
                                Copy
                            </button>
                            <a href="{{ route('admin.users.export', ['type' => 'excel'] + request()->all()) }}"
                                class="inline-flex items-center px-4 py-2 bg-white text-blue-800 rounded-lg text-sm font-medium shadow-md">
                                Excel
                            </a>
                            <a href="{{ route('admin.users.export', ['type' => 'csv'] + request()->all()) }}"
                                class="inline-flex items-center px-4 py-2 bg-white text-blue-800 rounded-lg text-sm font-medium shadow-md">
                                CSV
                            </a>
                            <a href="{{ route('admin.users.export', ['type' => 'pdf'] + request()->all()) }}"
                                class="inline-flex items-center px-4 py-2 bg-white text-blue-800 rounded-lg text-sm font-medium shadow-md">
                                PDF
                            </a>
                        </div>
                        <div class="flex items-center gap-4">
                            <label for="searchInput" class="text-sm font-medium text-gray-700">Search:</label>
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                placeholder="Search users..."
                                class="border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm w-64">
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
                        toastNode.className = `toast-content flex items-center w-full max-w-sm p-4 mb-4 ${bgColorClass} ${textColorClass} rounded-lg shadow-2xl border ${borderColorClass} backdrop-blur`;
                        toastNode.innerHTML = `
                            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${textColorClass}">
                                ${iconSvg}
                            </div>
                            <div class="ml-3 text-sm font-normal">${message}</div>
                            <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8 ${textColorClass} hover:${bgColorClass.replace('/95', '')} focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </button>`;

                        // Add click handler for close button
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

                    function copyToClipboard() {
                        const table = document.querySelector('table');
                        const rows = Array.from(table.querySelectorAll('tbody tr'));

                        // Check if the only row is the "No users found" row
                        if (rows.length === 1 && rows[0].querySelector('td[colspan="8"]')) {
                            showToast('No data to copy.', 'error');
                            return;
                        }

                        const header = Array.from(table.querySelectorAll('thead th'))
                            .map(th => th.innerText.trim())
                            .slice(0, -1) // Exclude the last 'Action' header
                            .join('\t'); // Join header cells with TAB

                        let text = header + '\n';

                        text += rows.map(row => {
                            // Skip the "No users found" row if it exists among other rows (though unlikely)
                            if (row.querySelector('td[colspan="8"]')) {
                                return '';
                            }
                            return Array.from(row.querySelectorAll('td'))
                                .slice(0, -1)
                                .map(cell => cell.textContent.trim())
                                .join('\t');
                        })
                        .filter(rowText => rowText !== '')
                        .join('\n');

                        navigator.clipboard.writeText(text).then(() => {
                            showToast('Table data copied to clipboard!', 'success');
                        }).catch(err => {
                            console.error('Failed to copy text: ', err);
                            showToast('Failed to copy data to clipboard.', 'error');
                        });
                    }

                    document.addEventListener('DOMContentLoaded', function () {
                        // SweetAlert2 delete confirmation
                        const deleteForms = document.querySelectorAll('.delete-user-form');
                        deleteForms.forEach(form => {
                            form.addEventListener('submit', function (event) {
                                event.preventDefault();
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: "You won't be able to revert this!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33', // Make confirm button red for delete
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Yes, delete it!',
                                    customClass: {
                                        popup: 'rounded-lg shadow-lg',
                                        title: 'text-lg font-semibold text-gray-800',
                                        confirmButton: 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 mx-1',
                                        cancelButton: 'px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 mx-1'
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        event.target.submit();
                                    }
                                });
                            });
                        });

                        // Check for flashed session messages and show toasts
                        @if(session('success'))
                            showToast("{{ session('success') }}", 'success');
                        @endif

                        @if(session('error'))
                            showToast("{{ session('error') }}", 'error');
                        @endif
                    });
                </script>

                @php
                    $sortBy = request('sort_by', 'id');
                    $sortDirection = request('sort_direction', 'asc');

                    function sortLink($column, $label)
                    {
                        $sortBy = request('sort_by', 'id');
                        $sortDirection = request('sort_direction', 'asc');
                        $newDirection = $sortBy == $column && $sortDirection == 'asc' ? 'desc' : 'asc';
                        $queryParams = array_merge(request()->except('page'), [
                            'sort_by' => $column,
                            'sort_direction' => $newDirection,
                        ]);

                        $icon = '';
                        if ($sortBy == $column) {
                            $icon =
                                $sortDirection == 'asc'
                                    ? '<svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg>'
                                    : '<svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>';
                        }

                        return '<a href="' .
                            route('admin.users.index', $queryParams) .
                            '" class="flex items-center hover:text-blue-700">' .
                            $label .
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
                                            {!! sortLink('created_at', 'Date') !!}
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            {!! sortLink('name', 'Full Name') !!}
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            {!! sortLink('email', 'Email Id') !!}
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            {!! sortLink('created_at', 'Created At') !!}
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            {!! sortLink('updated_at', 'Updated At') !!}
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @forelse ($users as $user)
                                        <tr
                                            class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-colors duration-150">
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $user->id }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $user->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $user->name }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ strtolower($user->email) }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $user->created_at->format('d/m/Y H:i:s') }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $user->updated_at->format('d/m/Y H:i:s') }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                                        class="text-blue-600 hover:text-blue-900" title="View User">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                            <path fill-rule="evenodd"
                                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                                        class="text-yellow-600 hover:text-yellow-900" title="Edit User">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path
                                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}"
                                                        method="POST" class="inline delete-user-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-900" title="Delete User">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                                viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-10 text-center">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-300" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                    </svg>
                                                    <p class="mt-4 text-gray-500 text-sm font-medium">No users found</p>
                                                    <p class="mt-1 text-gray-400 text-xs">Try adjusting your search or
                                                        filter to find what you're looking for.</p>
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
                    {{-- Left side: Results Summary and Per Page Dropdown --}}
                    <div class="flex items-center gap-4">
                        {{-- Show results summary if there are items --}}
                        @if ($users->total() > 0)
                            <p class="text-sm text-gray-500">
                                Showing
                                <span class="font-medium">{{ $users->firstItem() }}</span>
                                to
                                <span class="font-medium">{{ $users->lastItem() }}</span>
                                of
                                <span class="font-medium">{{ $users->total() }}</span>
                                results
                            </p>
                        @endif

                        {{-- Define options before using in the @if --}}
                        @php $options = [10, 25, 50, 100]; @endphp

                        {{-- Per Page Dropdown (only show if there are enough items) --}}
                        @if ($users->total() > ($options[0] ?? 10))
                            {{-- Use defined $options --}}
                            <div class="flex items-center gap-2">
                                <label for="perPageBottom" class="text-sm font-medium text-gray-700">Per Page:</label>
                                {{-- Update onchange to set hidden input and submit form --}}
                                <select id="perPageBottom"
                                    onchange="document.getElementById('perPageInput').value = this.value; document.getElementById('filterForm').submit();"
                                    class="border border-gray-300 rounded-lg text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                                    @foreach ($options as $option)
                                        <option value="{{ $option }}"
                                            {{ request('per_page', 10) == $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    {{-- Right side: Pagination Links --}}
                    <div>
                        {{-- Only show pagination if there are multiple pages --}}
                        @if ($users->hasPages())
                            {{ $users->appends(request()->query())->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
