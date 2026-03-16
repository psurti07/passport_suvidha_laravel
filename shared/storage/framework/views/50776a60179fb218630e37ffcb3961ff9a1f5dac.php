<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-semibold text-primary-blue">Passport Application Statistics - <?php echo e(now()->format('d M, Y')); ?></h1>
    </div>

    <!-- Statistics Charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
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

<?php $__env->startPush('scripts'); ?>
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
                bodyColor: 'white',  // Assuming white text is desired on the primary color
                padding: 10,
                cornerRadius: 4,
                displayColors: false // Hide color box in tooltip
            }
        },
        // Consider adjusting bar thickness if needed for smaller charts
        // barThickness: 30 
    };

    const chartLabels = ['7-4', '8-4', '9-4', '10-4', '11-4', '12-4', '13-4', '14-4', '15-4', '16-4']; // Example labels

    // Normal Passport 36 Page Chart
    const normal36Ctx = document.getElementById('normal36Chart').getContext('2d');
    new Chart(normal36Ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Normal 36p',
                data: [65, 45, 62, 45, 55, 75, 58, 68, 72, 65], // Example Data
                backgroundColor: primaryBlue || defaultColor,
                borderRadius: 4
            }]
        },
        options: { ...commonOptions } // Spread common options
    });

    // Normal Passport 60 Page Chart
    const normal60Ctx = document.getElementById('normal60Chart').getContext('2d');
    new Chart(normal60Ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Normal 60p',
                data: [30, 25, 35, 20, 28, 40, 33, 38, 42, 35], // Example Data
                backgroundColor: secondaryBlue || defaultColor,
                borderRadius: 4
            }]
        },
        options: { ...commonOptions } 
    });

    // Tatkal Passport 36 Page Chart
    const tatkal36Ctx = document.getElementById('tatkal36Chart').getContext('2d');
    new Chart(tatkal36Ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Tatkal 36p',
                data: [15, 10, 18, 12, 20, 25, 16, 22, 28, 20], // Example Data
                backgroundColor: accentBlue || defaultColor,
                borderRadius: 4
            }]
        },
        options: { ...commonOptions } 
    });

    // Tatkal Passport 60 Page Chart
    const tatkal60Ctx = document.getElementById('tatkal60Chart').getContext('2d');
    new Chart(tatkal60Ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Tatkal 60p',
                data: [8, 5, 10, 6, 12, 15, 9, 13, 16, 11], // Example Data
                backgroundColor: textGray || defaultColor, // Using textGray for this one as before
                borderRadius: 4
            }]
        },
        options: { ...commonOptions } 
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/laravel-app/releases/11/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>