@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <h1 class="text-lg md:text-xl font-semibold text-primary-blue">Passport Application Statistics -
                {{ now()->format('d M, Y') }}</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Normal Passport - Customer
                </h2>
                <div class="h-[400px]">
                    <canvas id="normalCustChart"></canvas>
                </div>
            </div>

            <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Normal Passport - Lead
                </h2>
                <div class="h-[400px]">
                    <canvas id="normalLeadChart"></canvas>
                </div>
            </div>

            <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Tatkal Passport - Customer
                </h2>
                <div class="h-[400px]">
                    <canvas id="tatkalCustChart"></canvas>
                </div>
            </div>

            <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Tatkal Passport - Lead
                </h2>
                <div class="h-[400px]">
                    <canvas id="tatkalLeadChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const computedStyle = getComputedStyle(document.documentElement);
                const primaryBlue = computedStyle.getPropertyValue('--primary-blue').trim();
                const secondaryBlue = computedStyle.getPropertyValue('--secondary-blue').trim();
                const accentBlue = computedStyle.getPropertyValue('--accent-blue').trim();
                const textGray = computedStyle.getPropertyValue('--text-gray').trim();
                const borderColor = computedStyle.getPropertyValue('--border-color').trim();

                const defaultColor = '#CCCCCC'; 

                const commonOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: borderColor || defaultColor,
                                drawBorder: false
                            },
                            ticks: {
                                padding: 10,
                                color: textGray || defaultColor,
                                stepSize: 1,
                                precision: 0,
                                callback: function(value) {
                                    return value; 
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                padding: 10,
                                color: textGray || defaultColor
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false 
                        },
                        tooltip: {
                            backgroundColor: primaryBlue || defaultColor,
                            titleColor: 'white', 
                            bodyColor: 'white', 
                            padding: 10,
                            cornerRadius: 4,
                            displayColors: false 
                        }
                    },
                };

                const charts = [{
                        id: 'normalCustChart',
                        label: 'Normal Customers',
                        labels: @json($normalcustlabel),
                        data: @json($normalcustdata)
                    },
                    {
                        id: 'normalLeadChart',
                        label: 'Normal Leads',
                        labels: @json($normalleadlabel),
                        data: @json($normalleaddata)
                    },
                    {
                        id: 'tatkalCustChart',
                        label: 'Tatkal Customers',
                        labels: @json($tatkalcustlabel),
                        data: @json($tatkalcustdata)
                    },
                    {
                        id: 'tatkalLeadChart',
                        label: 'Tatkal Leads',
                        labels: @json($tatkalleadlabel),
                        data: @json($tatkalleaddata)
                    },
                ];

                charts.forEach(chart => {

                    const ctx = document.getElementById(chart.id).getContext('2d');

                    const isTatkal = chart.id.includes('tatkal');

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: chart.labels,
                            datasets: [{
                                label: chart.label,
                                data: chart.data,
                                backgroundColor: isTatkal ? secondaryBlue : primaryBlue,
                                borderRadius: 4
                            }]
                        },
                        options: {
                            ...commonOptions
                        }
                    });

                });
            });
        </script>
    @endpush
@endsection
