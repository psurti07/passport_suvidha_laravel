@extends('layouts.app')

@section('title', 'Sms Log')

@section('content')

<div class="mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="flex justify-between items-center mb-6">
                <h2
                    class="text-xl sm:text-2xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                    View Sms Log
                </h2>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.remarketing-logs.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>

            <div class="mt-8 border border-gray-100 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 border-b border-gray-100 flex items-center">
                    <span
                        class="bg-gradient-to-r from-blue-600 to-blue-800 text-white text-lg font-semibold px-4 py-2 rounded-lg shadow-sm">
                        #{{ $smsLog->id }}
                    </span>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500">Created:
                            {{ $smsLog->created_at }}
                        </p>
                        <p class="text-xs text-gray-500">Last Updated:
                            {{ $smsLog->updated_at }}</p>
                    </div>
                </div>

                <div class="bg-white p-4">
                    <div class="mb-6">
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700">Type</h3>
                        </div>
                        <div class="ml-7">
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                @if($smsLog->type == 'sms')
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $smsLog->type ? 'bg-blue-100 text-blue-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ 'SMS' }}
                                </span>
                                @elseif($smsLog->type == 'aisensy')
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $smsLog->type ? 'bg-green-100 text-green-800' : 'bg-green-100 text-green-800' }}">
                                    {{ 'Aisensy' }}
                                </span>
                                @elseif($smsLog->type == 'interakt')
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $smsLog->type ? 'bg-yellow-100 text-yellow-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ 'Interakt' }}
                                </span>
                                @elseif($smsLog->type == 'rcs')
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $smsLog->type ? 'bg-red-100 text-red-800' : 'bg-red-100 text-red-800' }}">
                                    {{ 'RCS' }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mb-6">
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.3-3.9A7.944 7.944 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700">Message</h3>
                        </div>
                        <div class="ml-7">
                            <p class="text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                {{ $smsLog->crontype }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700">Name</h3>
                        </div>
                        <div class="ml-7">
                            <p class="text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                {{ $smsLog->cronname }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-6m4 6V7m4 10V4M5 20h14" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700">Count</h3>
                        </div>
                        <div class="ml-7">
                            <p class="text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                {{ $smsLog->msgcount }}</p>
                        </div>  
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700">Response</h3>
                        </div>
                        <div class="ml-7">
                            <div
                                class="bg-gray-50 p-4 rounded-lg border border-gray-100 whitespace-pre-wrap text-gray-800 text-sm">{{ $smsLog->msgresponse }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection