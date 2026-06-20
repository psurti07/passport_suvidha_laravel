@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-lg md:text-xl font-semibold text-primary-blue">Passport Application Statistics -
                {{ now()->format('d M, Y') }}</h1>
        </div>

        <!-- Statistics Charts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Normal Passport Customer Registrations -->
            <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Normal Passport - Customer
                </h2>
                <div class="h-[400px]">
                    <canvas id="normalCustChart"></canvas>
                </div>
            </div>

            <!-- Normal Passport Lead Registrations -->
            <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Normal Passport - Lead
                </h2>
                <div class="h-[400px]">
                    <canvas id="normalLeadChart"></canvas>
                </div>
            </div>

            <!-- Tatkal Passport Customer Registrations -->
            <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Tatkal Passport - Customer
                </h2>
                <div class="h-[400px]">
                    <canvas id="tatkalCustChart"></canvas>
                </div>
            </div>

            <!-- Tatkal Passport Lead Registrations -->
            <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Tatkal Passport - Lead
                </h2>
                <div class="h-[400px]">
                    <canvas id="tatkalLeadChart"></canvas>
                </div>
            </div>

            <!-- Normal Passport 36 Page Customer -->
            <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Normal Pages - Customer</h2>
                <div class="h-[400px]">
                    <canvas id="normalPageCustChart"></canvas>
                </div>
            </div>

            <!-- Normal Passport 36 Page Lead -->
            <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Normal Pages - Lead</h2>
                <div class="h-[400px]">
                    <canvas id="normalPageLeadChart"></canvas>
                </div>
            </div>

            <!-- Tatkal Passport 36 Page Customer -->
            <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Tatkal Pages - Customer</h2>
                <div class="h-[400px]">
                    <canvas id="tatkalPageCustChart"></canvas>
                </div>
            </div>

            <!-- Tatkal Passport 36 Page Lead -->
            <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Tatkal Pages - Lead</h2>
                <div class="h-[400px]">
                    <canvas id="tatkalPageLeadChart"></canvas>
                </div>
            </div>

            <!-- Normal Passport 60 Page Customer -->
            {{-- <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Normal 60 Pages - Customer</h2>
                <div class="h-[400px]">
                    <canvas id="normal60CustChart"></canvas>
                </div>
            </div> --}}

            <!-- Normal Passport 60 Page Lead -->
            {{-- <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Normal 60 Pages - Lead</h2>
                <div class="h-[400px]">
                    <canvas id="normal60LeadChart"></canvas>
                </div>
            </div> --}}

            <!-- Tatkal Passport 60 Page Customer -->
            {{-- <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Tatkal 60 Pages - Customer</h2>
                <div class="h-[400px]">
                    <canvas id="tatkal60CustChart"></canvas>
                </div>
            </div> --}}

            <!-- Tatkal Passport 60 Page Lead -->
            {{-- <div class="card p-6 bg-white rounded-lg">
                <h2 class="text-md md:text-lg font-medium text-primary-blue mb-6">Tatkal 60 Pages - Lead</h2>
                <div class="h-[400px]">
                    <canvas id="tatkal60LeadChart"></canvas>
                </div>
            </div> --}}
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get computed styles
                const computedStyle = getComputedStyle(document.documentElement);
                const primaryBlue = computedStyle.getPropertyValue('--primary-blue').trim();
                const secondaryBlue = computedStyle.getPropertyValue('--secondary-blue').trim();
                const accentBlue = computedStyle.getPropertyValue('--accent-blue').trim();
                const textGray = computedStyle.getPropertyValue('--text-gray').trim();
                const borderColor = computedStyle.getPropertyValue('--border-color').trim();

                // Ensure default values if variables are not defined
                const defaultColor = '#CCCCCC'; // A neutral default color

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
                                    return value; // no decimal formatting
                                }
                                // Optional: format ticks if numbers get large
                                // callback: function(value) {
                                //     if (value >= 1000) {
                                //         return (value / 1000) + 'k';
                                //     }
                                //     return value;
                                // }
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
                            display: false // Keep legend hidden
                        },
                        tooltip: {
                            backgroundColor: primaryBlue || defaultColor,
                            titleColor: 'white', // Assuming white text is desired on the primary color
                            bodyColor: 'white', // Assuming white text is desired on the primary color
                            padding: 10,
                            cornerRadius: 4,
                            displayColors: false // Hide color box in tooltip
                        }
                    },
                    // Consider adjusting bar thickness if needed for smaller charts
                    // barThickness: 30 
                };

                // Chart Data from Controller
                const charts = [{
                        id: 'normalCustChart',
                        label: 'Normal Registrations',
                        labels: @json($normalcustlabel),
                        data: @json($normalcustdata)
                    },
                    {
                        id: 'normalLeadChart',
                        label: 'Normal Registrations',
                        labels: @json($normalleadlabel),
                        data: @json($normalleaddata)
                    },
                    {
                        id: 'normalPageCustChart',
                        label: 'Normal page',
                        labels: @json($normalpagecustlabel),
                        data: @json($normalpagecustdata)
                    },
                    {
                        id: 'normalPageLeadChart',
                        label: 'Normal page',
                        labels: @json($normalpageleadlabel),
                        data: @json($normalpageleaddata)
                    },
                    {
                        id: 'tatkalCustChart',
                        label: 'Tatkal Registrations',
                        labels: @json($tatkalcustlabel),
                        data: @json($tatkalcustdata)
                    },
                    {
                        id: 'tatkalLeadChart',
                        label: 'Tatkal Registrations',
                        labels: @json($tatkalleadlabel),
                        data: @json($tatkalleaddata)
                    },
                    {
                        id: 'tatkalPageCustChart',
                        label: 'Tatkal page',
                        labels: @json($tatkalpagecustlabel),
                        data: @json($tatkalpageleaddata)
                    },
                    {
                        id: 'tatkalPageLeadChart',
                        label: 'Tatkal page',
                        labels: @json($tatkalpageleadlabel),
                        data: @json($tatkalpageleaddata)
                    },
                ];

                // Create Charts
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
