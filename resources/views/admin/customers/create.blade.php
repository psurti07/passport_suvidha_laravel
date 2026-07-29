@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="py-2 lg:py-8">
    <div class="mx-auto sm:px-1 lg:px-8">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            {{-- Header Section --}}
            <div class="px-8 py-4 lg:py-6 bg-gray-50 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Create New Customer</h2>
                <p class="text-sm text-gray-600 mt-1">Add a new customer paid to the system</p>
            </div>

            <div class="p-8 pt-0">
                <form method="POST" action="{{ route('admin.customers.store') }}" class="space-y-6" novalidate>
                    @csrf
                    <div class="border-b border-gray-200">
                        <div style="opacity: 1; transform: none;">
                            <div class="md:text-xl font-semibold tracking-tight text-xl flex items-center gap-2 mb-4"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user h-5 w-5 text-navy">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>Basic Information</div>
                        </div>
                        <div style="opacity: 1; transform: none;"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-sm font-semibold text-gray-900">
                                Full Name
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}"
                                    required placeholder="Enter full name"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                        transition-all duration-200 placeholder-gray-400
                                        @error('full_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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
                            <label for="mobile_number" class="block text-sm font-semibold text-gray-900">
                                Mobile Number
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="tel" id="mobile_number" name="mobile_number"
                                    value="{{ old('mobile_number') }}" required placeholder="Enter mobile number"
                                    maxlength="10" inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400
                                    @error('mobile_number') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-900">
                                Email Id
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    placeholder="Enter email id"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                        transition-all duration-200 placeholder-gray-400
                                        @error('email') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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
                            <label for="service_id" class="block text-sm font-semibold text-gray-900">
                                Service Name
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <select id="service_id" name="service_id" :required="isPaid"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm
                                        @error('service_id') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                    <option value="" disabled selected>Select service name</option>
                                    @foreach ($services as $service)
                                    <option value="{{ $service->id }}"
                                        {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->service_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('service_id')
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

                    <div class="border-b border-gray-200">
                        <div style="opacity: 1; transform: none;">
                            <div class="md:text-xl font-semibold tracking-tight text-xl flex items-center gap-2 mb-4"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="h-5 w-5 text-navy">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>Family Details</div>
                        </div>
                        <div style="opacity: 1; transform: none;"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="father_name" class="block text-sm font-semibold text-gray-900">
                                Father Name
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="father_name" name="father_name" value="{{ old('father_name') }}"
                                    required placeholder="Enter father name"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                        transition-all duration-200 placeholder-gray-400
                                        @error('father_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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
                            <label for="mother_name" class="block text-sm font-semibold text-gray-900">
                                Mother Name
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="mother_name" name="mother_name" value="{{ old('mother_name') }}"
                                    required placeholder="Enter father name"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                        transition-all duration-200 placeholder-gray-400
                                        @error('mother_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="marital_status" class="block text-sm font-semibold text-gray-900">
                                Marital Status
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="relative group">
                                <select id="marital_status" name="marital_status" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm @error('marital_status') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">

                                    <option value="" disabled selected>Select Marital Status</option>

                                    <option value="single"
                                        {{ old('marital_status') == 'single' ? 'selected' : '' }}>
                                        Single
                                    </option>

                                    <option value="married"
                                        {{ old('marital_status') == 'married' ? 'selected' : '' }}>
                                        Married
                                    </option>

                                    <option value="widow"
                                        {{ old('marital_status') == 'widow' ? 'selected' : '' }}>
                                        Widow
                                    </option>

                                    <option value="widower"
                                        {{ old('marital_status') == 'widower' ? 'selected' : '' }}>
                                        Widower
                                    </option>

                                    <option value="seperated"
                                        {{ old('marital_status') == 'seperated' ? 'selected' : '' }}>
                                        Seperated
                                    </option>

                                    <option value="divorced"
                                        {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>
                                        Divorced
                                    </option>
                                </select>
                            </div>

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
                        <div>
                            <label for="spouse_name" class="block text-sm font-semibold text-gray-900">
                                Spouse Name
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="spouse_name" name="spouse_name" value="{{ old('spouse_name') }}"
                                    required placeholder="Enter spouse name"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                        transition-all duration-200 placeholder-gray-400
                                        @error('spouse_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-semibold text-gray-900">
                                Emergency Contact Name
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                                    required placeholder="Enter emergency contact name"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                        transition-all duration-200 placeholder-gray-400
                                        @error('emergency_contact_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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
                            <label for="emergency_contact_mobile" class="block text-sm font-semibold text-gray-900">
                                Emergency Contact Mobile
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="tel" id="emergency_contact_mobile" name="emergency_contact_mobile"
                                    value="{{ old('emergency_contact_mobile') }}" required placeholder="Enter emergency contact mobile"
                                    maxlength="10" inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400
                                    @error('emergency_contact_mobile') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="emergency_contact_email" class="block text-sm font-semibold text-gray-900">
                                Emergency Contact Email
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="emergency_contact_email" id="emergency_contact_email" name="emergency_contact_email" value="{{ old('emergency_contact_email') }}" required
                                    placeholder="Enter emergency contact email"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                        transition-all duration-200 placeholder-gray-400
                                        @error('emergency_contact_email') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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
                    </div>

                    <div class="border-b border-gray-200">
                        <div style="opacity: 1; transform: none;">
                            <div class="md:text-xl font-semibold tracking-tight text-xl flex items-center gap-2 mb-4"><svg xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="h-5 w-5 text-navy">
                                    <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>Personal Details</div>
                        </div>
                        <div style="opacity: 1; transform: none;"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-12">
                        <div>
                            <label for="address" class="block text-sm font-semibold text-gray-900">
                                Address
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <textarea id="address" name="address" :required="isPaid" placeholder="Enter address" rows="3"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('address') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('address') }}</textarea>
                            </div>
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
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="pin_code" class="block text-sm font-semibold text-gray-900">
                                Pincode
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="pin_code" name="pin_code" inputmode="numeric"
                                    value="{{ old('pin_code') }}" :required="isPaid"
                                    placeholder="Enter pincode" maxlength="6" minlength="6"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('pin_code') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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
                            <label for="city" class="block text-sm font-semibold text-gray-900">
                                City
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="city" name="city" value="{{ old('city') }}"
                                    :required="isPaid" placeholder="Enter city" readonly
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('city') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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

                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="state" class="block text-sm font-semibold text-gray-900">
                                State
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="state" name="state" value="{{ old('state') }}"
                                    :required="isPaid" placeholder="Enter state" readonly
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('state') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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
                            <label for="pin_code" class="block text-sm font-semibold text-gray-900">
                                Nearest Police Station Pincode
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="pin_code" name="pin_code" inputmode="numeric"
                                    value="{{ old('pin_code') }}" :required="isPaid"
                                    placeholder="Enter pincode" maxlength="6" minlength="6"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('pin_code') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="gender" class="block text-sm font-semibold text-gray-900">
                                Gender
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <select id="gender" name="gender" :required="isPaid"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm
                                        @error('gender') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                    <option value="" disabled selected>Select gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male
                                    </option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>
                                        Female
                                    </option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other
                                    </option>
                                </select>
                            </div>
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
                            <label for="date_of_birth" class="block text-sm font-semibold text-gray-900">
                                Date of Birth
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <input type="date" id="date_of_birth" name="date_of_birth"
                                    value="{{ old('date_of_birth') }}" :required="isPaid"
                                    placeholder="Enter date of birth"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('date_of_birth') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="education_qualification"
                                class="block text-sm font-semibold text-gray-900">
                                Education Qualification
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="relative group">
                                <select id="education_qualification" name="education_qualification" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm @error('education_qualification') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">

                                    <option value="" disabled selected>Select Education Qualification
                                    </option>

                                    <option value="Below 10th"
                                        {{ old('education_qualification') == 'Below 10th' ? 'selected' : '' }}>
                                        Below 10th
                                    </option>

                                    <option value="10th Pass And Above"
                                        {{ old('education_qualification') == '10th Pass And Above' ? 'selected' : '' }}>
                                        10th Pass And Above
                                    </option>

                                    <option value="Graduate And Above"
                                        {{ old('education_qualification') == 'Graduate And Above' ? 'selected' : '' }}>
                                        Graduate And Above
                                    </option>
                                </select>
                            </div>

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
                            <label for="employment_type" class="block text-sm font-semibold text-gray-900">
                                Employment Type
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="relative group">
                                <select id="employment_type" name="employment_type" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm @error('employment_type') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">

                                    <option value="" disabled selected>Select Employment Type</option>

                                    <option value="Government"
                                        {{ old('employment_type') == 'Government' ? 'selected' : '' }}>
                                        Government
                                    </option>

                                    <option value="Private"
                                        {{ old('employment_type') == 'Private' ? 'selected' : '' }}>
                                        Private
                                    </option>

                                    <option value="Self Employed"
                                        {{ old('employment_type') == 'Self Employed' ? 'selected' : '' }}>
                                        Self Employed
                                    </option>

                                    <option value="Student"
                                        {{ old('employment_type') == 'Student' ? 'selected' : '' }}>
                                        Student
                                    </option>

                                    <option value="Homemaker"
                                        {{ old('employment_type') == 'Homemaker' ? 'selected' : '' }}>
                                        Homemaker
                                    </option>

                                    <option value="Retired"
                                        {{ old('employment_type') == 'Retired' ? 'selected' : '' }}>
                                        Retired
                                    </option>

                                    <option value="Others"
                                        {{ old('employment_type') == 'Others' ? 'selected' : '' }}>
                                        Others
                                    </option>
                                </select>
                            </div>

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
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nationality" class="block text-sm font-semibold text-gray-900">
                                Nationality
                                <span class="text-red-500" x-show="isPaid">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="nationality" name="nationality"
                                    value="{{ old('nationality') }}" :required="isPaid"
                                    placeholder="Enter nationality"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('nationality') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
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

                    <div class="border-b border-gray-200">
                        <div style="opacity: 1; transform: none;">
                            <div class="md:text-xl font-semibold tracking-tight text-xl flex items-center gap-2 mb-4"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="h-5 w-5 text-navy">
                                    <rect x="2" y="5" width="20" height="14" rx="2" />
                                    <line x1="2" y1="10" x2="22" y2="10" />
                                </svg>Application Card Details</div>
                        </div>
                        <div style="opacity: 1; transform: none;"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="card_number" class="block text-sm font-semibold text-gray-900">
                                Card Number
                            </label>
                            <div class="relative group">
                                <input type="text" id="card_number" name="card_number"
                                    value="{{ old('card_number', $cardNumber) }}"
                                    placeholder="Enter card number"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                                hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                                transition-all duration-200 placeholder-gray-400">
                            </div>
                        </div>
                        <div>
                            <label for="amount" class="block text-sm font-semibold text-gray-900">
                                Card Amount (Note: 18% GST
                                amount added on card amount.)
                            </label>
                            <div class="relative group">
                                <input type="number" id="amount" name="amount"
                                    value="{{ old('amount') }}" placeholder="Enter card amount"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                                hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                                transition-all duration-200 placeholder-gray-400">
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="payment_id" class="block text-sm font-semibold text-gray-900">
                                Payment Id
                            </label>
                            <div class="relative group">
                                <input type="text" id="payment_id" name="payment_id"
                                    value="{{ old('payment_id', $paymentId) }}"
                                    placeholder="Enter payment id"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                                hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                                transition-all duration-200 placeholder-gray-400">
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.customers.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to List
                        </a>
                        <button type="submit"
                            onclick="this.disabled=true; this.innerText='Creating...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-xl shadow-sm text-sm font-medium text-gray-900 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Create Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let debounceTimer;

    $(document).ready(function() {

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
                            $('#pincode-error').text(res.message ||
                                'Invalid pincode');
                        }
                    },
                    error: function(xhr) {
                        $('#city').val('');
                        $('#state').val('');

                        let msg = xhr.responseJSON?.message || 'Invalid pincode';
                        $('#pincode-error').text(msg);
                    }
                });

            }, 500);
        });

    });
</script>
@endpush