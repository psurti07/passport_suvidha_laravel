@extends('layouts.app')

@section('title', 'Final Detail')

@section('content')
<div class="mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="flex justify-between items-center mb-6">
                <h2
                    class="text-xl sm:text-2xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                    Edit Final Detail
                </h2>
                <a href="{{ route('admin.final-details.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to List
                </a>
            </div>

            {{-- Display Validation Errors --}}
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

            <form action="{{ route('admin.final-details.update', $finalDetail) }}" method="POST"
                enctype="multipart/form-data" class="space-y-6" novalidate>
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    {{-- Customer --}}
                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer <span
                                class="text-red-500">*</span></label>
                        <select id="customer_id" name="customer_id" required
                            class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('customer_id') border-red-500 @enderror">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}"
                                {{ (old('customer_id', $finalDetail->customer_id) == $customer->id) ? 'selected' : '' }}>
                                {{ $customer->full_name }} ({{ $customer->mobile_number }})
                            </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Document File --}}
                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Document (PDF, JPG, JPEG,
                            PNG)</label>

                        @if($finalDetail->file_path)
                        <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-100">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-sm text-gray-700">Current file:</span>
                                <a href="{{ Storage::url($finalDetail->file_path) }}" target="_blank"
                                    class="text-blue-600 hover:underline ml-2">
                                    <span class="text-sm">{{ basename($finalDetail->file_path) }}</span>
                                </a>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Uploaded on:
                                {{ $finalDetail->upload_date ? $finalDetail->upload_date->format('d M Y, h:i A') : 'N/A' }}
                            </p>
                        </div>
                        @endif

                        <div class="mt-2">
                            <input type="file" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png"
                                class="block w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('file') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Maximum file size: 10MB. Leave empty to keep current
                                file.</p>
                        </div>

                        @error('file')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Is Approved --}}
                    <div>
                        <div class="flex items-center">
                            <input id="is_approved" name="is_approved" type="checkbox" value="1"
                                {{ $finalDetail->is_approved ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_approved" class="ml-2 block text-sm font-medium text-gray-700">
                                Approved
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Approving will automatically set the approval date and
                            approver to the current user.</p>
                        @if($finalDetail->is_approved)
                        <div class="mt-2 text-xs text-gray-600">
                            <p>Currently approved by: {{ $finalDetail->approverName }}</p>
                            <p>Approved date:
                                {{ $finalDetail->approved_date ? $finalDetail->approved_date->format('d M Y, h:i A') : 'N/A' }}
                            </p>
                        </div>
                        @endif
                    </div>

                    {{-- Uploader Info --}}
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <h3 class="text-sm font-medium text-gray-700 mb-1">Upload Information</h3>
                        <p class="text-xs text-gray-600">
                            Uploaded by: {{ $finalDetail->uploader->name ?? 'Unknown' }}<br>
                            Upload date:
                            {{ $finalDetail->upload_date ? $finalDetail->upload_date->format('d M Y, h:i A') : 'N/A' }}
                        </p>
                        <p class="mt-2 text-xs text-gray-500">Note: Uploading a new file will update this information to
                            the current user and time.</p>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            Update Final Detail
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection