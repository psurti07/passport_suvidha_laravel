@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Create New Customer</h2>
                <p class="text-sm text-gray-500 mt-1">Add a new customer (lead or paid) to the system</p>
            </div>

            <form method="POST" action="{{ route('admin.customers.store') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
                        <input id="first_name" 
                               type="text" 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-blue focus:ring focus:ring-primary-blue focus:ring-opacity-20 transition-shadow @error('first_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                               name="first_name" 
                               value="{{ old('first_name') }}" 
                               required 
                               placeholder="Enter first name"
                               autofocus>
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
                        <input id="last_name" 
                               type="text" 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-blue focus:ring focus:ring-primary-blue focus:ring-opacity-20 transition-shadow @error('last_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                               name="last_name" 
                               value="{{ old('last_name') }}" 
                               required 
                               placeholder="Enter last name">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
                        <input id="email" 
                               type="email" 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-blue focus:ring focus:ring-primary-blue focus:ring-opacity-20 transition-shadow @error('email') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               placeholder="Enter email address">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="mobile_number" class="block text-sm font-medium text-gray-700">Mobile Number <span class="text-red-500">*</span></label>
                        <input id="mobile_number" 
                               type="text" 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-blue focus:ring focus:ring-primary-blue focus:ring-opacity-20 transition-shadow @error('mobile_number') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                               name="mobile_number" 
                               value="{{ old('mobile_number') }}" 
                               required 
                               placeholder="Enter mobile number">
                        @error('mobile_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="is_paid" name="is_paid" type="checkbox" value="1" {{ old('is_paid') ? 'checked' : '' }} 
                           class="h-4 w-4 text-primary-blue focus:ring-secondary-blue border-gray-300 rounded"
                           x-data x-ref="isPaidCheckbox" @change="$dispatch('paid-status-change', $el.checked)">
                    <label for="is_paid" class="ml-2 block text-sm text-gray-900">Mark as Paid Customer</label>
                </div>

                <div x-data="{ isPaid: {{ old('is_paid') ? 'true' : 'false' }} }" @paid-status-change.window="isPaid = $event.detail" x-show="isPaid" x-transition class="space-y-6 border-t border-gray-200 pt-6">
                    <p class="text-sm text-gray-500">Enter additional details for the paid customer:</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label for="pack_code" class="block text-sm font-medium text-gray-700">Pack Code <span class="text-red-500" x-show="isPaid">*</span></label>
                            <input id="pack_code" 
                                   type="text" 
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-blue focus:ring focus:ring-primary-blue focus:ring-opacity-20 transition-shadow @error('pack_code') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                   name="pack_code" 
                                   value="{{ old('pack_code') }}" 
                                   :required="isPaid" 
                                   placeholder="Enter pack code">
                            @error('pack_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="service_code" class="block text-sm font-medium text-gray-700">Service Code <span class="text-red-500" x-show="isPaid">*</span></label>
                            <input id="service_code" 
                                   type="text" 
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-blue focus:ring focus:ring-primary-blue focus:ring-opacity-20 transition-shadow @error('service_code') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                   name="service_code" 
                                   value="{{ old('service_code') }}" 
                                   :required="isPaid" 
                                   placeholder="Enter service code">
                            @error('service_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="address" class="block text-sm font-medium text-gray-700">Address <span class="text-red-500" x-show="isPaid">*</span></label>
                        <textarea id="address" 
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-blue focus:ring focus:ring-primary-blue focus:ring-opacity-20 transition-shadow @error('address') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                name="address" 
                                rows="3" 
                                :required="isPaid" 
                                placeholder="Enter full address">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label for="gender" class="block text-sm font-medium text-gray-700">Gender <span class="text-red-500" x-show="isPaid">*</span></label>
                            <select id="gender" 
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-blue focus:ring focus:ring-primary-blue focus:ring-opacity-20 transition-shadow @error('gender') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror" 
                                    name="gender" 
                                    :required="isPaid">
                                <option value="">Select gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth <span class="text-red-500" x-show="isPaid">*</span></label>
                            <input id="date_of_birth" 
                                   type="date" 
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-blue focus:ring focus:ring-primary-blue focus:ring-opacity-20 transition-shadow @error('date_of_birth') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                   name="date_of_birth" 
                                   value="{{ old('date_of_birth') }}" 
                                   :required="isPaid">
                            @error('date_of_birth')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label for="place_of_birth" class="block text-sm font-medium text-gray-700">Place of Birth <span class="text-red-500" x-show="isPaid">*</span></label>
                            <input id="place_of_birth" 
                                   type="text" 
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-blue focus:ring focus:ring-primary-blue focus:ring-opacity-20 transition-shadow @error('place_of_birth') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                   name="place_of_birth" 
                                   value="{{ old('place_of_birth') }}" 
                                   :required="isPaid" 
                                   placeholder="Enter place of birth">
                            @error('place_of_birth')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="nationality" class="block text-sm font-medium text-gray-700">Nationality <span class="text-red-500" x-show="isPaid">*</span></label>
                            <input id="nationality" 
                                   type="text" 
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-blue focus:ring focus:ring-primary-blue focus:ring-opacity-20 transition-shadow @error('nationality') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                   name="nationality" 
                                   value="{{ old('nationality') }}" 
                                   :required="isPaid" 
                                   placeholder="Enter nationality">
                            @error('nationality')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="payment_info_id" class="block text-sm font-medium text-gray-700">Payment Information ID <span class="text-red-500" x-show="isPaid">*</span></label>
                        <input id="payment_info_id" 
                               type="number" 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-blue focus:ring focus:ring-primary-blue focus:ring-opacity-20 transition-shadow @error('payment_info_id') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                               name="payment_info_id" 
                               value="{{ old('payment_info_id') }}" 
                               :required="isPaid" 
                               placeholder="Enter payment information ID">
                        @error('payment_info_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.customers.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-blue transition-colors">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-blue hover:bg-secondary-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-blue transition-colors">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
@endpush 