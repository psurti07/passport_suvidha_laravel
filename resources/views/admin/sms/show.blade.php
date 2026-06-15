@extends('layouts.app')

@section('title', 'SMS Message: ' . $sms->name)

@section('content')
<div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1
            class="text-xl sm:text-2xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
            SEND TEST SMS
        </h1>
        <div class="flex items-center space-x-3 flex-shrink-0">
            <a href="{{ route('admin.sms.index') }}"
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
                <form action="{{ route('admin.sms.sendTest') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="slug" value="{{ $sms->slug }}">
                    <div class="space-y-6">
                        <div>
                            <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-1">Mobile
                                Number
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="mobile_number" id="mobile_number" required
                                value="{{ old('mobile_number') }}" maxlength="10" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('mobile_number') border-red-500 @enderror"
                                placeholder="Enter mobile number">
                            @error('mobile_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                onclick="this.disabled=true; this.innerText='Sending...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                                class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                Send
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>
@endsection