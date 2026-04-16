@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-semibold text-primary-blue">Passport Application Statistics -
            {{ now()->format('d M, Y') }}</h1>
    </div>

    <!-- Statistics Charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Tatkal Passport Customer Registrations -->
        <div class="card p-6">
            <h2 class="text-lg font-medium text-primary-blue mb-6">Tatkal Passport - Customer Registrations</h2>
            <div class="h-[400px]">
                <canvas id="tatkalCustChart"></canvas>
            </div>
        </div>

        <!-- Tatkal Passport Customer Leads -->
        <div class="card p-6">
            <h2 class="text-lg font-medium text-primary-blue mb-6">Tatkal Passport - Customer Leads</h2>
            <div class="h-[400px]">
                <canvas id="tatkalLeadChart"></canvas>
            </div>
        </div>

        <!-- Tatkal Passport 36 Page -->
        <div class="card p-6">
            <h2 class="text-lg font-medium text-primary-blue mb-6">Tatkal Passport - 36 Pages</h2>
            <div class="h-[400px]">
                <canvas id="tatkal36Chart"></canvas>
            </div>
        </div>

        <!-- Tatkal Passport 60 Page -->
        <div class="card p-6">
            <h2 class="text-lg font-medium text-primary-blue mb-6">Tatkal Passport - 60 Pages</h2>
            <div class="h-[400px]">
                <canvas id="tatkal60Chart"></canvas>
            </div>
        </div>

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
            id: 'tatkalCustChart',
            label: 'Tatkal Registrations',
            labels: @json($tatkalcustlabel),
            data: @json($tatkalcustdata)
        },
        {
            id: 'tatkalLeadChart',
            label: 'Tatkal Leads',
            labels: @json($tatkalleadlabel),
            data: @json($tatkalleaddata)
        },
        {
            id: 'tatkal36Chart',
            label: 'Tatkal 36p',
            labels: @json($tatkal36plabel),
            data: @json($tatkal36pdata)
        },
        {
            id: 'tatkal60Chart',
            label: 'Tatkal 60p',
            labels: @json($tatkal60plabel),
            data: @json($tatkal60pdata)
        }
    ];

    // Create Charts
    charts.forEach(chart => {

        const ctx = document.getElementById(chart.id).getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chart.labels,
                datasets: [{
                    label: chart.label,
                    data: chart.data,
                    backgroundColor: secondaryBlue,
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