{{-- resources/views/admin/site-options.blade.php --}}

@extends('layouts.app')

@section('title', 'Site Settings')

@section('content')
<div class="mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-4 sm:p-6 lg:p-8">

            {{-- Header --}}
            <div class="flex justify-between items-center mb-6">
                <h2
                    class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                    Site Settings
                </h2>
            </div>

            {{-- ================= SMS ================= --}}
            <div class="border border-gray-200 rounded-lg p-5 mt-6 mb-6 bg-gray-50">
                <h3 class="font-semibold mb-4">SMS Settings</h3>

                @php
                $sms = ['sms-sender-id' => 'SMS Sender ID'];
                @endphp

                @foreach($sms as $key => $label)
                <form method="POST" action="{{ route('admin.site-options.update') }}" class="mb-4">
                    @csrf
                    <input type="hidden" name="option_key" value="{{ $key }}">
                    <input type="hidden" name="option_label" value="{{ $label }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <div>
                            <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
                        </div>

                        <div class="md:col-span-2 flex gap-3">
                            <input type="text" name="option_value" value="{{ getOption($key) }}"
                                placeholder="Enter {{ $label }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">

                            <button type="submit"
                                onclick="this.disabled=true; this.innerText='Updating...'; this.form.submit();" class="h-full px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white 
                                rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 
                                transition-all duration-200 shadow-md hover:shadow-lg whitespace-nowrap">
                                Update
                            </button>
                        </div>
                    </div>
                </form>
                @endforeach
            </div>

            {{-- ================= FACEBOOK ================= --}}
            <div class="border border-gray-200 rounded-lg p-5 mt-6 mb-6 bg-gray-50">
                <h3 class="font-semibold mb-4">Facebook Settings</h3>

                @php
                $facebook = [
                'facebook-domain-verification-id' => 'Facebook Domain Verification Id',
                'facebook-pixel-key' => 'Facebook Pixel Key',
                'facebook-access-token' => 'Facebook Access Token',
                'facebook-event-name' => 'Facebook Event Name',
                'facebook-event-id' => 'Facebook Event ID'
                ];
                @endphp

                @foreach($facebook as $key => $label)
                <form method="POST" action="{{ route('admin.site-options.update') }}" class="mb-4">
                    @csrf
                    <input type="hidden" name="option_key" value="{{ $key }}">
                    <input type="hidden" name="option_label" value="{{ $label }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <div>
                            <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
                        </div>

                        <div class="md:col-span-2 flex gap-3">
                            <input type="text" name="option_value" value="{{ getOption($key) }}"
                                placeholder="Enter {{ $label }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">

                            <button type="submit"
                                onclick="this.disabled=true; this.innerText='Updating...'; this.form.submit();" class="h-full px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white 
                                rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 
                                transition-all duration-200 shadow-md hover:shadow-lg whitespace-nowrap">
                                Update
                            </button>
                        </div>
                    </div>
                </form>
                @endforeach
            </div>

            {{-- ================= WHATSAPP ================= --}}
            <div class="border border-gray-200 rounded-lg p-5 mt-6 mb-6 bg-gray-50">
                <h3 class="font-semibold mb-4">Whatsapp Settings</h3>

                @php
                $whatsapp = [
                'whatsapp-remarketing-campaign' => 'Whatsapp Remarketing',
                'whatsapp-get-offer-campaign' => 'Whatsapp Get Offer',
                'whatsapp-payment-success-campaign' => 'Whatsapp Payment Success',
                'whatsapp-username-password-campaign' => 'Whatsapp Username Password'
                ];
                @endphp

                @foreach($whatsapp as $key => $label)
                <form method="POST" action="{{ route('admin.site-options.update') }}" class="mb-4">
                    @csrf
                    <input type="hidden" name="option_key" value="{{ $key }}">
                    <input type="hidden" name="option_label" value="{{ $label }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <div>
                            <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
                        </div>

                        <div class="md:col-span-2 flex gap-3">
                            <input type="text" name="option_value" value="{{ getOption($key) }}"
                                placeholder="Enter {{ $label }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">

                            <button type="submit"
                                onclick="this.disabled=true; this.innerText='Updating...'; this.form.submit();" class="h-full px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white 
                                rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 
                                transition-all duration-200 shadow-md hover:shadow-lg whitespace-nowrap">
                                Update
                            </button>
                        </div>
                    </div>
                </form>
                @endforeach
            </div>

            {{-- ================= CUSTOMER MESSAGE ================= --}}
            <div class="border border-gray-200 rounded-lg p-5 mt-6 mb-6 bg-gray-50">
                <h3 class="font-semibold mb-4">Customer Message</h3>

                @php
                $key = 'customer-message';
                $label = 'Customer Message';
                @endphp

                <form method="POST" action="{{ route('admin.site-options.update') }}">
                    @csrf
                    <input type="hidden" name="option_key" value="{{ $key }}">
                    <input type="hidden" name="option_label" value="{{ $label }}">

                    <textarea name="option_value" rows="3" placeholder="Enter {{ $label }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">{{ getOption($key) }}</textarea>

                    <button type="submit"
                        onclick="this.disabled=true; this.innerText='Updating...'; this.form.submit();" class="h-full px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white 
                                rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 
                                transition-all duration-200 shadow-md hover:shadow-lg whitespace-nowrap">
                        Update
                    </button>
                </form>
            </div>

            {{-- ================= WELCOME MESSAGE ================= --}}
            <div class="border border-gray-200 rounded-lg p-5 mt-6 mb-6 bg-gray-50">
                <h3 class="font-semibold mb-4">Welcome Message</h3>

                @php
                $key = 'welcome-message';
                $label = 'Welcome Message';
                @endphp

                <form method="POST" action="{{ route('admin.site-options.update') }}">
                    @csrf
                    <input type="hidden" name="option_key" value="{{ $key }}">
                    <input type="hidden" name="option_label" value="{{ $label }}">

                    <textarea name="option_value" rows="3" placeholder="Enter {{ $label }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">{{ getOption($key) }}</textarea>

                    <button type="submit"
                        onclick="this.disabled=true; this.innerText='Updating...'; this.form.submit();" class="h-full px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white 
                                rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 
                                transition-all duration-200 shadow-md hover:shadow-lg whitespace-nowrap">
                        Update
                    </button>
                </form>
            </div>

            <!-- @php
            $options = [
            'sms-sender-id' => 'SMS Sender ID',
            'facebook-domain-verification-id' => 'Facebook Domain Verification Id',
            'facebook-pixel-key' => 'Facebook Pixel Key',
            'facebook-access-token' => 'Facebook Access Token',
            'facebook-event-name' => 'Facebook Event Name',
            'facebook-event-id' => 'Facebook Event ID',
            'whatsapp-remarketing-campaign' => 'Whatsapp Remarketing',
            'whatsapp-get-offer-campaign' => 'Whatsapp Get Offer',
            'whatsapp-payment-success-campaign' => 'Whatsapp Payment Success',
            'whatsapp-username-password-campaign' => 'Whatsapp Username Password',
            'customer-message' => 'Customer Message',
            'welcome-message' => 'Welcome Message'
            ];
            @endphp

            <div class="space-y-6">

                @foreach($options as $key => $label)
                <form method="POST" action="{{ route('admin.site-options.update') }}">
                    @csrf

                    <input type="hidden" name="option_key" value="{{ $key }}">
                    <input type="hidden" name="option_label" value="{{ $label }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">

                        {{-- Label --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700">
                                {{ $label }}
                            </label>
                        </div>

                        {{-- Input --}}
                        <div class="md:col-span-2 flex gap-3">

                            @if(in_array($key, ['customer-message', 'welcome-message']))

                            {{-- TEXTAREA --}}
                            <textarea name="option_value" rows="3" placeholder="Enter {{ $label }}"
                                class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">{{ getOption($key) }}</textarea>

                            @else

                            {{-- INPUT --}}
                            <input type="text" name="option_value" value="{{ getOption($key) }}"
                                placeholder="Enter {{ $label }}"
                                class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">

                            @endif

                            {{-- Button --}}
                            <button type="submit"
                                onclick="this.disabled=true; this.innerText='Updating...'; this.form.submit();" class="h-full px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white 
                                rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 
                                transition-all duration-200 shadow-md hover:shadow-lg whitespace-nowrap">
                                Update
                            </button>
                        </div>

                    </div>
                </form>
                @endforeach

            </div> -->

        </div>
    </div>
</div>
@endsection