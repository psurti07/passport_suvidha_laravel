@extends('layouts.app')

@section('title', 'Create Predefined Message')

@section('content')
<div class="mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                    Create New Predefined Message
                </h2>
                <a href="{{ route('admin.predefined-messages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

            <form action="{{ route('admin.predefined-messages.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    {{-- Message Name --}}
                    <div>
                        <label for="message_name" class="block text-sm font-medium text-gray-700 mb-1">Message Name <span class="text-red-500">*</span></label>
                        <input type="text" name="message_name" id="message_name" required value="{{ old('message_name') }}"
                               class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('message_name') border-red-500 @enderror"
                               placeholder="Enter a unique name for the message">
                        @error('message_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Message Remarks --}}
                    <div>
                        <label for="message_remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks <span class="text-red-500">*</span></label>
                        <textarea name="message_remarks" id="message_remarks" rows="6" required
                                  class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('message_remarks') border-red-500 @enderror"
                                  placeholder="Enter the content of the predefined message">{{ old('message_remarks') }}</textarea>
                        @error('message_remarks')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end pt-4">
                        <button type="submit"
                                class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            Create Message
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Toastify CSS and JS --}}
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
    // Toast notification function
    function showToast(message, type = 'success') {
        let bgColor = type === 'success' ? 'linear-gradient(to right, #00b09b, #96c93d)' : 
                      type === 'error' ? 'linear-gradient(to right, #ff5f6d, #ffc371)' : 
                      'linear-gradient(to right, #00b09b, #96c93d)';
        
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: bgColor,
            stopOnFocus: true,
        }).showToast();
    }

    // Show session messages
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    });
</script>
@endsection 