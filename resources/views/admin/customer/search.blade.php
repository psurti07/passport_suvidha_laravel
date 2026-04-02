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
                    <label for="mobile_no"class="block text-gray-700 text-sm font-bold mb-2">Mobile No *</label>
                    <input type="text" name="mobile_no" id="mobile_no" value="{{ old('mobile_no', $mobileNo ?? '') }}"
                        class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('mobile_no') border-red-500 @enderror"
                        title="Mobile number must be 10 digits" placeholder="Enter 10-digit mobile number">
                    @error('mobile_no')
                        <p class="text-red-600 text-sm italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-start pt-2">
                    <button type="submit" class="btn-primary px-6 py-2.5">
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
        @if (isset($mobileNo) || isset($customer))
            <div class="md:col-span-2 bg-white p-8 rounded-lg shadow-lg border border-gray-200">
                <h2 class="text-xl font-semibold mb-6 text-gray-800 border-b pb-3">Search Result</h2>
                <div class="mt-6">
                    @if ($customer)
                        <dl class="divide-y divide-gray-200">
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Module</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold">CUSTOMER</dd>
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Mobile</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ $customer->mobile_number ?? 'N/A' }}</dd> {{-- Assuming mobile_number from controller fix --}}
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Registration Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ $customer->created_at ? $customer->created_at->format('d M Y, H:i A') : 'N/A' }}</dd>
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ $customer->first_name ?? '' }} {{ $customer->last_name ?? 'N/A' }}</dd>
                                {{-- Assuming first_name, last_name exist --}}
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $customer->email ?? 'N/A' }}
                                </dd>
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
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
                            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end">
                                <a href="{{ route('admin.customers.show', $customer->id) }}"
                                    class="btn-primary px-5 py-2">
                                    View Full Details &rarr;
                                </a>
                            </div>
                        @else
                        <div x-data="convertCustomerComponent({{ $errors->any() ? 'true' : 'false' }})">
                            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end">
                                <button type="button" @click="showCustomerForm = !showCustomerForm"
                                    class="btn-primary inline-flex items-center px-4 py-2">
                                    <span x-text="showCustomerForm ? 'Close' : 'Convert to Customer &rarr;'">Convert to Customer &rarr;</span>
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
                                    <div class="grid grid-cols-12 md:grid-cols-12 gap-12">
                                        <div>
                                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                                    class="text-red-500">*</span></label>
                                            <textarea id="address" name="address" 
                                                required placeholder="Enter address" rows="2" 
                                                class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">{{ old('address', $customer->address ?? '') }}</textarea>
                                            @error('address')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                                        <div>
                                            <label for="pin_code" class="block text-sm font-medium text-gray-700 mb-1">Pincode <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" id="pin_code" name="pin_code" 
                                                value="{{ old('pin_code', $customer->pin_code) }}" required placeholder="Enter pincode"
                                                maxlength="6" minlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                            @error('pin_code')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                            <span id="pincode-error" class="text-red-500 text-sm"></span>
                                        </div>
                                        <div>
                                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" id="city" name="city" 
                                                value="{{ old('city', $customer->city) }}" required placeholder="Enter city" readonly
                                                class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                            @error('city')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" id="state" name="state" 
                                                value="{{ old('state', $customer->state) }}" required placeholder="Enter state" readonly
                                                class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                            @error('state')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender <span
                                                    class="text-red-500">*</span></label>
                                            <select id="gender" name="gender" required
                                                class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm">
                                                <option value="" disabled selected>Select gender</option>
                                                <option value="male"
                                                    {{ old('gender', $customer->gender) == 'male' ? 'selected' : '' }}>Male
                                                </option>
                                                <option value="female"
                                                    {{ old('gender', $customer->gender) == 'female' ? 'selected' : '' }}>Female
                                                </option>
                                                <option value="other"
                                                    {{ old('gender', $customer->gender) == 'other' ? 'selected' : '' }}>Other
                                                </option>
                                        
                                                {{-- Add other states --}}
                                            </select>
                                            @error('gender')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of
                                                Birth <span class="text-red-500">*</span></label>
                                            <input type="date" id="date_of_birth" name="date_of_birth" 
                                                value="{{ old('date_of_birth', $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '') }}" required placeholder="Enter date of birth"
                                                class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                            @error('date_of_birth')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="place_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Place of
                                                Birth <span class="text-red-500">*</span></label>
                                            <input type="text" id="place_of_birth" name="place_of_birth" 
                                                value="{{ old('place_of_birth', $customer->place_of_birth ?? '') }}" required placeholder="Enter place of birth"
                                                class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                            @error('place_of_birth')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="nationality" class="block text-sm font-medium text-gray-700 mb-1">Nationality <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" id="nationality" name="nationality" 
                                                value="{{ old('nationality', $customer->nationality) }}" required placeholder="Enter nationality"
                                                class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                            @error('nationality')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="service_code" class="block text-sm font-medium text-gray-700 mb-1">Service Code <span
                                                    class="text-red-500">*</span></label>
                                            <select id="service_code" name="service_code" required
                                                class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm">
                                                <option value="" disabled selected>Select service code</option>
                                                <option value="NORMAL_36"
                                                    {{ old('service_code', $customer->service_code) == 'NORMAL_36' ? 'selected' : '' }}>NORMAL_36
                                                </option>
                                                <option value="NORMAL_60"
                                                    {{ old('service_code', $customer->service_code) == 'NORMAL_60' ? 'selected' : '' }}>NORMAL_60
                                                </option>
                                                <option value="TATKAL_36"
                                                    {{ old('service_code', $customer->service_code) == 'TATKAL_36' ? 'selected' : '' }}>TATKAL_36
                                                </option>
                                                <option value="TATKAL_60"
                                                    {{ old('service_code', $customer->service_code) == 'TATKAL_60' ? 'selected' : '' }}>TATKAL_60
                                                </option>
                                        
                                                {{-- Add other states --}}
                                            </select>
                                            @error('service_code')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="card_number" class="block text-sm font-semibold text-gray-900">
                                                Card Number
                                            </label>
                                            <div class="relative group">
                                                <input type="text" id="card_number" name="card_number"
                                                    value="{{ old('card_number', $cardNumber) }}" placeholder="Enter card number"
                                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                                        transition-all duration-200 placeholder-gray-400">
                                            </div>
                                        </div>
                                        <div>
                                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                                <div class="flex items-center">
                                                    <label class="ml-2 block text-sm font-semibold text-gray-900">Note: 18% GST
                                                        amount added on card amount.</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="amount" class="block text-sm font-semibold text-gray-900">
                                                Card Amount
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
                                                    value="{{ old('payment_id', $paymentId) }}" placeholder="Enter payment id"
                                                    class="peer p-2 pl-3 mt-1 block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm 
                                                        hover:border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50 
                                                        transition-all duration-200 placeholder-gray-400">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Form Buttons --}}
                                    <div class="pt-5 mt-4 border-t border-gray-200 flex justify-end gap-3">
                                        <button type="submit" class="btn-primary px-8 py-2">Create An Account</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @elseif(isset($mobileNo))
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
                                        No customer found for mobile number: <strong
                                            class="font-medium text-yellow-800">{{ $mobileNo }}</strong>
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

    $(document).ready(function () {

        $('#pin_code').on('input', function () {

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

            debounceTimer = setTimeout(function () {

                $.ajax({
                    url: "{{ route('admin.pincode.location') }}",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        pincode: pincode
                    },
                    beforeSend: function () {
                        $('#city').val('Loading...');
                        $('#state').val('Loading...');
                    },
                    success: function (res) {
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
                    error: function (xhr) {
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