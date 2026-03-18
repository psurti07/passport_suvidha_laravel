@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="py-2 lg:py-8">
    <div class="max-w-3xl mx-auto sm:px-1 lg:px-8">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Header Section -->
            <div class="px-8 py-4 lg:py-6 bg-gray-50 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Create New Customer</h2>
                <p class="text-sm text-gray-600 mt-1">Add a new customer (lead or paid) to the system</p>
            </div>

            <div class="p-8 p-0">
                <form method="POST" action="{{ route('admin.customers.store') }}" class="space-y-6" novalidate>
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <!-- First Name Field -->
                        <div class="space-y-2">
                            <label for="first_name" class="block text-sm font-semibold text-gray-900">
                                First Name
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input id="first_name" 
                                    type="text" 
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                        transition-all duration-200 placeholder-gray-400
                                        @error('first_name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                    name="first_name" 
                                    value="{{ old('first_name') }}" 
                                    required 
                                    autocomplete="first_name" 
                                    placeholder="Enter first name">
                            </div>
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rul     ="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <!-- Last Name Field -->
                        <div class="space-y-2">
                            <label for="last_name" class="block text-sm font-semibold text-gray-900">
                                Last Name
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input id="last_name" 
                                    type="text" 
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                        transition-all duration-200 placeholder-gray-400
                                        @error('last_name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                    name="last_name" 
                                    value="{{ old('last_name') }}" 
                                    required 
                                    autocomplete="last_name" 
                                    placeholder="Enter last name">
                            </div>
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Email Address Field -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-semibold text-gray-900">
                                Email Address
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input id="email" 
                                    type="email" 
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                        transition-all duration-200 placeholder-gray-400
                                        @error('email') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    autocomplete="email"
                                    placeholder="Enter email address">
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <!-- Mobile Number Field -->
                        <div class="space-y-2">
                            <label for="mobile_number" class="block text-sm font-semibold text-gray-900">
                                Mobile Number
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input id="mobile_number" 
                                    type="tel" 
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                        transition-all duration-200 placeholder-gray-400
                                        @error('mobile_number') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                    name="mobile_number" 
                                    value="{{ old('mobile_number') }}" 
                                    maxlength="10"
                                    oninput="validateMobile(this)"
                                    required 
                                    autocomplete="mobile_number" 
                                    placeholder="Enter mobile number">
                            </div>
                            @error('mobile_number')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Paid Customer Requirements -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <div class="flex items-center">
                            <input id="is_paid" name="is_paid" type="checkbox" value="1" {{ old('is_paid') ? 'checked' : '' }} 
                                class="h-4 w-4 text-primary-blue focus:ring-secondary-blue border-gray-300 rounded"
                                x-data x-ref="isPaidCheckbox" @change="$dispatch('paid-status-change', $el.checked)">
                            <label for="is_paid" class="ml-2 block text-sm font-semibold text-gray-900">Mark as Paid Customer</label>
                        </div>
                    </div>

                    <div x-data="{ isPaid: {{ old('is_paid') ? 'true' : 'false' }} }" @paid-status-change.window="isPaid = $event.detail" x-show="isPaid" x-transition class="space-y-6 border-t border-gray-200 pt-6">
                        <p class="text-sm text-gray-500">Enter additional details for the paid customer:</p>
                        <div class="grid grid-cols-12 md:grid-cols-12 gap-12">
                            <!-- Address Field -->
                            <div class="space-y-2">
                                <label for="address" class="block text-sm font-semibold text-gray-900">
                                    Address
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <textarea id="address" 
                                        class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('address') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                        name="address" 
                                        rows="3"
                                        :required="isPaid" 
                                        autocomplete="address"
                                        placeholder="Enter address">{{ old('address') }}</textarea>
                                </div>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Pincode Field -->
                            <div class="space-y-2">
                                <label for="pin_code" class="block text-sm font-semibold text-gray-900">
                                    Pincode
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <input id="pin_code" 
                                        type="text" 
                                        class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('pin_code') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                        name="pin_code" 
                                        value="{{ old('pin_code') }}" 
                                        :required="isPaid" 
                                        autocomplete="pin_code"
                                        placeholder="Enter pincode">
                                </div>
                                @error('pin_code')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <!-- City Field -->
                            <div class="space-y-2">
                                <label for="city" class="block text-sm font-semibold text-gray-900">
                                    City
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <input id="city" 
                                        type="text" 
                                        class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('city') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                        name="city" 
                                        value="{{ old('city') }}" 
                                        :required="isPaid" 
                                        autocomplete="city"
                                        placeholder="Enter city">
                                </div>
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- State Field -->
                            <div class="space-y-2">
                                <label for="state" class="block text-sm font-semibold text-gray-900">
                                    State
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <input id="state" 
                                        type="text" 
                                        class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('state') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                        name="state" 
                                        value="{{ old('state') }}" 
                                        :required="isPaid" 
                                        autocomplete="state"
                                        placeholder="Enter state">
                                </div>
                                @error('state')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <!-- Gender Field -->
                            <div class="space-y-2">
                                <label for="gender" class="block text-sm font-semibold text-gray-900">
                                    Gender
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <select name="gender" id="gender" :required="isPaid" 
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm">
                                        <option value="" disabled selected>Select gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Date of Birth Field -->
                            <div class="space-y-2">
                                <label for="date_of_birth" class="block text-sm font-semibold text-gray-900">
                                    Date of Birth
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <input id="date_of_birth" 
                                        type="date" 
                                        class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('date_of_birth') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                        name="date_of_birth" 
                                        value="{{ old('date_of_birth') }}" 
                                        :required="isPaid" 
                                        autocomplete="date_of_birth"
                                        placeholder="Enter date of birth">
                                </div>
                                @error('date_of_birth')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <!-- Place of Birth Field -->
                            <div class="space-y-2">
                                <label for="place_of_birth" class="block text-sm font-semibold text-gray-900">
                                    Place of Birth
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <input id="place_of_birth" 
                                        type="text" 
                                        class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('place_of_birth') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                        name="place_of_birth" 
                                        value="{{ old('place_of_birth') }}" 
                                        :required="isPaid" 
                                        autocomplete="place_of_birth"
                                        placeholder="Enter place of birth">
                                </div>
                                @error('place_of_birth')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nationality Field -->
                            <div class="space-y-2">
                                <label for="nationality" class="block text-sm font-semibold text-gray-900">
                                    Nationality
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <input id="nationality" 
                                        type="text" 
                                        class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('nationality') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                        name="nationality" 
                                        value="{{ old('nationality') }}" 
                                        :required="isPaid" 
                                        autocomplete="nationality"
                                        placeholder="Enter nationality">
                                </div>
                                @error('nationality')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <!-- Service Code Field -->
                            <div class="space-y-2">
                                <label for="service_code" class="block text-sm font-semibold text-gray-900">
                                    Service Code
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <select name="service_code" id="service_code" :required="isPaid" 
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm">
                                        <option value="" disabled selected>Select service code</option>
                                        <option value="NORMAL_36" {{ old('service_code') == 'NORMAL_36' ? 'selected' : '' }}>NORMAL 36</option>
                                        <option value="NORMAL_60" {{ old('service_code') == 'NORMAL_60' ? 'selected' : '' }}>NORMAL 60</option>
                                        <option value="TATKAL_36" {{ old('service_code') == 'TATKAL_36' ? 'selected' : '' }}>TATKAL 36</option>
                                        <option value="TATKAL_60" {{ old('service_code') == 'TATKAL_60' ? 'selected' : '' }}>TATKAL 60</option>
                                    </select>
                                </div>
                                @error('service_code')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-6 border-t border-gray-200 pt-6">
                            <p class="text-sm text-gray-500">Enter application card details for the paid customer:</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Card Number Field -->
                                <div class="space-y-2">
                                    <label for="card_number" class="block text-sm font-semibold text-gray-900">
                                        Card Number
                                    </label>
                                    <div class="relative group">
                                        <input id="card_number" 
                                            type="text" 
                                            class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                                hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                                transition-all duration-200 placeholder-gray-400
                                                @error('card_number') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                            name="card_number" 
                                            value="{{ old('card_number', $card_number) }}" 
                                            autocomplete="card_number"
                                            placeholder="Enter card number">
                                    </div>
                                    @error('card_number')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div class="space-y-2">
                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                        <div class="flex items-center">
                                            <label class="ml-2 block text-sm font-semibold text-gray-900">Note: 18% GST amount added on card amount.</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Card Amount Field -->
                                <div class="space-y-2">
                                    <label for="amount" class="block text-sm font-semibold text-gray-900">
                                        Card Amount
                                    </label>
                                    <div class="relative group">
                                        <input id="amount" 
                                            type="text" 
                                            class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                                hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                                transition-all duration-200 placeholder-gray-400
                                                @error('amount') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                            name="amount" 
                                            value="{{ old('amount') }}" 
                                            autocomplete="amount"
                                            placeholder="Enter card amount">
                                    </div>
                                    @error('amount')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <!-- Payment Id Field -->
                                <div class="space-y-2">
                                    <label for="paymentid" class="block text-sm font-semibold text-gray-900">
                                        Payment Id
                                    </label>
                                    <div class="relative group">
                                        <input id="paymentid" 
                                            type="text" 
                                            class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                                hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                                transition-all duration-200 placeholder-gray-400
                                                @error('paymentid') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                            name="paymentid" 
                                            value="{{ old('paymentid', $paymentid) }}" 
                                            autocomplete="paymentid"
                                            placeholder="Enter payment id">
                                    </div>
                                    @error('paymentid')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.customers.index') }}" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to List
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 border border-transparent rounded-xl shadow-sm text-sm font-medium text-gray-900 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Save Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const inputs = document.querySelectorAll("input, textarea, select");
        inputs.forEach(input => {
            input.addEventListener("input", function () {
                this.classList.remove("border-red-300");
                const wrapper = this.closest(".space-y-2");
                const error = wrapper?.querySelector(".text-red-600");
                if (error) {
                    error.style.display = "none";
                }
            });
        });
    });

    function validateMobile(input){
        input.value = input.value.replace(/[^0-9]/g,'');
        if(input.value.length === 1 && input.value < 6){
            input.value = '';
        }
    }
</script>
@endpush

