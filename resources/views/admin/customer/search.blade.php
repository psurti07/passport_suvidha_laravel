@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Search Form Card --}}
        <div class="md:col-span-1 bg-white p-8 rounded-lg shadow-lg border border-gray-200 md:self-start">
            <h2 class="text-xl font-semibold mb-6 text-gray-800 border-b pb-3">Search Customer</h2>
            <form action="{{ route('admin.customer.search') }}" method="POST" class="mt-6 space-y-6">
                @csrf

                <div>
                    <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Mobile or Email *</label>
                    <input type="text" name="search" id="search" value="{{ old('search', $search ?? '') }}"
                        maxlength="255"
                        class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('search') border-red-500 @enderror"
                        title="Mobile number must be 10 digits" placeholder="Enter 10-digit mobile or email">
                    <p id="search-error" class="text-red-600 text-sm italic mt-2 hidden"></p>
                    @error('search')
                        <p class="text-red-600 text-sm italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-start pt-2">
                    <button type="submit"
                        onclick="this.disabled=true; this.innerText='Searching...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                        class="btn-primary px-6 py-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                    </button>
                </div>
            </form>
        </div>

        {{-- Search Results Card --}}
        @if (isset($search) || isset($customer))
            <div class="md:col-span-2 bg-white p-8 rounded-lg shadow-lg border border-gray-200">
                <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Search Result</h2>
                    @if ($customer && $customer->is_paid == 0)
                        <form action="{{ route('admin.lead.delete', $customer->id) }}" method="post">
                            {{-- onsubmit="confirmDelete('{{ $customer->mobile_number }} Customer', this.form)"> --}}
                            @csrf
                            @method('delete')
                            <button type="button"
                                onclick="confirmDelete('{{ ucwords(strtolower($customer->full_name)) }} Lead', this.form)"
                                class="gap-2 inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-all duration-200 shadow-md hover:shadow-lg mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Delete Lead
                            </button>
                        </form>
                    @endif
                </div>
                <div class="mt-6">
                    @if ($customer)
                        <dl class="divide-y divide-gray-200">
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-md font-bold text-gray-500">Module</dt>
                                @if ($customer->is_paid == 1)
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold">CUSTOMER</dd>
                                @else
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold">LEAD CUSTOMER
                                    </dd>
                                @endif
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-md font-bold text-gray-500">Mobile</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ $customer->mobile_number ?? 'N/A' }}
                                </dd>
                                {{-- Assuming mobile_number from controller fix --}}
                            </div>
                            <!-- <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">                                                                                                               </div> -->
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-md font-bold text-gray-500">
                                    {{ $customer->is_paid == 1 ? 'Registration Date' : 'Created Date' }}
                                </dt>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    @if ($customer->is_paid == 1 && $customer->payment_date)
                                        {{ \Carbon\Carbon::parse($customer->payment_date)->format('d M Y, h:i A') }}
                                    @elseif($customer->created_at)
                                        {{ $customer->created_at->format('d M Y, h:i A') }}
                                    @else
                                        N/A
                                    @endif
                                </dd>
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-md font-bold text-gray-500">Full Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ ucwords(strtolower($customer->full_name)) ?? '' }}
                                </dd>
                                {{-- Assuming full_name exist --}}
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-md font-bold text-gray-500">Email Address</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ $customer->email ?? 'N/A' }}
                                </dd>
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-md font-bold text-gray-500">Services</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ session('service_name') ?? 'N/A' }}
                                </dd>
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-md font-bold text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    @if ($customer->is_paid == 1)
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Registered Customer
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Lead
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                        @if ($customer->is_paid == 1)
                            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end gap-3">
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn-primary px-5 py-2">
                                    View Full Details &rarr;
                                </a>
                                <a href="{{ route('admin.customers.show', $customer->id) }}#application-process"
                                    class="btn-primary px-5 py-2">
                                    Application Process &rarr;
                                </a>
                            </div>
                        @else
                            <div x-data="convertCustomerComponent({{ $errors->any() ? 'true' : 'false' }})">
                                <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end">
                                    <button type="button" @click="showCustomerForm = !showCustomerForm"
                                        class="btn-primary inline-flex items-center px-4 py-2">
                                        <span x-text="showCustomerForm ? 'Close' : 'Convert to Customer &rarr;'">Convert to
                                            Customer
                                            &rarr;</span>
                                    </button>
                                </div>
                                {{-- Customer Form (Initially Hidden) --}}
                                <div x-show="showCustomerForm" x-cloak x-transition
                                    class="border border-gray-200 rounded-lg p-5 mt-6 mb-6 bg-gray-50">
                                    <h2 class="text-lg font-semibold text-gray-800">Convert to Customer</h2>
                                    <form action="{{ route('admin.customers.convert', $customer->id) }}" method="POST"
                                        enctype="multipart/form-data" class="space-y-5" novalidate>
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="mobile_number" value="{{ $customer->mobile_number }}">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                                            <div>
                                                <label for="payment_date"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Registration Date
                                                    <span class="text-red-500">*</span></label>
                                                <input type="date" id="payment_date" name="payment_date"
                                                    value="{{ old('payment_date', $customer->payment_date ? $customer->payment_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                                    required placeholder="Enter registration date"
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                @error('payment_date')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="father_name"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Father Name <span
                                                        class="text-red-500">*</span></label>
                                                <input type="text" id="father_name" name="father_name"
                                                    value="{{ old('father_name', $customer->father_name) }}" required
                                                    placeholder="Enter Father Name"
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                @error('father_name')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="mother_name"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Mother Name <span
                                                        class="text-red-500">*</span></label>
                                                <input type="text" id="mother_name" name="mother_name"
                                                    value="{{ old('mother_name', $customer->mother_name) }}" required
                                                    placeholder="Enter Mother Name"
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                @error('mother_name')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="marital_status"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Marital Status
                                                    <span class="text-red-500">*</span></label>
                                                <select id="marital_status" name="marital_status" required
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm">
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
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div id="spouse_name_div">
                                                <label for="spouse_name"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Spouse Name <span
                                                        class="text-red-500">*</span></label>
                                                <input type="text" id="spouse_name" name="spouse_name"
                                                    value="{{ old('spouse_name', $customer->spouse_name) }}" required
                                                    placeholder="Enter Spouse Name"
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                @error('spouse_name')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="emergency_contact_name"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact
                                                    Name <span class="text-red-500">*</span></label>
                                                <input type="text" id="emergency_contact_name"
                                                    name="emergency_contact_name"
                                                    value="{{ old('emergency_contact_name', $customer->emergency_contact_name) }}"
                                                    required placeholder="Enter Emergency Contact Name"
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                @error('emergency_contact_name')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="emergency_contact_mobile"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact
                                                    Mobile <span class="text-red-500">*</span></label>
                                                <input type="tel" id="emergency_contact_mobile"
                                                    name="emergency_contact_mobile"
                                                    value="{{ old('emergency_contact_mobile', $customer->emergency_contact_mobile) }}"
                                                    required maxlength="10" inputmode="numeric"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                    placeholder="Enter Emergency Contact Mobile"
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                @error('emergency_contact_mobile')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="emergency_contact_email"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact
                                                    Email <span class="text-red-500">*</span></label>
                                                <input type="tel" id="emergency_contact_email"
                                                    name="emergency_contact_email"
                                                    value="{{ old('emergency_contact_email', $customer->emergency_contact_email) }}"
                                                    required placeholder="Enter Emergency Contact Email"
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                @error('emergency_contact_email')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="address"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                                        class="text-red-500">*</span></label>
                                                <textarea id="address" name="address" required placeholder="Enter address" rows="2"
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">{{ old('address', $customer->address ?? '') }}</textarea>
                                                @error('address')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="pin_code"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Pincode <span
                                                        class="text-red-500">*</span></label>
                                                <input type="text" id="pin_code" name="pin_code"
                                                    value="{{ old('pin_code', $customer->pin_code) }}" required
                                                    placeholder="Enter pincode" maxlength="6" minlength="6"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                @error('pin_code')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                                <span id="pincode-error" class="text-red-500 text-sm"></span>
                                            </div>

                                            <div>
                                                <label for="city"
                                                    class="block text-sm font-medium text-gray-700 mb-1">City <span
                                                        class="text-red-500">*</span></label>
                                                <input type="text" id="city" name="city"
                                                    value="{{ old('city', $customer->city) }}" required
                                                    placeholder="Enter city" readonly
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                @error('city')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="state"
                                                    class="block text-sm font-medium text-gray-700 mb-1">State <span
                                                        class="text-red-500">*</span></label>
                                                <input type="text" id="state" name="state"
                                                    value="{{ old('state', $customer->state) }}" required
                                                    placeholder="Enter state" readonly
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                @error('state')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Is Permanent Address Same as Current Address?
                                                    <span class="text-red-500">*</span>
                                                </label>

                                                <div class="flex items-center gap-6">
                                                    {{-- YES --}}
                                                    <label class="flex items-center gap-2 cursor-pointer">
                                                        <input type="radio" name="is_address_permanent" value="1"
                                                            id="is_address_permanent_yes"
                                                            {{ old('is_address_permanent', $customer->is_address_permanent ?? '1') == '1' ? 'checked' : '' }}
                                                            class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                        <span class="text-sm text-gray-700">Yes</span>
                                                    </label>

                                                    {{-- NO --}}
                                                    <label class="flex items-center gap-2 cursor-pointer">
                                                        <input type="radio" name="is_address_permanent" value="0"
                                                            id="is_address_permanent_no"
                                                            {{ old('is_address_permanent', $customer->is_address_permanent ?? '1') == '0' ? 'checked' : '' }}
                                                            class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                        <span class="text-sm text-gray-700">No</span>
                                                    </label>
                                                </div>

                                                @error('is_address_permanent')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div id="permanent-address-fields"
                                                class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-5"
                                                style="{{ old('is_address_permanent', $customer->is_address_permanent ?? '1') == '0' ? '' : 'display:none;' }}">

                                                <div>
                                                    <label for="permanent_address"
                                                        class="block text-sm font-medium text-gray-700 mb-1">
                                                        Permanent Address
                                                        <span class="text-red-500">*</span>
                                                    </label>

                                                    <textarea id="permanent_address" name="permanent_address" rows="2" placeholder="Enter permanent address"
                                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">{{ old('permanent_address', $customer->permanent_address ?? '') }}</textarea>

                                                    @error('permanent_address')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label for="permanent_pin_code"
                                                        class="block text-sm font-medium text-gray-700 mb-1">
                                                        Permanent Pincode
                                                        <span class="text-red-500">*</span>
                                                    </label>

                                                    <input type="text" id="permanent_pin_code"
                                                        name="permanent_pin_code"
                                                        value="{{ old('permanent_pin_code', $customer->permanent_pin_code ?? '') }}"
                                                        placeholder="Enter pincode" maxlength="6" minlength="6"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">

                                                    @error('permanent_pin_code')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror

                                                    <span id="permanent-pincode-error"
                                                        class="text-red-500 text-sm"></span>
                                                </div>

                                                <div>
                                                    <label for="permanent_city"
                                                        class="block text-sm font-medium text-gray-700 mb-1">Permanent City
                                                        <span class="text-red-500">*</span></label>
                                                    <input type="text" id="permanent_city" name="permanent_city"
                                                        value="{{ old('permanent_city', $customer->permanent_city) }}"
                                                        required placeholder="Enter permanent_city" readonly
                                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                    @error('permanent_city')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label for="permanent_state"
                                                        class="block text-sm font-medium text-gray-700 mb-1">
                                                        Permanent State
                                                        <span class="text-red-500">*</span>
                                                    </label>

                                                    <input type="text" id="permanent_state" name="permanent_state"
                                                        value="{{ old('permanent_state', $customer->permanent_state ?? '') }}"
                                                        placeholder="Enter state" readonly
                                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">

                                                    @error('permanent_state')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                            </div>

                                            <div>
                                                <label for="police_station_name"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Nearest Police
                                                    Station Name <span class="text-red-500">*</span></label>
                                                <input type="text" id="police_station_name" name="police_station_name"
                                                    value="{{ old('police_station_name', $customer->police_station_name) }}"
                                                    required placeholder="Enter Police Station Name"
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                @error('police_station_name')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="gender"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Gender <span
                                                        class="text-red-500">*</span></label>
                                                <select id="gender" name="gender" required
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm">
                                                    <option value="" disabled selected>Select gender</option>
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

                                                    {{-- Add other states --}}
                                                </select>
                                                @error('gender')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="date_of_birth"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Date of
                                                    Birth <span class="text-red-500">*</span></label>
                                                <input type="date" id="date_of_birth" name="date_of_birth"
                                                    value="{{ old('date_of_birth', $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '') }}"
                                                    required placeholder="Enter date of birth"
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                @error('date_of_birth')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="place_of_birth"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Place
                                                    of
                                                    Birth <span class="text-red-500">*</span></label>
                                                <input type="text" id="place_of_birth" name="place_of_birth"
                                                    value="{{ old('place_of_birth', $customer->place_of_birth ?? '') }}"
                                                    required placeholder="Enter place of birth"
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                @error('place_of_birth')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="education_qualification"
                                                    class="block text-sm font-medium text-gray-700 mb-1"> Education
                                                    Qualification
                                                    <span class="text-red-500">*</span></label>
                                                <select id="education_qualification" name="education_qualification"
                                                    required
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm">
                                                    <option value="">Select Education Qualification</option>

                                                    <!-- <option value="Below 10th"
                                                                                                                                                                                                                                        {{ strtolower(trim(old('education_qualification', $customer->education_qualification))) == strtolower('Below 10th') ? 'selected' : '' }}>
                                                                                                                                                                                                                                        Below 10th
                                                                                                                                                                                                                                    </option> -->

                                                    <option value="10th Pass And Above"
                                                        {{ strtolower(trim(old('education_qualification', $customer->education_qualification))) == strtolower('10th Pass And Above') ? 'selected' : '' }}>
                                                        10th Pass And Above
                                                    </option>

                                                    <option value="7th Pass Or Less"
                                                        {{ strtolower(trim(old('education_qualification', $customer->education_qualification))) == strtolower('7th Pass Or Less') ? 'selected' : '' }}>
                                                        7th Pass Or Less
                                                    </option>

                                                    <option value="Between 8th And 9th Standard"
                                                        {{ strtolower(trim(old('education_qualification', $customer->education_qualification))) == strtolower('Between 8th And 9th Standard') ? 'selected' : '' }}>
                                                        Between 8th And 9th Standard
                                                    </option>

                                                    <option value="Graduate And Above"
                                                        {{ strtolower(trim(old('education_qualification', $customer->education_qualification))) == strtolower('Graduate And Above') ? 'selected' : '' }}>
                                                        Graduate And Above
                                                    </option>
                                                </select>
                                                @error('education_qualification')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="employment_type"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Employment Type
                                                    <span class="text-red-500">*</span></label>
                                                <select id="employment_type" name="employment_type" required
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm">
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
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            {{-- Organisation Name --}}
                                            <div id="organisation_name_div"
                                                class="{{ old('employment_type', $customer->employment_type) == 'Government' ? '' : 'hidden' }}">

                                                <label for="organisation_name"
                                                    class="block text-sm font-medium text-gray-700 mb-1">
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
                                                <label for="nationality"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Nationality <span
                                                        class="text-red-500">*</span></label>
                                                <input type="text" id="nationality" name="nationality"
                                                    value="{{ old('nationality', $customer->nationality) }}" required
                                                    placeholder="Enter nationality"
                                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                                @error('nationality')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="card_number"
                                                    class="block text-sm font-semibold text-gray-900">
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
                                                    Card Amount (Note: 18% GST amount added on card amount).
                                                </label>
                                                <div class="relative group">
                                                    <input type="text" id="amount" name="amount"
                                                        value="{{ old('amount') }}" placeholder="Enter card amount"
                                                        class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                                        transition-all duration-200 placeholder-gray-400">
                                                </div>
                                            </div>

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

                                        {{-- Form Buttons --}}
                                        <div class="pt-5 mt-4 border-t border-gray-200 flex justify-end gap-3">
                                            <button type="submit"
                                                onclick="this.disabled=true; this.innerText='Creating...'; this.classList.add('opacity-50','cursor-not-allowed'); this.form.submit();"
                                                class="btn-primary px-8 py-2">Create An Account</button>
                                        </div>
                                    </form>
                                </div>
                        @endif
                    @elseif(isset($search))
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.33-.22 3.008-1.74 3.008H4.413c-1.52 0-2.49-1.678-1.74-3.008l5.58-9.92zM10 13a1 1 0 100-2 1 1 0 000 2zm-1-4a1 1 0 011-1h.008a1 1 0 110 2H10a1 1 0 01-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        No customer found for
                                        <strong class="font-medium text-yellow-800">
                                            {{ preg_match('/^\d+$/', $search) ? 'mobile number' : 'email' }}:
                                        </strong>
                                        <strong class="font-medium text-yellow-800">
                                            {{ $search }}
                                        </strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function convertCustomerComponent(show = false) {
            return {
                showCustomerForm: show,
            }
        }

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


        function toggleOrganisation() {
            if ($('#employment_type').val() === 'Government') {
                $('#organisation_name_div').removeClass('hidden');
                $('#organisation_name').prop('required', true);
            } else {
                $('#organisation_name_div').addClass('hidden');
                $('#organisation_name').prop('required', false);
                $('#organisation_name').val('');
            }
        }

        $(document).ready(function() {
            toggleOrganisation();
            $('#employment_type').on('change', function() {
                toggleOrganisation();
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

        $(document).ready(function() {

            $('#search').on('input', function() {

                let value = $(this).val().trim();
                let error = $('#search-error');

                error.text('');
                error.addClass('hidden');

                if (value === '') {
                    return;
                }

                if (/^\d+$/.test(value)) {

                    if (value.length > 10) {
                        value = value.substring(0, 10);
                        $(this).val(value);
                    }

                    if (value.length === 10) {

                        if (!/^[6-9][0-9]{9}$/.test(value)) {
                            error
                                .text('Mobile number must be 10 digits and start with 6-9.')
                                .removeClass('hidden');
                        }
                    }

                    return;
                }

                const emailRegex =
                    /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)+$/;

                if (value.includes('@') || value.includes('.')) {

                    if (!emailRegex.test(value)) {
                        error
                            .text('Please enter a valid email address.')
                            .removeClass('hidden');
                    }
                }
            });

        });
    </script>
@endpush
