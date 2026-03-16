@extends('layouts.app')

@section('title', 'Today\'s Statistics')

@section('content')

<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 bg-white rounded-lg shadow-md my-8">
    <h1 class="text-center text-2xl font-semibold text-blue-900 mb-8">Today's Statistics - {{ $currentDate }}</h1>

    <h2 class="mt-8 mb-4 text-xl font-medium text-blue-900 border-b border-gray-200 pb-2">Today's Statistics</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($todayStats as $stat)
            {{-- Added 'group' class for hover effect on icon --}}
            <div class="group bg-white border border-gray-200 rounded-lg p-5 flex justify-between items-center shadow-sm transition duration-200 ease-in-out hover:shadow-lg hover:-translate-y-1">
                <div class="info">
                    <div class="text-3xl font-semibold text-blue-900 mb-1">{{ $stat['count'] }}</div>
                    <div class="text-sm font-medium text-gray-600">{{ $stat['label'] }}</div>
                </div>
                {{-- Adjusted icon size and color, added group-hover effect --}}
                <div class="icon text-4xl text-blue-700 opacity-80 group-hover:opacity-100 transition duration-200 ease-in-out group-hover:scale-105">
                    <i class="fas {{ $stat['icon'] }}"></i>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Placeholder for Payment & Offers Statistics (Apply similar Tailwind classes if uncommented) --}}
    {{-- <h2 class="mt-8 mb-4 text-xl font-medium text-blue-900 border-b border-gray-200 pb-2">Payment & Offers Statistics</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($paymentStats as $stat)
            <div class="group bg-white border border-gray-200 rounded-lg p-5 flex justify-between items-center shadow-sm transition duration-200 ease-in-out hover:shadow-lg hover:-translate-y-1">
                <div class="info">
                    <div class="text-3xl font-semibold text-blue-900 mb-1">{{ $stat['count'] }}</div>
                    <div class="text-sm font-medium text-gray-600">{{ $stat['label'] }}</div>
                </div>
                <div class="icon text-4xl text-blue-700 opacity-80 group-hover:opacity-100 transition duration-200 ease-in-out group-hover:scale-105">
                    <i class="fas {{ $stat['icon'] }}"></i>
                </div>
            </div>
        @endforeach
    </div> --}}

</div>
@endsection