@extends('layouts.app')

@section('title', 'Today\'s Registration Process')

@section('content')

    <div class="max-w-8xl mx-auto py-6 px-4 sm:px-6 lg:px-8 bg-white rounded-lg shadow-md my-8">

        {{-- Page Title --}}
        <h1 class="text-center text-xl md:text-2xl font-semibold text-blue-900 mb-8">
            Today's Registration Process - {{ $currentDate }}
        </h1>


        {{-- Registration Process Statistics --}}
        <h2 class="mt-8 mb-4 text-md md:text-lg font-medium text-blue-900 border-b border-gray-200 pb-2">
            Customer Registration Steps
        </h2>


        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach ($processStats as $stat)
                <div
                    class="group bg-white border border-gray-200 rounded-lg p-5
                    flex justify-between items-center shadow-sm
                    transition duration-200 ease-in-out
                    hover:shadow-lg hover:-translate-y-1">

                    {{-- Count & Label --}}
                    <div class="info">

                        <div class="text-xl md:text-3xl font-semibold text-blue-900 mb-1">
                            {{ $stat['count'] }}
                        </div>

                        <div class="text-sm font-medium text-gray-600">
                            Step {{ $stat['step'] }} - {{ $stat['label'] }}
                        </div>

                    </div>


                    {{-- Icon --}}
                    <div
                        class="icon text-4xl text-blue-700 opacity-80
                        group-hover:opacity-100
                        transition duration-200 ease-in-out
                        group-hover:scale-105">

                        <i class="fas {{ $stat['icon'] }}"></i>

                    </div>

                </div>
            @endforeach

        </div>

    </div>

@endsection
