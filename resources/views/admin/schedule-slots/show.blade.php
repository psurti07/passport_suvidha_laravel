@extends('layouts.app')

@section('title', 'Schedule Slots')

@section('content')
<div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1
            class="text-xl sm:text-2xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
            SCHEDULE SLOT DETAILS
        </h1>
        <div class="flex items-center space-x-3 flex-shrink-0">
            <span @class([ 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium'
                , 'bg-blue-100 text-blue-800'=> $scheduleSlot->status === 1,
                'bg-green-100 text-green-800' => $scheduleSlot->status === 2,
                'bg-red-100 text-red-800' => $scheduleSlot->status === 3,
                'bg-yellow-100 text-yellow-800' => $scheduleSlot->status === 4,
                ])>
                Status:
                {{ $scheduleSlot->status === 1 ? 'Scheduled' : ($scheduleSlot->status === 2 ? 'Completed' : ($scheduleSlot->status === 3 ? 'Cancelled' : ($scheduleSlot->status === 4 ? 'Not Reachable' : 'Unknown'))) }}
            </span>

            <a href="{{ url()->previous() }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back
            </a>
        </div>
    </div>

    {{-- Display Validation Errors (Optional but Recommended) --}}
    @if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Oops! Something went wrong.</strong>
        <ul class="mt-2 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:items-start">
        <div class="lg:col-span-1 bg-white rounded-xl shadow-lg border border-gray-100 p-6 space-y-6">
            <div>
                <h2 class="text-lg font-semibold text-blue-700 border-b border-gray-200 pb-3 mb-4">Schedule Information
                </h2>
                <dl class="space-y-4">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Customer</dt>
                        <dd class="text-sm text-gray-900 font-semibold">
                            {{ $scheduleSlot->customer->first_name ?? 'N/A' }}
                            {{ $scheduleSlot->customer->last_name ?? 'N/A' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Mobile</dt>
                        <dd class="text-sm text-gray-900 font-semibold">
                            {{ $scheduleSlot->customer->mobile_number ?? 'N/A' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Service</dt>
                        <dd class="text-sm text-gray-900 font-semibold">
                            {{ $scheduleSlot->service->service_name ?? 'N/A' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                        <dd class="text-sm text-gray-900">
                            {{ $scheduleSlot->date->format('d M Y') . ' ' . $scheduleSlot->time->format('h:i A') }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Language</dt>
                        <dd class="text-sm text-gray-900 font-semibold">
                            {{ $scheduleSlot->language === 1 ? 'Hindi' : ($scheduleSlot->language === 2 ? 'English' : ($scheduleSlot->language === 3 ? 'Gujarati' : 'Unknown')) }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-blue-700 border-b border-gray-200 pb-3 mb-4">Update Status</h2>
                <form action="{{ route('admin.schedule-slots.update-status', $scheduleSlot->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center gap-3">
                        <label for="status" class="sr-only">Status</label>
                        <select id="status" name="status"
                            class="block w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                            <option value="1" {{ $scheduleSlot->status == 1 ? 'selected' : '' }}>Schedule</option>
                            <option value="2" {{ $scheduleSlot->status == 2 ? 'selected' : '' }}>Completed</option>
                            <option value="3" {{ $scheduleSlot->status == 3 ? 'selected' : '' }}>Cancelled</option>
                            <option value="4" {{ $scheduleSlot->status == 4 ? 'selected' : '' }}>Not Reachable</option>
                        </select>
                        <button type="submit"
                            onclick="this.disabled=true; this.innerText='Updating...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                            class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 whitespace-nowrap">
                            Update
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-100 p-6 space-y-6">
            <div>
                <h3 class="text-md font-medium text-gray-700 mb-3">Schedule Remarks</h3>

                <div class="mb-4 space-y-3 max-h-72 overflow-y-auto pr-2 border rounded-md p-3 bg-gray-50">
                    @if($scheduleSlot->remarks)
                    <div class="bg-blue-50 p-3 rounded-md border border-blue-100 shadow-sm">
                        <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $scheduleSlot->remarks }}</p>
                        <p class="text-xs text-gray-500 mt-1.5">
                            Last updated:
                            <span class="font-medium">
                                {{ $scheduleSlot->updated_at->format('d M Y, h:i A') }}
                            </span>
                        </p>
                    </div>
                    @else
                    <p class="text-sm text-gray-500 italic text-center py-4">
                        No remarks added yet.
                    </p>
                    @endif
                </div>

                <form action="{{ route('admin.schedule-slots.remark.update', $scheduleSlot->id) }}" method="POST"
                    class="mt-4">
                    @csrf
                    <div>
                        <textarea id="remark" name="remark" rows="3" required minlength="5"
                            class="block w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-150 ease-in-out @error('remark') border-red-500 ring-red-500 @enderror"
                            placeholder="Enter remark here...">{{ old('remark') }}</textarea>
                        @error('remark')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-3 flex justify-end">
                        <button type="submit"
                            onclick="this.disabled=true; this.innerText='Updating...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                            class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            Update Remark
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection