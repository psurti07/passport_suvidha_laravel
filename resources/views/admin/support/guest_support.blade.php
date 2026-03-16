@extends('layouts.app')

@section('title', 'Guest User Support')

@section('content')
{{-- Placeholder for Toastify/SweetAlert if needed later --}}

<div class="mx-auto"> {{-- Changed container --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100"> {{-- Added wrapper div --}}
        <div class="p-4 sm:p-6 lg:p-8"> {{-- Added padding div --}}
            {{-- Header Section --}}
            <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 sm:space-y-0 mb-6">
                <div class="flex items-center gap-4">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        GUEST USER SUPPORT TICKETS
                    </h1>
                </div>
                {{-- Add Filters/Exports Here if needed --}}
            </div>

            {{-- Table Section --}}
            <div class="mt-8 overflow-x-auto">
                <div class="inline-block min-w-full align-middle max-h-[60vh] overflow-y-auto"> {{-- Added max-height and scroll --}}
                    <div class="shadow-sm ring-1 ring-black ring-opacity-5"> {{-- Added ring --}}
                        <table class="min-w-full divide-y divide-gray-200 relative"> {{-- Added relative --}}
                            <thead class="bg-blue-50"> {{-- Changed thead background --}}
                                <tr>
                                    {{-- Adjusted header cell styles --}}
                                    <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">ID</th>
                                    <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Subject</th>
                                    <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Guest Name</th>
                                    <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Guest Email</th>
                                    <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Status</th>
                                    <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Created At</th>
                                    <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white"> {{-- Removed divide-y --}}
                                @forelse ($tickets as $ticket)
                                    {{-- Adjusted row styles and added hover/clickable effect --}}
                                    <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-colors duration-150">
                                        {{-- Adjusted cell styles --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $ticket->id }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ Str::limit($ticket->subject, 50) }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $ticket->name }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $ticket->email }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            {{-- Adjusted status badge styles --}}
                                             <span @class([
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                'bg-green-100 text-green-800' => $ticket->status === 'open',
                                                'bg-yellow-100 text-yellow-800' => $ticket->status === 'in_progress',
                                                'bg-red-100 text-red-800' => $ticket->status === 'closed',
                                                'bg-gray-100 text-gray-800' => !in_array($ticket->status, ['open', 'in_progress', 'closed']),
                                            ])>
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-700">
                                            {{-- Action link - styling is less critical inside the cell, kept original for simplicity --}}
                                            <a href="{{ route('admin.support.tickets.show', $ticket->ticket_number) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                             {{-- Add other actions if needed --}}
                                        </td>
                                    </tr>
                                @empty
                                    {{-- Styled empty state --}}
                                    <tr>
                                         <td colspan="7" class="px-6 py-10 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                                <p class="mt-4 text-gray-500 text-sm font-medium">No guest tickets found</p>
                                                <p class="mt-1 text-gray-400 text-xs">There are currently no support tickets from guest users.</p>
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
                {{-- Left side: Results Summary and Per Page Dropdown --}}
                <div class="flex items-center gap-4">
                    {{-- Show results summary if there are items --}}
                    @if ($tickets->total() > 0)
                        <p class="text-sm text-gray-500">
                            Showing
                            <span class="font-medium">{{ $tickets->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $tickets->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $tickets->total() }}</span>
                            results
                        </p>
                    @endif
                    {{-- Add Per Page Dropdown Here if needed --}}
                </div>

                {{-- Right side: Pagination Links --}}
                <div>
                    {{-- Only show pagination if there are multiple pages --}}
                    @if ($tickets->hasPages())
                        {{ $tickets->links() }} {{-- Use default pagination view --}}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 