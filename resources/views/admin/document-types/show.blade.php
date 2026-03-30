@extends('layouts.app')

@section('title', 'Document Type')

@section('content')
<div class="mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="flex justify-between items-center mb-6">
                <h2
                    class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                    View Document Type
                </h2>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.document-types.index') }}"
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
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 border-b border-gray-100 flex items-center">
                    <span
                        class="bg-gradient-to-r from-blue-600 to-blue-800 text-white text-lg font-semibold px-4 py-2 rounded-lg shadow-sm">
                        #{{ $documentType->id }}
                    </span>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500">Created: {{ $documentType->created_at->format('d/m/Y H:i:s') }}
                        </p>
                        <p class="text-xs text-gray-500">Last Updated:
                            {{ $documentType->updated_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>

                <div class="bg-white p-6">
                    <div class="mb-6">
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700">Document Type Name</h3>
                        </div>
                        <div class="ml-7">
                            <p class="text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                {{ $documentType->name }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700">Description</h3>
                        </div>
                        <div class="ml-7">
                            <div
                                class="bg-gray-50 p-4 rounded-lg border border-gray-100 whitespace-pre-wrap text-gray-800">
                                {{ $documentType->description }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700">Mandatory Status</h3>
                        </div>
                        <div class="ml-7">
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $documentType->is_mandatory ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $documentType->is_mandatory ? 'Mandatory' : 'Optional' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <form action="{{ route('admin.document-types.destroy', $documentType) }}" method="POST"
                    class="inline delete-document-type-form">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete('{{ $documentType->name }} document type', this.form)"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-all duration-200 shadow-md hover:shadow-lg mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Document Type
                    </button>
                </form>
                <a href="{{ route('admin.document-types.edit', $documentType) }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Document Type
                </a>
            </div>
        </div>
    </div>
</div>
@endsection