@extends('layouts.app')

@section('title', 'Customer Details - ' . ($customer->full_name ?? 'N/A'))

@php
    // REMOVE PHP Helper function - Replaced by Alpine
    // function isActiveTab($tabName, $currentTab) { ... }
    // $currentTab = request()->query('tab', 'info');
@endphp

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">CUSTOMER DETAILS</h1>
            <p class="text-xl text-gray-700 font-medium mt-1">{{ strtoupper($customer->full_name ?? 'N/A') }} -
                {{ $customer->mobile_number ?? 'N/A' }}
            </p>
        </div>
        <div>
            {{-- Use previous_url from query if available, otherwise fallback --}}
            <a href="{{ request()->query('previous_url', route('admin.customers.index')) }}"
                class="btn-secondary inline-flex items-center border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>
    </div>

    {{-- Wrap layout in Alpine component --}}
    <div class="flex flex-col lg:flex-row gap-6" x-data="{
        activeTab: window.location.hash ? window.location.hash.substring(1) : 'info',
        setActiveTab(tab) {
            this.activeTab = tab;
            window.location.hash = tab;
        }
    }" x-init="() => {
        // Listen for hash changes in the URL
        window.addEventListener('hashchange', () => {
            activeTab = window.location.hash ? window.location.hash.substring(1) : 'info';
        });
    
        // Set initial hash if not already present
        if (!window.location.hash) {
            window.location.hash = activeTab;
        }
    }">
        {{-- Sidebar Navigation --}}
        <div class="w-full lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <nav class="divide-y divide-gray-200">
                    {{-- Update links for Alpine --}}
                    <a href="#" @click.prevent="setActiveTab('info')"
                        :class="{ 'bg-blue-600 text-white': activeTab === 'info', 'text-gray-700 hover:bg-blue-50 hover:text-blue-700 focus:bg-blue-100 focus:text-blue-800': activeTab !== 'info' }"
                        class="flex items-center px-6 py-4 text-md font-bold transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Customer Info
                    </a>
                    <a href="#" @click.prevent="setActiveTab('documents')"
                        :class="{ 'bg-blue-600 text-white': activeTab === 'documents', 'text-gray-700 hover:bg-blue-50 hover:text-blue-700 focus:bg-blue-100 focus:text-blue-800': activeTab !== 'documents' }"
                        class="flex items-center px-6 py-4 text-md font-bold transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Uploaded Documents
                    </a>
                    <a href="#" @click.prevent="setActiveTab('application-process')"
                        :class="{ 'bg-blue-600 text-white': activeTab === 'application-process', 'text-gray-700 hover:bg-blue-50 hover:text-blue-700 focus:bg-blue-100 focus:text-blue-800': activeTab !== 'application-process' }"
                        class="flex items-center px-6 py-4 text-md font-bold transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Application Process
                    </a>
                    <a href="#" @click.prevent="setActiveTab('passport-credential')"
                        :class="{ 'bg-blue-600 text-white': activeTab === 'passport-credential', 'text-gray-700 hover:bg-blue-50 hover:text-blue-700 focus:bg-blue-100 focus:text-blue-800': activeTab !== 'passport-credential' }"
                        class="flex items-center px-6 py-4 text-md font-bold transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="4" y="10" width="11" height="10" rx="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 10V7a4 4 0 018 0v3" />
                            <circle cx="17" cy="15" r="2.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.8 16.8L22 20m-1.5-1.5l1.2-1.2" />
                        </svg>
                        Passport Credential
                    </a>
                    <a href="#" @click.prevent="setActiveTab('actions')"
                        :class="{ 'bg-blue-600 text-white': activeTab === 'actions', 'text-gray-700 hover:bg-blue-50 hover:text-blue-700 focus:bg-blue-100 focus:text-blue-800': activeTab !== 'actions' }"
                        class="flex items-center px-6 py-4 text-md font-bold transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Actions
                    </a>
                </nav>
            </div>
        </div>

        {{-- Main Content Area --}}
        <div class="w-full lg:w-3/4">

            {{-- Customer Info Tab Content --}}
            <div x-show="activeTab === 'info'" x-cloak>
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">

                    {{-- Top Info Row --}}
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b border-gray-200 gap-y-2">

                        <p class="text-sm text-gray-600">
                            {{ $customer->is_paid == 1 ? 'Registration on:' : 'Created on:' }}

                            <strong class="text-gray-800 font-semibold">
                                @if ($customer->is_paid == 1 && $customer->payment_date)
                                    {{ $customer->payment_date->format('d M Y, h:i A') }}
                                @elseif ($customer->created_at)
                                    {{ $customer->created_at->format('d M Y, h:i A') }}
                                @else
                                    N/A
                                @endif
                            </strong>
                        </p>

                        @if ($invoice)
                            <a href="{{ route('admin.invoices.download', $invoice->id) }}"
                                class="inline-flex items-center text-sm px-4 py-2 w-auto bg-gradient-to-r from-green-500 to-green-600 font-medium rounded-lg border border-white text-white hover:from-green-600 hover:to-green-700">
                                Download Invoice
                            </a>
                        @else
                            <span class="text-gray-400 text-sm">No Invoice Found</span>
                        @endif
                    </div>

                    {{-- Form Fields --}}
                    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" class="space-y-5"
                        novalidate>
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="full_name" name="full_name"
                                    value="{{ old('full_name', $customer->full_name) }}" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('full_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('full_name')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-1">Mobile
                                    Number
                                    <span class="text-red-500">*</span></label>
                                <input type="tel" id="mobile_number" name="mobile_number"
                                    value="{{ old('mobile_number', $customer->mobile_number) }}" required maxlength="10"
                                    inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('mobile_number') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('mobile_number')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Id <span
                                        class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email"
                                    value="{{ old('email', $customer->email) }}" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('email') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="father_name" class="block text-sm font-medium text-gray-700 mb-1">Father Name
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="father_name" name="father_name"
                                    value="{{ old('father_name', $customer->father_name) }}" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('father_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('father_name')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="mother_name" class="block text-sm font-medium text-gray-700 mb-1">Mother Name
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="mother_name" name="mother_name"
                                    value="{{ old('mother_name', $customer->mother_name) }}" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('mother_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('mother_name')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="marital_status" class="block text-sm font-medium text-gray-700 mb-1">
                                    Marital Status <span class="text-red-500">*</span>
                                </label>

                                <select id="marital_status" name="marital_status" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm @error('marital_status') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">

                                    <option value="">Select Marital Status</option>

                                    <option value="single"
                                        {{ old('marital_status', $customer->marital_status) == 'single' ? 'selected' : '' }}>
                                        Single
                                    </option>

                                    <option value="married"
                                        {{ old('marital_status', $customer->marital_status) == 'married' ? 'selected' : '' }}>
                                        Married
                                    </option>

                                    <option value="widow"
                                        {{ old('marital_status', $customer->marital_status) == 'widow' ? 'selected' : '' }}>
                                        Widow
                                    </option>

                                    <option value="widower"
                                        {{ old('marital_status', $customer->marital_status) == 'widower' ? 'selected' : '' }}>
                                        Widower
                                    </option>

                                    <option value="separated"
                                        {{ old('marital_status', $customer->marital_status) == 'separated' ? 'selected' : '' }}>
                                        Separated
                                    </option>

                                    <option value="divorced"
                                        {{ old('marital_status', $customer->marital_status) == 'divorced' ? 'selected' : '' }}>
                                        Divorced
                                    </option>
                                </select>

                                @error('marital_status')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div id="spouse_name_div">
                                <label for="spouse_name" class="block text-sm font-medium text-gray-700 mb-1">Spouse Name
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="spouse_name" name="spouse_name"
                                    value="{{ old('spouse_name', $customer->spouse_name) }}" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('spouse_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('spouse_name')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="emergency_contact_name"
                                    class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                                    value="{{ old('emergency_contact_name', $customer->emergency_contact_name) }}"
                                    required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('emergency_contact_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('emergency_contact_name')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="emergency_contact_mobile"
                                    class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Mobile
                                    <span class="text-red-500">*</span></label>
                                <input type="tel" id="emergency_contact_mobile" name="emergency_contact_mobile"
                                    value="{{ old('emergency_contact_mobile', $customer->emergency_contact_mobile) }}"
                                    required maxlength="10" inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('emergency_contact_mobile') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('emergency_contact_mobile')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="emergency_contact_email"
                                    class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Email <span
                                        class="text-red-500">*</span></label>
                                <input type="email" id="emergency_contact_email" name="emergency_contact_email"
                                    value="{{ old('emergency_contact_email', $customer->emergency_contact_email) }}"
                                    required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('emergency_contact_email') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('emergency_contact_email')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                        class="text-red-500">*</span></label>
                                <textarea id="address" name="address" rows="2" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('address') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('address', $customer->address ?? '') }}
                            </textarea>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="pin_code" class="block text-sm font-medium text-gray-700 mb-1">Pincode <span
                                        class="text-red-500">*</span></label>
                                <input type="text" id="pin_code" name="pin_code"
                                    value="{{ old('pin_code', $customer->pin_code) }}" required maxlength="6"
                                    minlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('pin_code') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('pin_code')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <span id="pincode-error" class="text-red-500 text-sm"></span>
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City <span
                                        class="text-red-500">*</span></label>
                                <input type="text" id="city" name="city"
                                    value="{{ old('city', $customer->city) }}" required readonly
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('city') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State <span
                                        class="text-red-500">*</span></label>
                                <input type="text" id="state" name="state"
                                    value="{{ old('state', $customer->state) }}" required readonly
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('state') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('state')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Is Permanent Address Same as Current Address?
                                    <span class="text-red-500">*</span>
                                </label>

                                <div class="flex items-center gap-6">

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="is_address_permanent" value="1"
                                            id="is_address_permanent_yes"
                                            {{ old('is_address_permanent', $customer->is_address_permanent ?? '1') == '1' ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600">
                                        <span>Yes</span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="is_address_permanent" value="0"
                                            id="is_address_permanent_no"
                                            {{ old('is_address_permanent', $customer->is_address_permanent ?? '1') == '0' ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600">
                                        <span>No</span>
                                    </label>

                                </div>
                            </div>

                            <div id="permanent-address-fields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-5"
                                style="{{ old('is_address_permanent', $customer->is_address_permanent ?? '1') == '0' ? '' : 'display: none;' }}">

                                <div>
                                    <label for="permanent_address" class="block text-sm font-medium text-gray-700 mb-1">
                                        Permanent Address
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <textarea id="permanent_address" name="permanent_address" rows="2" placeholder="Enter permanent address"
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm @error('permanent_address') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('permanent_address', $customer->permanent_address ?? '') }}</textarea>

                                    @error('permanent_address')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="permanent_pin_code" class="block text-sm font-medium text-gray-700 mb-1">
                                        Permanent Pincode
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input type="text" id="permanent_pin_code" name="permanent_pin_code"
                                        value="{{ old('permanent_pin_code', $customer->permanent_pin_code ?? '') }}"
                                        maxlength="6" minlength="6" placeholder="Enter pincode"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm @error('permanent_pin_code') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">

                                    @error('permanent_pin_code')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror

                                    <span id="permanent-pincode-error" class="text-red-500 text-sm"></span>
                                </div>

                                <div>
                                    <label for="permanent_city" class="block text-sm font-medium text-gray-700 mb-1">
                                        Permanent City
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input type="text" id="permanent_city" name="permanent_city"
                                        value="{{ old('permanent_city', $customer->permanent_city ?? '') }}"
                                        placeholder="Enter city" readonly
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm @error('permanent_city') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">

                                    @error('permanent_city')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="permanent_state" class="block text-sm font-medium text-gray-700 mb-1">
                                        Permanent State
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input type="text" id="permanent_state" name="permanent_state"
                                        value="{{ old('permanent_state', $customer->permanent_state ?? '') }}"
                                        placeholder="Enter state" readonly
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm @error('permanent_state') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">

                                    @error('permanent_state')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                            </div>



                            <div>
                                <label for="police_station_name"
                                    class="block text-sm font-medium text-gray-700 mb-1">Nearest Police Station Name
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="police_station_name" name="police_station_name"
                                    value="{{ old('police_station_name', $customer->police_station_name) }}" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('police_station_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('police_station_name')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                                    Gender <span class="text-red-500">*</span>
                                </label>

                                <select id="gender" name="gender" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm @error('gender') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">

                                    <option value="">Select Gender</option>

                                    <option value="male"
                                        {{ old('gender', $customer->gender) == 'male' ? 'selected' : '' }}>
                                        Male
                                    </option>

                                    <option value="female"
                                        {{ old('gender', $customer->gender) == 'female' ? 'selected' : '' }}>
                                        Female
                                    </option>

                                    <option value="other"
                                        {{ old('gender', $customer->gender) == 'other' ? 'selected' : '' }}>
                                        Other
                                    </option>
                                </select>

                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of
                                    Birth <span class="text-red-500">*</span></label>
                                <input type="date" id="date_of_birth" name="date_of_birth"
                                    value="{{ old('date_of_birth', $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '') }}"
                                    required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('date_of_birth') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('date_of_birth')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="place_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Place of
                                    Birth <span class="text-red-500">*</span></label>
                                <input type="text" id="place_of_birth" name="place_of_birth"
                                    value="{{ old('place_of_birth', $customer->place_of_birth ?? '') }}" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('place_of_birth') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('place_of_birth')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="education_qualification" class="block text-sm font-medium text-gray-700 mb-1">
                                    Education Qualification <span class="text-red-500">*</span>
                                </label>

                                <select id="education_qualification" name="education_qualification" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm @error('education_qualification') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">

                                    <option value="">Select Education Qualification</option>

                                    <!-- <option value="Below 10th"
                                                                                                                                        {{ old('education_qualification', $customer->education_qualification) == 'Below 10th' ? 'selected' : '' }}>
                                                                                                                                        Below 10th
                                                                                                                                    </option> -->
                                    <option value="10th Pass And Above"
                                        {{ old('education_qualification', $customer->education_qualification) == '10th Pass And Above' ? 'selected' : '' }}>
                                        10th Pass And Above
                                    </option>

                                    <option value="7th Pass Or Less"
                                        {{ old('education_qualification', $customer->education_qualification) == '7th Pass Or Less' ? 'selected' : '' }}>
                                        7th Pass Or Less
                                    </option>

                                    <option value="Between 8th And 9th Standard"
                                        {{ old('education_qualification', $customer->education_qualification) == 'Between 8th And 9th Standard' ? 'selected' : '' }}>
                                        Between 8th And 9th Standard
                                    </option>

                                    <option value="Graduate And Above"
                                        {{ old('education_qualification', $customer->education_qualification) == 'Graduate And Above' ? 'selected' : '' }}>
                                        Graduate And Above
                                    </option>
                                </select>

                                @error('education_qualification')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Employment Type <span class="text-red-500">*</span>
                                </label>

                                <select id="employment_type" name="employment_type" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm @error('employment_type') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">

                                    <option value="">Select Employment Type</option>

                                    <option value="Government"
                                        {{ old('employment_type', $customer->employment_type) == 'Government' ? 'selected' : '' }}>
                                        Government
                                    </option>

                                    <option value="Private"
                                        {{ old('employment_type', $customer->employment_type) == 'Private' ? 'selected' : '' }}>
                                        Private
                                    </option>

                                    <option value="Self Employed"
                                        {{ old('employment_type', $customer->employment_type) == 'Self Employed' ? 'selected' : '' }}>
                                        Self Employed
                                    </option>

                                    <option value="Student"
                                        {{ old('employment_type', $customer->employment_type) == 'Student' ? 'selected' : '' }}>
                                        Student
                                    </option>

                                    <option value="Homemaker"
                                        {{ old('employment_type', $customer->employment_type) == 'Homemaker' ? 'selected' : '' }}>
                                        Homemaker
                                    </option>

                                    <option value="Retired"
                                        {{ old('employment_type', $customer->employment_type) == 'Retired' ? 'selected' : '' }}>
                                        Retired
                                    </option>

                                    <option value="Others"
                                        {{ old('employment_type', $customer->employment_type) == 'Others' ? 'selected' : '' }}>
                                        Others
                                    </option>
                                </select>

                                @error('employment_type')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Organisation Name --}}
                            <div id="organisation_name_div">

                                <label for="organisation_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Organisation Name
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="organisation_name" name="organisation_name"
                                    value="{{ old('organisation_name', $customer->organisation_name) }}"
                                    placeholder="Enter Organisation Name"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm">

                                @error('organisation_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="nationality" class="block text-sm font-medium text-gray-700 mb-1">Nationality
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="nationality" name="nationality"
                                    value="{{ old('nationality', $customer->nationality) }}" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                    @error('nationality') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('nationality')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>
                        {{-- Save Button --}}
                        <div class="pt-6 mt-4 border-t border-gray-200 flex justify-end">
                            <button type="submit"
                                onclick="this.disabled=true; this.innerText='Updating...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                                class="btn-primary px-8 py-2.5">SAVE</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Documents Tab Content --}}
            <div x-show="activeTab === 'documents'" x-cloak>
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 space-y-8">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-800">Uploaded Documents</h2>
                        @php
                            $documents = $customer->applicationDocuments;
                            $hasDocuments = $documents->count() > 0;
                            $allVerified = $hasDocuments && $documents->where('is_verified', 0)->count() === 0;
                        @endphp
                        @if ($hasDocuments)
                            <form action="{{ route('admin.documents.updateAll') }}" method="POST">
                                @csrf
                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                <input type="hidden" name="status" value="{{ $allVerified ? 0 : 1 }}">

                                @if ($allVerified)
                                    <button
                                        onclick="this.disabled=true; this.innerText='Unverifying...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                                        class="bg-red-100 text-red-700 px-4 py-2 rounded-md text-sm font-medium inline-flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Unverify All Documents
                                    </button>
                                @else
                                    <button
                                        onclick="this.disabled=true; this.innerText='Verifying...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                                        class="bg-green-100 text-green-700 px-4 py-2 rounded-md text-sm font-medium inline-flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 flex-shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Verify All Documents
                                    </button>
                                @endif
                            </form>
                        @endif
                    </div>
                    {{-- Section 1: Display Existing Uploaded Documents --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 mb-4">Current Documents</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse ($customer->applicationDocuments ?? [] as $document)
                                <div
                                    class="border border-gray-200 rounded-lg p-4 bg-gray-50 flex flex-col justify-between">
                                    <div class="flex items-center mb-3">
                                        @if (pathinfo($document->file_path, PATHINFO_EXTENSION) == 'pdf')
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-8 w-8 mr-3 text-gray-500 flex-shrink-0" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-8 w-8 mr-3 text-gray-500 flex-shrink-0" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm break-all">
                                                {{ $document->documentType->name }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">Uploaded:
                                                {{ $document->created_at->format('Y-m-d') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                                        <a href="{{ Storage::url($document->file_path) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            View
                                        </a>
                                        @if ($document->is_verified)
                                            <a href="{{ route('admin.documents.toggleVerify', ['id' => $document->id, 'redirect' => 'show']) }}"
                                                class="text-red-600 hover:text-red-800 text-sm font-medium inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Click to Unverify
                                            </a>
                                        @else
                                            <a href="{{ route('admin.documents.toggleVerify', ['id' => $document->id, 'redirect' => 'show']) }}"
                                                class="text-green-600 hover:text-green-800 text-sm font-medium inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 flex-shrink-0"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Click to Verify
                                            </a>
                                        @endif
                                        {{-- Staff Delete Button --}}
                                        <form id="delete-document-{{ $document->id }}"
                                            action="{{ route('admin.application-documents.destroy', $document->id) }}"
                                            method="POST" class="inline">
                                            <input type="hidden" name="redirect" value="show">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('{{ $document->documentType->name }} Document', this.form)"
                                                class="text-red-500 hover:text-red-700 text-sm font-medium inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm sm:col-span-2 lg:col-span-3">No documents have been
                                    uploaded yet.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Section 2: Display Remaining/Required Documents --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 mb-4">Required Documents Checklist</h3>
                        @php
                            $documentTypes = \App\Models\DocumentType::all();
                            $uploadedDocumentIds = $customer->applicationDocuments
                                ? $customer->applicationDocuments->pluck('document_type_id')->toArray()
                                : [];
                        @endphp

                        <ul class="space-y-2 text-sm">
                            @forelse($documentTypes as $docType)
                                @php
                                    $isUploaded = in_array($docType->id, $uploadedDocumentIds);

                                    if ($isUploaded) {
                                        $textColorClass = 'text-green-600';
                                        $iconPath = '
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                                        $statusText = 'Uploaded';
                                    } elseif ($docType->is_mandatory) {
                                        $textColorClass = 'text-red-600';
                                        $iconPath = '
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                                        $statusText = 'Pending';
                                    } else {
                                        $textColorClass = 'text-amber-600';
                                        $iconPath = '
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                                        $statusText = 'Optional';
                                    }
                                @endphp
                                <li class="flex items-center {{ $textColorClass }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        {!! $iconPath !!}
                                    </svg>
                                    <span class="{{ !$isUploaded && $docType->is_mandatory ? 'font-medium' : '' }}">
                                        {{ $docType->name }} ({{ $statusText }})
                                    </span>
                                </li>
                            @empty
                                <li class="text-gray-500">No document types defined in the system.</li>
                            @endforelse
                        </ul>
                    </div>

                    {{-- Section 3: Staff Upload Form --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 mb-4">Upload New Document (Staff)</h3>
                        <form action="{{ route('admin.application-documents.store') }}" method="POST"
                            enctype="multipart/form-data" novalidate
                            class="border border-gray-200 rounded-lg p-5 bg-gray-50 space-y-4">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                            <input type="hidden" name="redirect"
                                value="{{ route('admin.customers.show', $customer->id) }}#documents">

                            <div>
                                <label for="document_type_id"
                                    class="block text-sm font-medium text-gray-700 mb-1">Document
                                    Type <span class="text-red-500">*</span></label>
                                <select id="document_type_id" name="document_type_id" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm
                                    @error('document_type_id') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                    <option value="" disabled selected>Select document type</option>
                                    @foreach ($documentTypes as $docType)
                                        <option value="{{ $docType->id }}"
                                            {{ in_array($docType->id, $uploadedDocumentIds) ? 'disabled' : '' }}>
                                            {{ $docType->name }}
                                            {{ $docType->is_mandatory ? '(Required)' : '(Optional)' }}
                                            {{ in_array($docType->id, $uploadedDocumentIds) ? '- Already Uploaded' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('document_type_id')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="document_file" class="block text-sm font-medium text-gray-700 mb-1">Select
                                    File <span class="text-red-500">*</span></label>
                                <input type="file" id="document_file" name="document_file" required
                                    class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                    @error('document_file') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500">PDF, JPG, JPEG, PNG. Max size: 5MB.</p>
                                @error('document_file')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    onclick="this.disabled=true; this.innerText='Uploading...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                                    class="btn-primary inline-flex items-center px-5 py-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Upload Document
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Application Progress Tab Content --}}
            <div x-show="activeTab === 'application-process'" x-cloak x-data="applicationProcessComponent({{ $errors->any() ? 'true' : 'false' }}, '{{ old('status_id') }}', '{{ old('predefined_message') }}')" x-init="init()">

                <script>
                    function applicationProcessComponent(show = false, oldStatus = '', oldMessage = '') {
                        return {
                            predefinedMessages: @json($predefinedMessages),
                            statuses: @json($statuses),
                            showRemarkForm: show,
                            selectedMessage: oldMessage || '',
                            selectedStatus: oldStatus || '',
                            showFileUpload: false,
                            showAppointmentFields: false,
                            filteredMessages: [],
                            remarks: '{{ old('remark ') }}',
                            isInitialLoad: true,

                            init() {
                                if (this.selectedStatus) {
                                    this.updateMessages(false);
                                    this.updateFileUpload();

                                    this.$nextTick(() => {
                                        if (this.selectedMessage) {
                                            this.updateRemarks();
                                        }
                                    });
                                }
                                this.isInitialLoad = false;
                            },

                            getSelectedStatusSlug() {
                                const found = this.statuses.find(s => s.id == this.selectedStatus);
                                return found ? found.slug : null;
                            },

                            updateMessages(reset = true) {
                                if (!this.selectedStatus) {
                                    this.filteredMessages = [];

                                    if (reset) {
                                        this.selectedMessage = '';
                                        this.remarks = '';
                                    }
                                    return;
                                }

                                this.filteredMessages = this.predefinedMessages.filter(
                                    msg => msg.status_id == this.selectedStatus
                                );

                                const exists = this.filteredMessages.some(
                                    msg => msg.message_name === this.selectedMessage
                                );

                                if (!exists) {
                                    if (reset && !this.isInitialLoad) {
                                        this.selectedMessage = '';
                                        this.remarks = '';
                                    }
                                }
                            },

                            updateRemarks() {
                                if (!this.selectedMessage) {
                                    this.remarks = '';
                                    return;
                                }

                                const matched = this.filteredMessages.find(
                                    msg => msg.message_name === this.selectedMessage
                                );

                                if (matched) {
                                    this.remarks = matched.message_remarks;
                                }
                            },

                            updateFileUpload() {
                                const slug = this.getSelectedStatusSlug();

                                this.showFileUpload = [
                                    'details_verification',
                                    'appointment_scheduled',
                                    'appointment_rescheduled1',
                                    'appointment_rescheduled2',
                                    'appointment_rescheduled3'
                                ].includes(slug);

                                this.showAppointmentFields = [
                                    'appointment_scheduled',
                                    'appointment_rescheduled1',
                                    'appointment_rescheduled2',
                                    'appointment_rescheduled3'
                                ].includes(slug);
                            }
                        }
                    }
                </script>

                {{-- Alpine component for form toggle --}}
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Application Process</h2>
                        {{-- Send Remark Button --}}
                        <button type="button" @click="showRemarkForm = !showRemarkForm"
                            class="btn-primary inline-flex items-center px-4 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <span x-text="showRemarkForm ? 'Cancel' : 'Send Remark'">Send Remark</span>
                        </button>
                    </div>

                    {{-- Remark Form (Initially Hidden) --}}
                    <div x-show="showRemarkForm" x-cloak x-transition
                        class="border border-gray-200 rounded-lg p-5 mb-6 bg-gray-50">
                        <form action="{{ route('admin.application-progress.store') }}" method="POST"
                            enctype="multipart/form-data" class="space-y-5" novalidate>
                            @csrf
                            {{-- Add customer_id as needed --}}
                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                            <input type="hidden" name="redirect"
                                value="{{ route('admin.customers.show', $customer->id) }}#application-process">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                                <div>
                                    <label for="status_id"
                                        class="block text-sm font-medium text-gray-700 mb-1">Application
                                        Status <span class="text-red-500">*</span></label>
                                    <select id="status_id" name="status_id" required x-model="selectedStatus"
                                        @change="updateFileUpload(); updateMessages(true); updateRemarks()"
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm
                                        @error('application_status') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                        <option value="" disabled>Select Application Status</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->id }}">
                                                {{ $status->status_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status_id')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="status_date" class="block text-sm font-medium text-gray-700 mb-1">Status
                                        Date <span class="text-red-500">*</span></label>
                                    <input type="date" id="status_date" name="status_date" required
                                        value="{{ date('Y-m-d') }}"
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                        @error('status_date') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                    @error('status_date')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- File Upload Fields --}}
                                <div x-show="showFileUpload || showAppointmentFields" class="md:col-span-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload File <span
                                                x-show="showFileUpload" class="text-red-500">*</span></label>
                                        <input type="file" id="file" name="file" :required="showFileUpload"
                                            class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                            @error('file') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                        <p class="mt-1 text-xs text-gray-500">PDF, JPG, JPEG, PNG. Max size: 5MB.</p>
                                        @error('file')
                                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div class="mt-4" x-show="showAppointmentFields">
                                        <label for="appointment_date"
                                            class="block text-sm font-medium text-gray-700 mb-1">Appointment Date <span
                                                class="text-red-500">*</span></label>
                                        <input type="date" name="appointment_date" id="appointment_date"
                                            :required="showAppointmentFields" min="{{ date('Y-m-d') }}"
                                            class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                            @error('appointment_date') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                        @error('appointment_date')
                                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div class="mt-4" x-show="showAppointmentFields">
                                        <label for="appointment_time"
                                            class="block text-sm font-medium text-gray-700 mb-1">Appointment Time <span
                                                class="text-red-500">*</span></label>
                                        <input type="time" name="appointment_time" id="appointment_time"
                                            :required="showAppointmentFields"
                                            class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                            @error('appointment_time') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                        @error('appointment_time')
                                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="predefined_message"
                                        class="block text-sm font-medium text-gray-700 mb-1">Pre-defined Messages</label>
                                    <select name="predefined_message" id="predefined_message" x-model="selectedMessage"
                                        @change="updateRemarks()"
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm">
                                        <option value="" selected>Select Message (Optional)</option>
                                        <template x-for="msg in filteredMessages" :key="msg.id">
                                            <option :value="msg.message_name"
                                                :selected="msg.message_name === selectedMessage"
                                                x-text="msg.message_name">
                                            </option>
                                        </template>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="remark" class="block text-sm font-medium text-gray-700 mb-1">Remarks
                                        <span class="text-red-500">*</span></label>
                                    <textarea name="remark" id="remark" rows="4" required placeholder="Enter remarks..." x-model="remarks"
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                                        @error('remark') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror"></textarea>
                                    @error('remark')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Form Buttons --}}
                            <div class="pt-5 mt-4 border-t border-gray-200 flex justify-end gap-3">
                                <button type="button" @click="showRemarkForm = false; selectedMessage = ''; remarks = ''"
                                    class="btn-secondary px-6 py-2">CANCEL</button>
                                <button type="submit"
                                    onclick="this.disabled=true; this.innerText='Adding...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                                    class="btn-primary px-8 py-2">ADD</button>
                            </div>
                        </form>
                    </div>

                    {{-- Remark History Table --}}
                    <div class="w-full overflow-x-auto">
                        <table
                            class="w-auto min-w-full text-sm text-left text-gray-600 border border-gray-200 divide-y divide-gray-200 rounded-lg">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Date</th>
                                    <th scope="col" class="px-6 py-3">Remark</th>
                                    <th scope="col" class="px-6 py-3">File</th>
                                    <th scope="col" class="px-6 py-3">Staff Name</th>
                                    <th scope="col" class="px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($customer->applicationProgress()->orderBy('created_at', 'desc')->get() as $progress)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-6 py-4">
                                            @php
                                                $color = $progress->status->colorclass ?? 'gray';
                                            @endphp

                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                                                {{ str_replace('_', ' ', ucfirst($progress->status->status_name ?? 'N/A')) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $progress->status_date ? $progress->status_date->format('d M Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-normal">{{ $progress->remark }}</td>
                                        <td class="px-6 py-4">
                                            @if ($progress->relatedFile)
                                                <a href="{{ Storage::url($progress->relatedFile->file_path) }}"
                                                    target="_blank"
                                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                    View File
                                                </a>
                                            @else
                                                <span class="text-gray-500 text-sm">No file</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $progress->remarkedByUser ? $progress->remarkedByUser->name . ' (' . ucfirst($progress->remarkedByUser->role) . ')' : 'System' }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if (auth()->user()->role === 'admin' || auth()->user()->id === $progress->remarked_by)
                                                <form
                                                    action="{{ route('admin.application-progress.destroy', $progress->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="redirect"
                                                        value="{{ route('admin.customers.show', $customer->id) }}#application-process">
                                                    <button type="button"
                                                        onclick="confirmDelete('{{ str_replace('_', ' ', ucfirst($progress->status->status_name ?? 'N/A')) }} Remark', this.form)"
                                                        class="group inline-flex items-center justify-center h-9 w-9 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 hover:border-red-600 transition-all duration-200 shadow-sm"
                                                        title="Delete">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No remarks found
                                            for this customer.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Passport Credential Tab Content --}}
            <div x-show="activeTab === 'passport-credential'" x-cloak>
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">

                    {{-- Top Info Row --}}
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b border-gray-200 gap-y-2">

                        <div>
                            <h3 class="text-base font-semibold text-gray-800">
                                Passport Credentials
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Manage the customer's Passport Seva login credentials.
                            </p>
                        </div>

                        @if ($passportAccount && $passportAccount->is_email)
                            <span
                                class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Credentials Emailed
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                                </svg>
                                Not Emailed
                            </span>
                        @endif

                    </div>

                    {{-- Credentials Form --}}
                    <form action="{{ route('admin.customers.passport-account.store', $customer->id) }}" method="POST"
                        class="space-y-5" x-data="passportCredentialForm()">

                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                            {{-- Username --}}
                            <div>
                                <label for="passport_username" class="block text-sm font-medium text-gray-700 mb-1">
                                    Passport Username
                                    <span class="text-red-500">*</span>
                                </label>

                                <div class="relative">
                                    <input type="text" id="passport_username" name="username"
                                        value="{{ old('username', $passportAccount->username ?? '') }}" required
                                        autocomplete="off" placeholder="Enter Passport username"
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 pl-3 pr-12 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                            @error('username') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">

                                    {{-- Copy Username --}}
                                    <button type="button"
                                        @click="copyText(document.getElementById('passport_username').value, 'username')"
                                        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-blue-600 transition"
                                        title="Copy Username">
                                        {{-- Copy --}}
                                        <svg x-show="copiedField !== 'password'" class="w-5 h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8 8V5a2 2 0 012-2h9a2 2 0 012 2v9a2 2 0 01-2 2h-3" />

                                            <rect x="3" y="8" width="12" height="12" rx="2"
                                                ry="2" />
                                        </svg>

                                        {{-- Copied --}}
                                        <svg x-show="copiedField === 'password'" x-cloak class="w-5 h-5 text-teal-600"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </div>

                                @error('username')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Password --}}
                            <div>
                                <label for="passport_password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Passport Password
                                    <span class="text-red-500">*</span>
                                </label>

                                <div class="relative">

                                    <input :type="showPassword ? 'text' : 'password'" id="passport_password"
                                        name="password" value="{{ old('password', $passportPassword ?? '') }}" required
                                        autocomplete="new-password" placeholder="Enter Passport password"
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 pl-3 pr-20 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm
                            @error('password') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">

                                    <div class="absolute inset-y-0 right-0 flex items-center">

                                        {{-- Show / Hide Password --}}
                                        <button type="button" @click="showPassword = !showPassword"
                                            class="px-2 text-gray-400 hover:text-blue-600 transition"
                                            :title="showPassword ? 'Hide Password' : 'Show Password'">

                                            {{-- Eye --}}
                                            <svg x-show="!showPassword" class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>

                                            {{-- Eye Off --}}
                                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.223-3.592M6.228 6.228A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.96 9.96 0 01-4.132 5.168M6.228 6.228L3 3m3.228 3.228l11.544 11.544M9.88 9.88a3 3 0 104.24 4.24" />
                                            </svg>
                                        </button>

                                        {{-- Copy Password --}}
                                        <button type="button"
                                            @click="copyText(document.getElementById('passport_password').value, 'password')"
                                            class="px-2 pr-3 text-gray-400 hover:text-blue-600 transition"
                                            title="Copy Password">

                                            {{-- Copy --}}
                                            <svg x-show="copiedField !== 'password'" class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8 8V5a2 2 0 012-2h9a2 2 0 012 2v9a2 2 0 01-2 2h-3" />

                                                <rect x="3" y="8" width="12" height="12" rx="2"
                                                    ry="2" />
                                            </svg>

                                            {{-- Copied --}}
                                            <svg x-show="copiedField === 'password'" x-cloak
                                                class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>

                                    </div>
                                </div>

                                @error('password')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1 1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>

                        {{-- Buttons --}}
                        <div class="pt-6 mt-4 border-t border-gray-200 flex flex-col sm:flex-row justify-end gap-3">

                            {{-- Save --}}
                            <button type="submit" name="action" value="save" class="btn-primary px-8 py-2.5"
                                this.classList.add('opacity-50','cursor-not-allowed');>
                                SAVE
                            </button>

                            {{-- Send Email --}}
                            <button type="submit" name="action" value="send_email"
                                this.classList.add('opacity-50','cursor-not-allowed'); class="btn-primary px-8 py-2.5">
                                SEND EMAIL
                            </button>

                        </div>

                    </form>
                </div>
            </div>

            {{-- Actions Tab Content --}}
            <div x-show="activeTab === 'actions'" x-cloak>
                {{-- Consistent container like other tabs --}}
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 space-y-6 max-w-full">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-0">Account Actions</h2>
                    </div>

                    {{-- Deactivate Account Section --}}
                    <div class="pt-6 border-t border-gray-200">
                        @if ($customer->is_active == true)
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <div class="flex flex-col md:flex-row justify-between md:items-center">
                                    <div class="mb-3 md:mb-0">
                                        <h3 class="text-lg font-medium text-yellow-800 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            Deactivate Account
                                        </h3>
                                        <p class="text-sm text-yellow-700 mt-1 max-w-xl pl-7">
                                            Prevent the user from logging in. This action requires confirmation and should
                                            be
                                            used with caution.
                                        </p>
                                    </div>
                                    <form action="{{ route('admin.customers.deactivate', $customer) }}" method="POST"
                                        class="flex-shrink-0 mt-3 md:mt-0 md:ml-4">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            onclick="this.disabled=true; this.innerText='Deactivating...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                                            class="inline-flex items-center text-sm px-4 py-2 w-full md:w-auto 
                                         bg-gradient-to-r from-yellow-500 to-yellow-600
                                               font-medium rounded-lg border border-white text-white
                                                hover:from-yellow-600 hover:to-yellow-700
                                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 
                                               transition ease-in-out duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                            Deactivate Account
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <div class="flex flex-col md:flex-row justify-between md:items-center">
                                    <div class="mb-3 md:mb-0">
                                        <h3 class="text-lg font-medium text-yellow-800 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <p class="text-sm text-yellow-700 max-w-xl">
                                                <strong>Customer account is not activated.</strong> You can activate user
                                                account from action
                                                panel.
                                            </p>
                                        </h3>
                                    </div>
                                    <form action="{{ route('admin.customers.activate', $customer) }}" method="POST"
                                        class="flex-shrink-0 mt-3 md:mt-0 md:ml-4">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            onclick="this.disabled=true; this.innerText='Activating...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg text-sm font-medium hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7">
                                                </path>
                                            </svg>
                                            Activate Account
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                    {{-- Delete Account Section --}}
                    <div class="border-gray-200">
                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                            <div class="flex flex-col md:flex-row justify-between md:items-center">
                                <div class="mb-3 md:mb-0">
                                    <h3 class="text-lg font-medium text-red-800 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Delete Account
                                    </h3>
                                    <p class="text-sm text-red-700 mt-1 max-w-xl pl-7">
                                        By clicking this button, you will permanently delete this customer account and all
                                        associated records. This action cannot be undone and should be used with extreme
                                        caution.
                                    </p>
                                </div>
                                @if ($customer->is_active == true)
                                    <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST"
                                        class="inline delete-document-type-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            onclick="confirmDelete('{{ $customer->full_name }} Customer', this.form)"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-all duration-200 shadow-md hover:shadow-lg mr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            Delete Customer Account
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let debounceTimer;

        // $(document).ready(function() {

        //     $('#pin_code').on('input', function() {

        //         clearTimeout(debounceTimer);

        //         let pincode = $(this).val().trim();

        //         $('#pincode-error').text('');

        //         if (!/^\d*$/.test(pincode)) {
        //             $('#pincode-error').text('Only numbers allowed');
        //             $('#city').val('');
        //             $('#state').val('');
        //             return;
        //         }

        //         if (pincode.length !== 6) {
        //             $('#city').val('');
        //             $('#state').val('');
        //             return;
        //         }

        //         debounceTimer = setTimeout(function() {

        //             $.ajax({
        //                 url: "{{ route('admin.pincode.location') }}",
        //                 type: "POST",
        //                 data: {
        //                     _token: $('meta[name="csrf-token"]').attr('content'),
        //                     pincode: pincode
        //                 },
        //                 beforeSend: function() {
        //                     $('#city').val('Loading...');
        //                     $('#state').val('Loading...');
        //                 },
        //                 success: function(res) {
        //                     if (res.status === 'success') {
        //                         $('#city').val(res.city);
        //                         $('#state').val(res.state);
        //                         $('#pincode-error').text('');
        //                     } else {
        //                         $('#city').val('');
        //                         $('#state').val('');
        //                         $('#pincode-error').text(res.message ||
        //                             'Invalid pincode');
        //                     }
        //                 },
        //                 error: function(xhr) {
        //                     $('#city').val('');
        //                     $('#state').val('');

        //                     let msg = xhr.responseJSON?.message || 'Invalid pincode';
        //                     $('#pincode-error').text(msg);
        //                 }
        //             });

        //         }, 500);
        //     });

        // });

        $(document).ready(function() {

            let debounceTimer;
            let permanentDebounceTimer;

            $('#pin_code').on('input', function() {

                clearTimeout(debounceTimer);

                let pincode = $(this).val().trim();

                $('#pincode-error').text('');

                if (!/^\d*$/.test(pincode)) {

                    $('#pincode-error').text('Only numbers allowed');

                    $('#city').val('');
                    $('#state').val('');

                    return;
                }

                if (pincode.length !== 6) {

                    $('#city').val('');
                    $('#state').val('');

                    return;
                }

                debounceTimer = setTimeout(function() {

                    $.ajax({
                        url: "{{ route('admin.pincode.location') }}",
                        type: "POST",

                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            pincode: pincode
                        },

                        beforeSend: function() {

                            $('#city').val('Loading...');
                            $('#state').val('Loading...');

                        },

                        success: function(res) {

                            if (res.status === 'success') {

                                $('#city').val(res.city);
                                $('#state').val(res.state);
                                $('#pincode-error').text('');

                            } else {

                                $('#city').val('');
                                $('#state').val('');

                                $('#pincode-error').text(
                                    res.message || 'Invalid pincode'
                                );
                            }
                        },

                        error: function(xhr) {

                            $('#city').val('');
                            $('#state').val('');

                            let msg =
                                xhr.responseJSON?.message ||
                                'Invalid pincode';

                            $('#pincode-error').text(msg);
                        }
                    });

                }, 500);
            });

            $('#permanent_pin_code').on('input', function() {

                clearTimeout(permanentDebounceTimer);

                let pincode = $(this).val().trim();

                $('#permanent-pincode-error').text('');

                if (!/^\d*$/.test(pincode)) {

                    $('#permanent-pincode-error').text(
                        'Only numbers allowed'
                    );

                    $('#permanent_city').val('');
                    $('#permanent_state').val('');

                    return;
                }

                if (pincode.length !== 6) {

                    $('#permanent_city').val('');
                    $('#permanent_state').val('');

                    return;
                }

                permanentDebounceTimer = setTimeout(function() {

                    $.ajax({
                        url: "{{ route('admin.pincode.location') }}",
                        type: "POST",

                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            pincode: pincode
                        },

                        beforeSend: function() {

                            $('#permanent_city').val('Loading...');
                            $('#permanent_state').val('Loading...');

                        },

                        success: function(res) {

                            if (res.status === 'success') {

                                $('#permanent_city').val(res.city);
                                $('#permanent_state').val(res.state);

                                $('#permanent-pincode-error').text('');

                            } else {

                                $('#permanent_city').val('');
                                $('#permanent_state').val('');

                                $('#permanent-pincode-error').text(
                                    res.message || 'Invalid pincode'
                                );
                            }
                        },

                        error: function(xhr) {

                            $('#permanent_city').val('');
                            $('#permanent_state').val('');

                            let msg =
                                xhr.responseJSON?.message ||
                                'Invalid pincode';

                            $('#permanent-pincode-error').text(msg);
                        }
                    });

                }, 500);
            });

        });

        document.addEventListener('DOMContentLoaded', function() {
            const yesRadio = document.getElementById('is_address_permanent_yes');
            const noRadio = document.getElementById('is_address_permanent_no');
            const permanentFields = document.getElementById('permanent-address-fields');
            const permanentAddress = document.getElementById('permanent_address');
            const permanentPin = document.getElementById('permanent_pin_code');
            const permanentCity = document.getElementById('permanent_city');
            const permanentState = document.getElementById('permanent_state');

            function togglePermanentFields() {
                if (noRadio && noRadio
                    .checked) {
                    permanentFields.style.display = '';
                    permanentAddress.required = true;
                    permanentPin.required = true;
                    permanentCity.required = true;
                    permanentState.required = true;
                } else {
                    permanentFields.style.display = 'none';
                    permanentAddress.required = false;
                    permanentPin.required = false;
                    permanentCity.required = false;
                    permanentState.required = false;
                }
            }
            yesRadio.addEventListener('change', togglePermanentFields);
            noRadio.addEventListener('change', togglePermanentFields);
            togglePermanentFields();
        });

        function toggleSpouseName() {
            if ($('#marital_status').val() === 'married') {
                $('#spouse_name_div').show();
                $('#spouse_name').prop('required', true);
            } else {
                $('#spouse_name_div').hide();
                $('#spouse_name').prop('required', false);
                $('#spouse_name').val('');
            }
        }

        $(document).ready(function() {
            toggleSpouseName();
            $('#marital_status').on('change', function() {
                toggleSpouseName();
            });

        });

        function toggleOrganisationName() {
            if ($('#employment_type').val() === 'Government') {
                $('#organisation_name_div').show();
                $('#organisation_name').prop('required', true);
            } else {
                $('#organisation_name_div').hide();
                $('#organisation_name').prop('required', false);
                $('#organisation_name').val('');
            }
        }

        $(document).ready(function() {
            toggleOrganisationName();
            $('#employment_type').on('change', function() {
                toggleOrganisationName();
            });

        });

        function passportCredentialForm() {
            return {
                showPassword: false,
                copiedField: null,

                copyText(text, field) {
                    if (!text) {
                        return;
                    }

                    navigator.clipboard.writeText(text).then(() => {
                        this.copiedField = field;

                        setTimeout(() => {
                            this.copiedField = null;
                        }, 1500);
                    });
                }
            }
        }
    </script>
@endpush
