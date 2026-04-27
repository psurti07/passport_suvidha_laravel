@extends('layouts.app')

@section('title', 'Edit Appointment Letter')

@section('content')
<div class="mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="flex justify-between items-center mb-6">
                <h2
                    class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                    Edit Appointment Letter
                </h2>
                <a href="{{ route('admin.appointment-letters.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to List
                </a>
            </div>

            {{-- Remove old success/error message divs if they exist --}}
            {{-- @if(session('success'))
                    <div class="hidden" id="success-message" data-message="{{ session('success') }}">
        </div>
        @endif

        @if(session('error'))
        <div class="hidden" id="error-message" data-message="{{ session('error') }}"></div>
        @endif --}}

        {{-- Display Validation Errors (Copied from final-details) --}}
        @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <strong class="font-bold">Whoops! Something went wrong.</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-gray-50 rounded-lg p-6">
            <form action="{{ route('admin.appointment-letters.update', $appointmentLetter->id) }}" method="POST"
                enctype="multipart/form-data" id="appointment-letter-form" novalidate>
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-5">
                            <label for="customer_id"
                                class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                            <select id="customer_id" name="customer_id" required
                                class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('customer_id') border-red-500 @enderror">
                                <option value="">Select a customer</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ old('customer_id', $appointmentLetter->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->email }})
                                </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label for="upload_date" class="block text-sm font-medium text-gray-700 mb-1">Upload
                                Date</label>
                            <input type="date" id="upload_date" name="upload_date" required
                                value="{{ old('upload_date', $appointmentLetter->upload_date->format('Y-m-d')) }}"
                                class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('upload_date') border-red-500 @enderror">
                            @error('upload_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label for="appointment_date"
                                class="block text-sm font-medium text-gray-700 mb-1">Appointment Date</label>
                            <input type="date" id="appointment_date" name="appointment_date" required
                                value="{{ old('appointment_date', $appointmentLetter->appointment_date ? $appointmentLetter->appointment_date->format('Y-m-d') : '') }}"
                                class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('appointment_date') border-red-500 @enderror">
                            @error('appointment_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label for="appointment_time"
                                class="block text-sm font-medium text-gray-700 mb-1">Appointment Time</label>
                            <input type="time" id="appointment_time" name="appointment_time" required
                                value="{{ old('appointment_time', $appointmentLetter->appointment_time) }}"
                                class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('appointment_time') border-red-500 @enderror">
                            @error('appointment_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label for="appointment_letter"
                                class="block text-sm font-medium text-gray-700 mb-1">Document File (Leave empty to keep
                                current file)</label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="appointment_letter"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="appointment_letter" name="appointment_letter" type="file"
                                                class="sr-only" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PDF, DOC, DOCX, JPG, JPEG, or PNG up to 10MB
                                    </p>
                                    <p class="text-sm text-gray-600 mt-2" id="current-file">Current file:
                                        {{ basename($appointmentLetter->file_path) }}</p>
                                    <p class="text-sm text-gray-600 mt-2 hidden" id="file-name"></p>
                                </div>
                            </div>
                            @error('appointment_letter')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <div class="mb-5">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description
                                (Optional)</label>
                            <textarea id="description" name="description" rows="8"
                                class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('description') border-red-500 @enderror">{{ old('description', $appointmentLetter->description) }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <div class="flex items-center">
                                <input id="notify_customer" name="notify_customer" type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                <label for="notify_customer" class="ml-2 text-sm font-medium text-gray-700">Notify
                                    Customer</label>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Send an email notification to the customer about this
                                update.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-200 mt-6">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium shadow-sm hover:from-blue-700 hover:to-blue-900 transition-all duration-200 transform hover:-translate-y-0.5">
                        Update Appointment Letter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<script>
// Show session messages
document.addEventListener('DOMContentLoaded', function() {
    // Handle file input change (keep existing logic)
    const fileInput = document.getElementById('appointment_letter');
    const fileNameDisplay = document.getElementById('file-name');
    const currentFileDisplay = document.getElementById('current-file');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileNameDisplay.textContent = `Selected file: ${this.files[0].name}`;
                fileNameDisplay.classList.remove('hidden');
                if (currentFileDisplay) currentFileDisplay.classList.add('hidden');
            } else {
                fileNameDisplay.classList.add('hidden');
                if (currentFileDisplay) currentFileDisplay.classList.remove('hidden');
            }
        });
    }

    // Handle drag and drop (keep existing logic)
    const dropZone = document.querySelector('.border-dashed');
    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropZone.classList.add('border-blue-300', 'bg-blue-50');
        }

        function unhighlight() {
            dropZone.classList.remove('border-blue-300', 'bg-blue-50');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files && files.length && fileInput) {
                fileInput.files = files;
                fileNameDisplay.textContent = `Selected file: ${files[0].name}`;
                fileNameDisplay.classList.remove('hidden');
                if (currentFileDisplay) currentFileDisplay.classList.add('hidden');
            }
        }
    }

    // Remove old success/error message checks if they existed in DOMContentLoaded
    // const successMessage = document.getElementById('success-message');
    // ... old toastify logic ...
    // const errorMessage = document.getElementById('error-message');
    // ... old toastify logic ...
});
</script>
@endsection