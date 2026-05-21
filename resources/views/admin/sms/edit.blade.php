@extends('layouts.app')

@section('title', 'SMS Messages')

@section('content')
<div class="mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="flex justify-between items-center mb-6">
                <h2
                    class="text-xl sm:text-2xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                    Edit SMS Message
                </h2>
                <a href="{{ route('admin.sms.index') }}"
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

            <form action="{{ route('admin.sms.update', $sms) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    {{-- SMS Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            SMS Type
                        </label>

                        <input type="text" value="{{ $smsType }}" disabled
                            class="w-full border border-gray-200 bg-gray-100 rounded-lg text-sm px-4 py-2 text-gray-600 cursor-not-allowed">
                    </div>

                    {{-- SMS Message --}}
                    <div>
                        <label for="option_value" class="block text-sm font-medium text-gray-700 mb-1">SMS Message
                            <span class="text-red-500">*</span></label>
                        <textarea id="option_value" name="option_value" rows="4" placeholder="Enter sms message here..."
                            class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('option_value') border-red-500 @enderror">{{ old('option_value', $sms->option_value) }}</textarea>
                        @error('option_value')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            onclick="this.disabled=true; this.innerText='Updating...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                            class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            Update SMS Message
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection