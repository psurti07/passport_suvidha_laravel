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

                    <form method="POST" action="{{ route('admin.customers.store') }}" class="space-y-8" novalidate>
                        @csrf

                        <div class="border-b border-gray-200 pb-3">
                            <div class="md:text-xl font-semibold tracking-tight text-xl flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="h-5 w-5 text-navy">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Applicant Details
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">

                            {{-- Registration Date --}}
                            <div>
                                <label for="payment_date" class="block text-sm font-semibold text-gray-900">
                                    Registration Date
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="date" id="payment_date" name="payment_date"
                                    value="{{ old('payment_date', date('Y-m-d')) }}" required
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200
                focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400
                @error('payment_date') border-red-300 text-red-900 @enderror">

                                @error('payment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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

                            {{-- Name and Surname --}}
                            <div>
                                <label for="full_name" class="block text-sm font-semibold text-gray-900">
                                    Full Name
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}"
                                    required placeholder="Enter name and surname"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200
                focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400
                @error('full_name') border-red-300 text-red-900 @enderror">

                                @error('full_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Gender --}}
                            <div>
                                <label for="gender" class="block text-sm font-semibold text-gray-900">
                                    Gender
                                    <span class="text-red-500">*</span>
                                </label>

                                <select id="gender" name="gender" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 pr-10 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm
                @error('gender') border-red-300 text-red-900 @enderror">

                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>
                                        Select gender
                                    </option>

                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>
                                        Male
                                    </option>

                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>
                                        Female
                                    </option>

                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>
                                        Other
                                    </option>
                                </select>

                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Date of Birth --}}
                            <div>
                                <label for="date_of_birth" class="block text-sm font-semibold text-gray-900">
                                    Date of Birth
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="date" id="date_of_birth" name="date_of_birth"
                                    value="{{ old('date_of_birth') }}" required
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200
                focus:ring-opacity-50 transition-all duration-200
                @error('date_of_birth') border-red-300 text-red-900 @enderror">

                                @error('date_of_birth')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Place of Birth --}}
                            <div>
                                <label for="place_of_birth" class="block text-sm font-semibold text-gray-900">
                                    Place of Birth
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="place_of_birth" name="place_of_birth"
                                    value="{{ old('place_of_birth') }}" required placeholder="Enter place of birth"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200
                focus:ring-opacity-50 transition-all duration-200
                @error('place_of_birth') border-red-300 text-red-900 @enderror">

                                @error('place_of_birth')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Marital Status --}}
                            <div>
                                <label for="marital_status" class="block text-sm font-semibold text-gray-900">
                                    Marital Status
                                    <span class="text-red-500">*</span>
                                </label>

                                <select id="marital_status" name="marital_status" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 pr-10 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm
                @error('marital_status') border-red-300 text-red-900 @enderror">

                                    <option value="" disabled {{ old('marital_status') ? '' : 'selected' }}>
                                        Select Marital Status
                                    </option>

                                    <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>
                                        Single
                                    </option>

                                    <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>
                                        Married
                                    </option>

                                    <option value="widow" {{ old('marital_status') == 'widow' ? 'selected' : '' }}>
                                        Widow
                                    </option>

                                    <option value="widower" {{ old('marital_status') == 'widower' ? 'selected' : '' }}>
                                        Widower
                                    </option>

                                    <option value="separated"
                                        {{ old('marital_status') == 'separated' ? 'selected' : '' }}>
                                        Separated
                                    </option>

                                    <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>
                                        Divorced
                                    </option>
                                </select>

                                @error('marital_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Employment Type --}}
                            <div>
                                <label for="employment_type" class="block text-sm font-semibold text-gray-900">
                                    Employment Type
                                    <span class="text-red-500">*</span>
                                </label>

                                <select id="employment_type" name="employment_type" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 pr-10 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm
                @error('employment_type') border-red-300 text-red-900 @enderror">

                                    <option value="" disabled {{ old('employment_type') ? '' : 'selected' }}>
                                        Select Employment Type
                                    </option>

                                    <option value="Government"
                                        {{ old('employment_type') == 'Government' ? 'selected' : '' }}>
                                        Government
                                    </option>

                                    <option value="Private" {{ old('employment_type') == 'Private' ? 'selected' : '' }}>
                                        Private
                                    </option>

                                    <option value="Self Employed"
                                        {{ old('employment_type') == 'Self Employed' ? 'selected' : '' }}>
                                        Self Employed
                                    </option>

                                    <option value="Student" {{ old('employment_type') == 'Student' ? 'selected' : '' }}>
                                        Student
                                    </option>

                                    <option value="Homemaker"
                                        {{ old('employment_type') == 'Homemaker' ? 'selected' : '' }}>
                                        Homemaker
                                    </option>

                                    <option value="Retired" {{ old('employment_type') == 'Retired' ? 'selected' : '' }}>
                                        Retired
                                    </option>

                                    <option value="Others" {{ old('employment_type') == 'Others' ? 'selected' : '' }}>
                                        Others
                                    </option>
                                </select>

                                @error('employment_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Organisation Name --}}
                            <div id="organisation-wrapper"
                                class="{{ old('employment_type') == 'Government' ? '' : 'hidden' }}">

                                <label for="organisation_name" class="block text-sm font-semibold text-gray-900">
                                    Organisation Name
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="organisation_name" name="organisation_name"
                                    value="{{ old('organisation_name') }}" placeholder="Enter organisation name"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                @error('organisation_name') border-red-300 @enderror">

                                @error('organisation_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Education Qualification --}}
                            <div>
                                <label for="education_qualification" class="block text-sm font-semibold text-gray-900">
                                    Education Qualification
                                    <span class="text-red-500">*</span>
                                </label>

                                <select id="education_qualification" name="education_qualification" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 pr-10 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm
                @error('education_qualification') border-red-300 text-red-900 @enderror">

                                    <option value="" disabled
                                        {{ old('education_qualification') ? '' : 'selected' }}>
                                        Select Education Qualification
                                    </option>

                                    <option value="10th Pass And Above"
                                        {{ old('education_qualification') == '10th Pass And Above' ? 'selected' : '' }}>
                                        10th Pass And Above
                                    </option>

                                    <option value="7th Pass Or Less"
                                        {{ old('education_qualification') == '7th Pass Or Less' ? 'selected' : '' }}>
                                        7th Pass Or Less
                                    </option>

                                    <option value="Between 8th And 9th Standard"
                                        {{ old('education_qualification') == 'Between 8th And 9th Standard' ? 'selected' : '' }}>
                                        Between 8th And 9th Standard
                                    </option>

                                    <option value="Graduate And Above"
                                        {{ old('education_qualification') == 'Graduate And Above' ? 'selected' : '' }}>
                                        Graduate And Above
                                    </option>
                                </select>

                                @error('education_qualification')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>


                        {{-- =========================================================
        FAMILY DETAILS
    ========================================================== --}}
                        <div class="border-b border-gray-200 pb-3 pt-4">
                            <div class="md:text-xl font-semibold tracking-tight text-xl flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-navy">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                                Family Details
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">

                            {{-- Father --}}
                            <div>
                                <label for="father_name" class="block text-sm font-semibold text-gray-900">
                                    Father Name
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="father_name" name="father_name"
                                    value="{{ old('father_name') }}" required placeholder="Enter father name"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                @error('father_name') border-red-300 text-red-900 @enderror">

                                @error('father_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Mother --}}
                            <div>
                                <label for="mother_name" class="block text-sm font-semibold text-gray-900">
                                    Mother Name
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="mother_name" name="mother_name"
                                    value="{{ old('mother_name') }}" required placeholder="Enter mother name"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                @error('mother_name') border-red-300 text-red-900 @enderror">

                                @error('mother_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Spouse --}}
                            <div id="spouse_name_div" class="{{ old('marital_status') == 'married' ? '' : 'hidden' }}">

                                <label for="spouse_name" class="block text-sm font-semibold text-gray-900">
                                    Spouse Name
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="spouse_name" name="spouse_name"
                                    value="{{ old('spouse_name') }}" placeholder="Enter spouse name"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                @error('spouse_name') border-red-300 text-red-900 @enderror">

                                @error('spouse_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>


                        {{-- =========================================================
        ADDRESS DETAILS
    ========================================================== --}}
                        <div class="border-b border-gray-200 pb-3 pt-4">
                            <div class="md:text-xl font-semibold tracking-tight text-xl flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-navy">
                                    <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                Address Details
                            </div>
                        </div>

                        {{-- Current Address + Pincode --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Current Address --}}
                            <div>
                                <label for="address" class="block text-sm font-semibold text-gray-900">
                                    Address
                                    <span class="text-red-500">*</span>
                                </label>

                                <textarea id="address" name="address" rows="3" required placeholder="Enter current address"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                @error('address') border-red-300 text-red-900 @enderror">{{ old('address') }}</textarea>

                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Pincode --}}
                            <div>
                                <label for="pin_code" class="block text-sm font-semibold text-gray-900">
                                    Pin Code
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="pin_code" name="pin_code" value="{{ old('pin_code') }}"
                                    required maxlength="6" minlength="6" inputmode="numeric"
                                    placeholder="Enter pin code" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                @error('pin_code') border-red-300 text-red-900 @enderror">

                                @error('pin_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <span id="pincode-error" class="text-red-500 text-sm"></span>
                            </div>



                            {{-- State --}}
                            <div>
                                <label for="state" class="block text-sm font-semibold text-gray-900">
                                    State
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="state" name="state" value="{{ old('state') }}"
                                    required readonly placeholder="State"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                                        py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                                        focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                                        @error('state') border-red-300 text-red-900 @enderror">

                                @error('state')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- District / City --}}
                            <div>
                                <label for="city" class="block text-sm font-semibold text-gray-900">
                                    City
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="city" name="city" value="{{ old('city') }}"
                                    required readonly placeholder="City"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                                        py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                                        focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                                        @error('city') border-red-300 text-red-900 @enderror">

                                @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>


                        {{-- Permanent Address Question --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">
                                    Is Permanent Address Same as Current Address?
                                    <span class="text-red-500">*</span>
                                </label>

                                <div class="flex items-center gap-6">

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="is_address_permanent" value="1"
                                            id="is_address_permanent_yes"
                                            {{ old('is_address_permanent', '1') == '1' ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">

                                        <span class="text-sm text-gray-700">Yes</span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="is_address_permanent" value="0"
                                            id="is_address_permanent_no"
                                            {{ old('is_address_permanent', '1') == '0' ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">

                                        <span class="text-sm text-gray-700">No</span>
                                    </label>

                                </div>

                                @error('is_address_permanent')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Permanent Address Fields --}}
                        <div id="permanent-address-fields" class="grid grid-cols-1 md:grid-cols-2 gap-6"
                            style="{{ old('is_address_permanent', '1') == '0' ? '' : 'display:none;' }}">

                            {{-- Permanent Address --}}
                            <div>
                                <label for="permanent_address" class="block text-sm font-semibold text-gray-900">
                                    Permanent Address
                                    <span class="text-red-500">*</span>
                                </label>

                                <textarea id="permanent_address" name="permanent_address" rows="3" placeholder="Enter permanent address"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                @error('permanent_address') border-red-300 text-red-900 @enderror">{{ old('permanent_address') }}</textarea>

                                @error('permanent_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Permanent Pin --}}
                            <div>
                                <label for="permanent_pin_code" class="block text-sm font-semibold text-gray-900">
                                    Permanent Pin Code
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="permanent_pin_code" name="permanent_pin_code"
                                    value="{{ old('permanent_pin_code') }}" maxlength="6" minlength="6"
                                    inputmode="numeric" placeholder="Enter permanent pin code"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                @error('permanent_pin_code') border-red-300 text-red-900 @enderror">

                                @error('permanent_pin_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <span id="permanent-pincode-error" class="text-red-500 text-sm"></span>
                            </div>

                            {{-- Permanent City --}}
                            <div>
                                <label for="permanent_city" class="block text-sm font-semibold text-gray-900">
                                    Permanent District
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="permanent_city" name="permanent_city"
                                    value="{{ old('permanent_city') }}" readonly placeholder="Permanent district    "
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                @error('permanent_city') border-red-300 text-red-900 @enderror">

                                @error('permanent_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Permanent State --}}
                            <div>
                                <label for="permanent_state" class="block text-sm font-semibold text-gray-900">
                                    Permanent State
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="permanent_state" name="permanent_state"
                                    value="{{ old('permanent_state') }}" readonly placeholder="Permanent state"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                @error('permanent_state') border-red-300 text-red-900 @enderror">

                                @error('permanent_state')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Mobile --}}
                            <div>
                                <label for="mobile_number" class="block text-sm font-semibold text-gray-900">
                                    Mobile Number
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="tel" id="mobile_number" name="mobile_number"
                                    value="{{ old('mobile_number') }}" required maxlength="10" inputmode="numeric"
                                    placeholder="Enter mobile number"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                @error('mobile_number') border-red-300 text-red-900 @enderror">

                                @error('mobile_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-900">
                                    Email ID
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    required placeholder="Enter email id"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                                        py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                                        focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                                        @error('email') border-red-300 text-red-900 @enderror">

                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Police Station --}}
                            <div>
                                <label for="police_station_name" class="block text-sm font-semibold text-gray-900">
                                    Nearest Police Station Name
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="police_station_name" name="police_station_name"
                                    value="{{ old('police_station_name') }}" required
                                    placeholder="Enter police station name"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                                        py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                                        focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                                        @error('police_station_name') border-red-300 text-red-900 @enderror">

                                @error('police_station_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Nationality --}}
                            <div>
                                <label for="nationality" class="block text-sm font-semibold text-gray-900">
                                    Nationality
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="nationality" name="nationality"
                                    value="{{ old('nationality') }}" required placeholder="Enter nationality"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                                        py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                                        focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                                        @error('nationality') border-red-300 text-red-900 @enderror">

                                @error('nationality')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="border-b border-gray-200 pb-3 pt-4">
                            <div class="md:text-xl font-semibold tracking-tight text-xl flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-navy">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                Emergency Contact Details
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">

                            {{-- Emergency Name / Address --}}
                            <div>
                                <label for="emergency_contact_name" class="block text-sm font-semibold text-gray-900">
                                    Emergency Contact Name
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                                    value="{{ old('emergency_contact_name') }}" required maxlength="10"
                                    inputmode="numeric" placeholder="Enter emergency contact name"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                @error('emergency_contact_name') border-red-300 text-red-900 @enderror">

                                @error('emergency_contact_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Emergency Mobile --}}
                            <div>
                                <label for="emergency_contact_mobile" class="block text-sm font-semibold text-gray-900">
                                    Mobile Number
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="tel" id="emergency_contact_mobile" name="emergency_contact_mobile"
                                    value="{{ old('emergency_contact_mobile') }}" required maxlength="10"
                                    inputmode="numeric" placeholder="Enter emergency contact mobile"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                @error('emergency_contact_mobile') border-red-300 text-red-900 @enderror">

                                @error('emergency_contact_mobile')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Emergency Email --}}
                            <div>
                                <label for="emergency_contact_email" class="block text-sm font-semibold text-gray-900">
                                    Email ID
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="email" id="emergency_contact_email" name="emergency_contact_email"
                                    value="{{ old('emergency_contact_email') }}" required
                                    placeholder="Enter emergency contact email"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200
                @error('emergency_contact_email') border-red-300 text-red-900 @enderror">

                                @error('emergency_contact_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>



                        {{-- =========================================================
                                APPLICATION DETAILS
                            ========================================================== --}}
                        <div class="border-b border-gray-200 pb-3 pt-4">
                            <div class="md:text-xl font-semibold tracking-tight text-xl flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-navy">
                                    <rect x="2" y="5" width="20" height="14" rx="2" />
                                    <line x1="2" y1="10" x2="22" y2="10" />
                                </svg>
                                Application Details
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Card Number --}}
                            <div>
                                <label for="card_number" class="block text-sm font-semibold text-gray-900">
                                    Card Number
                                </label>

                                <input type="text" id="card_number" name="card_number"
                                    value="{{ old('card_number', $cardNumber) }}" placeholder="Enter card number"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200"
                                    readonly>
                            </div>

                            {{-- Amount --}}
                            <div>
                                <label for="amount" class="block text-sm font-semibold text-gray-900">
                                    Card Amount (Note: 18% GST amount added on card amount).
                                </label>

                                <input type="number" id="amount" name="amount" value="{{ old('amount') }}"
                                    placeholder="Enter card amount"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200">
                            </div>

                            {{-- Payment ID --}}
                            <div>
                                <label for="payment_id" class="block text-sm font-semibold text-gray-900">
                                    Payment ID
                                </label>

                                <input type="text" id="payment_id" name="payment_id"
                                    value="{{ old('payment_id', $paymentId) }}" placeholder="Enter payment ID"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm
                py-2 px-3 mt-1 hover:border-gray-300 focus:border-blue-500
                focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200">
                            </div>

                        </div>


                        {{-- =========================================================
        FORM ACTIONS
    ========================================================== --}}
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">

                            <a href="{{ route('admin.customers.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300
            rounded-xl text-sm font-medium text-gray-700 bg-white
            hover:bg-gray-50 focus:outline-none focus:ring-2
            focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">

                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>

                                Back to List
                            </a>

                            <button type="submit"
                                onclick="this.disabled=true; this.innerText='Creating...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                                class="inline-flex items-center px-6 py-2 border border-transparent
            rounded-xl shadow-sm text-sm font-medium text-gray-900
            bg-gray-100 hover:bg-gray-200 focus:outline-none
            focus:ring-2 focus:ring-offset-2 focus:ring-gray-500
            transition-all duration-200">

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
                    // Show permanent address fields 
                    permanentFields.style.display = '';
                    // Make fields required 
                    permanentAddress.required = true;
                    permanentPin.required = true;
                    permanentCity.required = true;
                    permanentState.required = true;
                } else {
                    // Hide permanent address fields
                    permanentFields.style.display = 'none';
                    // Remove required when hidden 
                    permanentAddress.required = false;
                    permanentPin.required = false;
                    permanentCity.required = false;
                    permanentState.required = false;
                }
            } // Radio button events 
            yesRadio.addEventListener('change', togglePermanentFields);
            noRadio.addEventListener('change', togglePermanentFields);
            // Run once when page loads 
            togglePermanentFields();
        });

        $(document).ready(function() {

            function toggleOrganisation() {

                if ($('#employment_type').val() === 'Government') {

                    // Show organisation
                    $('#organisation-wrapper').removeClass('hidden');

                    // Make required
                    $('#organisation_name').prop('required', true);

                } else {

                    // Hide organisation
                    $('#organisation-wrapper').addClass('hidden');

                    // Make optional
                    $('#organisation_name').prop('required', false);

                    // Clear value
                    $('#organisation_name').val('');
                }
            }

            // Run when page loads
            toggleOrganisation();

            // Run when employment type changes
            $('#employment_type').on('change', function() {
                toggleOrganisation();
            });

        });
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

        //     toggleSpouseName();
        //     $('#marital_status').on('change', function() {
        //         toggleSpouseName();
        //     });

        // });

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

            $('#permanent_pin_code').on('input', function() {

                clearTimeout(debounceTimer);

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

                debounceTimer = setTimeout(function() {

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


            toggleSpouseName();
            $('#marital_status').on('change', function() {
                toggleSpouseName();
            });
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
    </script>
@endpush
