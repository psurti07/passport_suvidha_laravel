@extends('layouts.app')

@section('title', 'Final Detail')

@section('content')
<div class="mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="flex justify-between items-center mb-6">
                <h2
                    class="text-xl sm:text-2xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                    View Final Detail
                </h2>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.final-details.edit', $finalDetail) }}"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('admin.final-details.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>

            <div class="mt-8 border border-gray-100 rounded-xl overflow-hidden">
                <!-- Header / ID Section -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 border-b border-gray-100 flex items-center">
                    <span
                        class="bg-gradient-to-r from-blue-600 to-blue-800 text-white text-lg font-semibold px-4 py-2 rounded-lg shadow-sm">
                        #{{ $finalDetail->id }}
                    </span>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500">Uploaded:
                            {{ $finalDetail->upload_date ? $finalDetail->upload_date->format('d M Y, h:i A') : 'N/A' }}
                        </p>
                        <p class="text-xs text-gray-500">Last Updated:
                            {{ $finalDetail->updated_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>

                <!-- Final Detail Information -->
                <div class="bg-white p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer Information Section -->
                    <div>
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700">Customer Information</h3>
                        </div>
                        <div class="ml-7 space-y-4">
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Name</span>
                                <p class="text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-100 mt-1">
                                    {{ $finalDetail->customer->full_name ?? 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Mobile</span>
                                <p class="text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-100 mt-1">
                                    {{ $finalDetail->customer->mobile_number ?? 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Email</span>
                                <p class="text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-100 mt-1">
                                    {{ $finalDetail->customer->email ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Information Section -->
                    <div>
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700">Approval Status</h3>
                        </div>
                        <div class="ml-7 space-y-4">
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Status</span>
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 mt-1">
                                    @if($finalDetail->is_approved)
                                    <span
                                        class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                    @else
                                    <span
                                        class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Approved Date</span>
                                <p class="text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-100 mt-1">
                                    {{ $finalDetail->approved_date ? $finalDetail->approved_date->format('d M Y, h:i A') : 'Not yet approved' }}
                                </p>
                            </div>
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Approved By</span>
                                <p class="text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-100 mt-1">
                                    {{ $finalDetail->approverName }}
                                </p>
                            </div>
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Uploaded By</span>
                                <p class="text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-100 mt-1">
                                    {{ $finalDetail->uploader->name ?? 'Unknown' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Section -->
                <div class="bg-white p-6 border-t border-gray-100">
                    <div class="flex items-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-700">Document</h3>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-100 flex justify-center items-center">
                        <div class="flex space-x-4">
                            <a href="{{ Storage::url($finalDetail->file_path) }}" target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Open Document
                            </a>
                            <a href="{{ Storage::url($finalDetail->file_path) }}" download
                                class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval/Unapproval Buttons -->
            <div class="mt-6 flex justify-end">
                @if(!$finalDetail->is_approved)
                <form action="{{ route('admin.final-details.approve', $finalDetail) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg text-sm font-medium hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Approve This Document
                    </button>
                </form>
                @else
                <form action="{{ route('admin.final-details.unapprove', $finalDetail) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg text-sm font-medium hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Remove Approval
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection