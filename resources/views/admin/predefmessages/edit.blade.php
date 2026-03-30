@extends('layouts.app')

@section('title', 'Predefined Message')

@section('content')
<div class="mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="flex justify-between items-center mb-6">
                <h2
                    class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                    Edit Predefined Message #{{ $preDefinedMessage->id }}
                </h2>
                <a href="{{ route('admin.predefined-messages.index') }}"
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

            <form
                action="{{ route('admin.predefined-messages.update', ['predefined_message' => $preDefinedMessage->id]) }}"
                method="POST" novalidate>
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    {{-- Application Status --}}
                    <div>
                        <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">Application Status
                            <span class="text-red-500">*</span></label>
                        <select id="status_id" name="status_id" required
                            class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('status_id') border-red-500 @enderror">
                            <option value="">Select Application Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}"
                                    {{ old('status_id', $preDefinedMessage->status_id) == $status->id ? 'selected' : '' }}>
                                    {{ $status->status_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('status_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Message Name --}}
                    <div>
                        <label for="message_name" class="block text-sm font-medium text-gray-700 mb-1">Message Name
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="message_name" id="message_name" required
                            value="{{ old('message_name', $preDefinedMessage->message_name) }}"
                            class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('message_name') border-red-500 @enderror"
                            placeholder="Enter a unique name for the message">
                        @error('message_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Message Remarks --}}
                    <div>
                        <label for="message_remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks <span
                                class="text-red-500">*</span></label>
                        <textarea name="message_remarks" id="message_remarks" rows="6" required
                            class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('message_remarks') border-red-500 @enderror"
                            placeholder="Enter the content of the predefined message">{{ old('message_remarks', $preDefinedMessage->message_remarks) }}</textarea>
                        @error('message_remarks')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            Update Message
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection