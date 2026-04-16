@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="py-2 lg:py-8">
    <div class="max-w-3xl mx-auto sm:px-1 lg:px-8">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            {{-- Header Section --}}
            <div class="px-8 py-4 lg:py-6 bg-gray-50 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Edit Customer (#{{ $customer->id }})</h2>
                <p class="text-sm text-gray-600 mt-1">Edit the details of an existing customer</p>
            </div>

            <div class="p-8 p-0">
                <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="space-y-6"
                    novalidate>
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-semibold text-gray-900">
                                First Name
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="first_name" name="first_name"
                                    value="{{ old('first_name', $customer->first_name) }}" required
                                    placeholder="Enter first name"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                        transition-all duration-200 placeholder-gray-400
                                        @error('first_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
                            @error('first_name')
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
                            <label for="last_name" class="block text-sm font-semibold text-gray-900">
                                Last Name
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="last_name" name="last_name"
                                    value="{{ old('last_name', $customer->last_name ?? '') }}" required
                                    placeholder="Enter last name"
                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                        transition-all duration-200 placeholder-gray-400
                                        @error('last_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
                            @error('last_name')
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
                            <label for="mobile_number" class="block text-sm font-semibold text-gray-900">
                                Mobile Number
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="tel" id="mobile_number" name="mobile_number"
                                    value="{{ old('mobile_number', $customer->mobile_number) }}" required
                                    placeholder="Enter mobile number" maxlength="10" inputmode="numeric"
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
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-900">
                                Email Id
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}"
                                    required placeholder="Enter email id"
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
                    </div>

                    {{-- Paid Customer Requirements --}}
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <div class="flex items-center">
                            <input id="is_paid" name="is_paid" type="checkbox" value="1"
                                {{ old('is_paid', $customer->is_paid) ? 'checked' : '' }}
                                class="h-4 w-4 text-primary-blue focus:ring-secondary-blue border-gray-300 rounded"
                                x-data x-ref="isPaidCheckbox" @change="$dispatch('paid-status-change', $el.checked)">
                            <label for="is_paid" class="ml-2 block text-sm font-semibold text-gray-900">Mark as Paid
                                Customer</label>
                        </div>
                    </div>

                    <div x-data="{ isPaid: {{ old('is_paid', $customer->is_paid) ? 'true' : 'false' }} }"
                        @paid-status-change.window="isPaid = $event.detail" x-show="isPaid" x-transition
                        class="space-y-6 border-t border-gray-200 pt-6">
                        <p class="text-sm text-gray-500">Enter additional details for the paid customer:</p>

                        <div class="grid grid-cols-12 md:grid-cols-12 gap-12">
                            <div>
                                <label for="address" class="block text-sm font-semibold text-gray-900">
                                    Address
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <textarea id="address" name="address" :required="isPaid" placeholder="Enter address"
                                        rows="3"
                                        class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('address') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('address', $customer->address ?? '') }}</textarea>
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
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <input type="text" id="pin_code" name="pin_code"
                                        value="{{ old('pin_code', $customer->pin_code) }}" :required="isPaid"
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
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <input type="text" id="city" name="city" value="{{ old('city', $customer->city) }}"
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
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <input type="text" id="state" name="state"
                                        value="{{ old('state', $customer->state) }}" :required="isPaid"
                                        placeholder="Enter state" readonly
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
                                <label for="date_of_birth" class="block text-sm font-semibold text-gray-900">
                                    Date of Birth
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <input type="date" id="date_of_birth" name="date_of_birth"
                                        value="{{ old('date_of_birth', $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '') }}"
                                        :required="isPaid" placeholder="Enter date of birth"
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
                                <label for="place_of_birth" class="block text-sm font-semibold text-gray-900">
                                    Place of Birth
                                    <span class="text-red-500" x-show="isPaid">*</span>
                                </label>
                                <div class="relative group">
                                    <input type="text" id="place_of_birth" name="place_of_birth"
                                        value="{{ old('place_of_birth', $customer->place_of_birth ?? '') }}"
                                        :required="isPaid" placeholder="Enter place of birth"
                                        class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                            hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                            transition-all duration-200 placeholder-gray-400
                                            @error('place_of_birth') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                </div>
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
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-xl shadow-sm text-sm font-medium text-gray-900 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Update Customer
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
                        $('#pincode-error').text(res.message || 'Invalid pincode');
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