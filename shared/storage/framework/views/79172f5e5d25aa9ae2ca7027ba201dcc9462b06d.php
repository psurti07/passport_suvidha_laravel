<?php $__env->startSection('title', 'Appointment Letters'); ?>

<?php $__env->startSection('content'); ?>
    
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    
    <style>
        .toastify {
            background: none !important;
            padding: 0 !important;
            box-shadow: none !important;
            opacity: 0;
            transform: translateX(100%);
            animation: slideIn 0.3s ease forwards;
        }

        .toastify.toastify-right {
            right: 16px;
        }

        @keyframes  slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .toast-content {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
    </style>

    <div class="mx-auto">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4 sm:p-6 lg:p-8">
                <form id="filterForm" action="<?php echo e(route('admin.appointment-letters.index')); ?>" method="GET">
                    <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
                        <div class="flex items-center gap-4">
                            <h2
                                class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                                APPOINTMENT LETTERS
                            </h2>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <div class="flex items-center gap-2">
                                <label for="start_date" class="text-sm font-medium text-gray-700">From:</label>
                                <input type="date" id="start_date" name="start_date" value="<?php echo e(request('start_date')); ?>"
                                    class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm w-32">
                            </div>

                            <div class="flex items-center gap-2">
                                <label for="end_date" class="text-sm font-medium text-gray-700">To:</label>
                                <input type="date" id="end_date" name="end_date" value="<?php echo e(request('end_date')); ?>"
                                    class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm w-32">
                            </div>

                            <div class="flex items-center gap-2 ml-2">
                                <label for="searchInput" class="text-sm font-medium text-gray-700">Search:</label>
                                <input type="text" id="searchInput" name="search" value="<?php echo e(request('search')); ?>"
                                    placeholder="Search by mobile number"
                                    class="border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm w-52"
                                    onkeypress="handleSearchKeyPress(event)">
                            </div>

                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filter
                            </button>

                            <?php if(request('search') || request('start_date') || request('end_date')): ?>
                                <a href="<?php echo e(route('admin.appointment-letters.index')); ?>"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 bg-white rounded-lg text-sm font-medium shadow-sm hover:bg-gray-50 transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>

                <script>
                    function showToast(message, type = 'success') {
                        let bgColorClass, textColorClass, iconSvg, borderColorClass;

                        switch (type) {
                            case 'success':
                                bgColorClass = 'bg-emerald-500/95';
                                textColorClass = 'text-white';
                                borderColorClass = 'border-emerald-400';
                                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>`;
                                break;
                            case 'error':
                                bgColorClass = 'bg-red-500/95';
                                textColorClass = 'text-white';
                                borderColorClass = 'border-red-400';
                                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>`;
                                break;
                            case 'warning':
                                bgColorClass = 'bg-amber-500/95';
                                textColorClass = 'text-white';
                                borderColorClass = 'border-amber-400';
                                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                </svg>`;
                                break;
                            default: // Info
                                bgColorClass = 'bg-blue-500/95';
                                textColorClass = 'text-white';
                                borderColorClass = 'border-blue-400';
                                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>`;
                        }

                        const toastNode = document.createElement('div');
                        toastNode.className =
                            `toast-content flex items-center w-full max-w-sm p-4 mb-4 ${bgColorClass} ${textColorClass} rounded-lg shadow-2xl border ${borderColorClass} backdrop-blur`;
                        toastNode.innerHTML = `
                            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${textColorClass}">
                                ${iconSvg}
                            </div>
                            <div class="ml-3 text-sm font-normal">${message}</div>
                            <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8 ${textColorClass} hover:${bgColorClass.replace('/95', '')} focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </button>`;

                        const closeButton = toastNode.querySelector('button');
                        closeButton.addEventListener('click', function() {
                            const toast = this.closest('.toastify');
                            if (toast) {
                                toast.style.opacity = '0';
                                toast.style.transform = 'translateX(100%)';
                                setTimeout(() => toast.remove(), 300);
                            }
                        });

                        Toastify({
                            node: toastNode,
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            stopOnFocus: true,
                        }).showToast();
                    }

                    function handleSearchKeyPress(event) {
                        if (event.key === 'Enter' || event.keyCode === 13) {
                            event.preventDefault();
                            document.getElementById('filterForm').submit();
                        }
                    }

                    function confirmDelete(id) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "This appointment letter will be permanently deleted.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, delete it!',
                            customClass: {
                                popup: 'rounded-lg shadow-lg',
                                title: 'text-lg font-semibold text-gray-800',
                                confirmButton: 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 mx-1',
                                cancelButton: 'px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 mx-1'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const form = document.getElementById('delete-form');
                                form.action = `/admin/appointment-letters/${id}`;
                                form.submit();
                            }
                        });
                    }

                    document.addEventListener('DOMContentLoaded', function() {
                        // Check for flashed session messages and show toasts
                        <?php if(session('success')): ?>
                            showToast("<?php echo e(session('success')); ?>", 'success');
                        <?php endif; ?>
                        <?php if(session('error')): ?>
                            showToast("<?php echo e(session('error')); ?>", 'error');
                        <?php endif; ?>
                    });
                </script>

                <div class="mt-8 overflow-x-auto">
                    <div class="inline-block min-w-full align-middle max-h-[60vh] overflow-y-auto">
                        <div class="shadow-sm ring-1 ring-black ring-opacity-5">
                            <table class="min-w-full divide-y divide-gray-200 relative">
                                <thead class="bg-blue-50">
                                    <tr>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            Customer
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            Mobile
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            Appointment Date & Time
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            Upload Date
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            Uploaded By
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <?php $__empty_1 = true; $__currentLoopData = $appointmentLetters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $letter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr
                                            class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-colors duration-150">
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            <?php echo e($letter->customer->first_name); ?>

                                                            <?php echo e($letter->customer->last_name); ?>

                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            <?php echo e($letter->customer->email); ?>

                                                        </div>                                                        
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo e($letter->customer->mobile_number); ?>

                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php if($letter->appointment_date): ?>
                                                    <?php echo e($letter->appointment_date->format('d/m/Y')); ?> at <?php echo e($letter->appointment_date->format('h:i A')); ?>

                                                <?php else: ?>
                                                    Not scheduled
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo e($letter->upload_date->format('d/m/Y')); ?>

                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo e($letter->uploader->name ?? 'System'); ?>

                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                                <div class="flex items-center space-x-3">
                                                    <a href="<?php echo e(route('admin.appointment-letters.preview', $letter->id)); ?>"
                                                        target="_blank" class="text-blue-600 hover:text-blue-900"
                                                        title="View File">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    <a href="<?php echo e(route('admin.appointment-letters.edit', $letter->id)); ?>"
                                                        class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                    <a href="<?php echo e(route('admin.appointment-letters.download', $letter->id)); ?>"
                                                        class="text-green-600 hover:text-green-900" title="Download">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                    </a>
                                                    <button onclick="confirmDelete('<?php echo e($letter->id); ?>')"
                                                        class="text-red-600 hover:text-red-900" title="Delete">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="px-6 py-10 text-center">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-300" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <p class="mt-4 text-gray-500 text-sm font-medium">No appointment
                                                        letters found</p>
                                                    <?php if(request('search') || request('start_date') || request('end_date')): ?>
                                                        <p class="mt-1 text-gray-400 text-xs">Try adjusting your search or
                                                            date filters</p>
                                                        <a href="<?php echo e(route('admin.appointment-letters.index')); ?>"
                                                            class="mt-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                            Clear Filters
                                                        </a>
                                                    <?php else: ?>
                                                        <p class="mt-1 text-gray-400 text-xs">No appointment letters
                                                            available at this time</p>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-6 px-4 sm:px-0 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-4">
                        <?php if($appointmentLetters->total() > 0): ?>
                            <p class="text-sm text-gray-500">
                                Showing
                                <span class="font-medium"><?php echo e($appointmentLetters->firstItem()); ?></span>
                                to
                                <span class="font-medium"><?php echo e($appointmentLetters->lastItem()); ?></span>
                                of
                                <span class="font-medium"><?php echo e($appointmentLetters->total()); ?></span>
                                results
                            </p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php echo e($appointmentLetters->appends(request()->query())->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <form id="delete-form" action="" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/laravel-app/releases/11/resources/views/admin/appointment-letters/index.blade.php ENDPATH**/ ?>