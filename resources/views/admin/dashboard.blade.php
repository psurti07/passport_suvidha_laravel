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
        <!-- Normal Passport Customer Registrations -->
        <div class="card p-6">
            <h2 class="text-lg font-medium text-primary-blue mb-6">Normal Passport - Customer Registrations</h2>
            <div class="h-[400px]">
                <canvas id="normalCustChart"></canvas>
            </div>
        </div>

        <!-- Normal Passport Customer Leads -->
        <div class="card p-6">
            <h2 class="text-lg font-medium text-primary-blue mb-6">Normal Passport - Customer Leads</h2>
            <div class="h-[400px]">
                <canvas id="normalLeadChart"></canvas>
            </div>
        </div>

        <!-- Normal Passport 36 Page -->
        <div class="card p-6">
            <h2 class="text-lg font-medium text-primary-blue mb-6">Normal Passport - 36 Pages</h2>
            <div class="h-[400px]">
                <canvas id="normal36Chart"></canvas>
            </div>
        </div>

        <!-- Normal Passport 60 Page -->
        <div class="card p-6">
            <h2 class="text-lg font-medium text-primary-blue mb-6">Normal Passport - 60 Pages</h2>
            <div class="h-[400px]">
                <canvas id="normal60Chart"></canvas>
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
                    callback: function(value) {
                        if (value >= 1000) {
                            return (value / 1000) + 'k';
                        }
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
            label: 'Normal Leads',
            labels: @json($normalleadlabel),
            data: @json($normalleaddata)
        },
        {
            id: 'normal36Chart',
            label: 'Normal 36p',
            labels: @json($normal36plabel),
            data: @json($normal36pdata)
        },
        {
            id: 'normal60Chart',
            label: 'Normal 60p',
            labels: @json($normal60plabel),
            data: @json($normal60pdata)
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
                    backgroundColor: primaryBlue,
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