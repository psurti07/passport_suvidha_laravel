    

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
            <form id="filterForm" action="<?php echo e(route('admin.customers.index')); ?>" method="GET">
                <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 sm:space-y-0 mb-6">
                    <div class="flex items-center gap-4">
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                            CUSTOMERS
                        </h2>
                    </div>
                    <div class="flex flex-row lg:flex-row flex-wrap gap-4 space-y-2 lg:space-y-0 lg:space-x-2">
                         <div class="flex flex-col lg:flex-row gap-4 w-full sm:w-auto space-y-0 lg:space-y-0 lg:space-x-2">
                            <div class="flex flex-row sm:flex-row items-center gap-2 space-x-2">
                                <label class="text-sm font-medium text-gray-700 whitespace-nowrap">From:</label>
                                <input type="date" name="from_date"
                                    class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm w-full sm:w-40"
                                    value="<?php echo e(request('from_date')); ?>">
                            </div>
                            <div class="flex flex-row sm:flex-row items-center gap-2 space-x-2">
                                <label class="text-sm font-medium text-gray-700 whitespace-nowrap">To:</label>
                                <input type="date" name="to_date"
                                    class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm w-full sm:w-40"
                                    value="<?php echo e(request('to_date')); ?>">
                            </div>
                        </div>
                        
                        <input type="hidden" name="sort_by" value="<?php echo e(request('sort_by', 'id')); ?>">
                        <input type="hidden" name="sort_direction" value="<?php echo e(request('sort_direction', 'desc')); ?>">
                        <input type="hidden" id="perPageInput" name="per_page" value="<?php echo e(request('per_page', 10)); ?>">
                        <button type="submit"
                            class="w-full sm:w-auto px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            Show Results
                        </button>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row justify-between items-center mt-6 gap-4">
                     <div class="flex flex-wrap items-center gap-2 space-x-2">
                        
                         
                         <button type="button" onclick="copyToClipboard()"
                            class="inline-flex items-center px-4 py-2 bg-white text-blue-800 rounded-lg text-sm font-medium shadow-md hover:bg-gray-100 transition-colors duration-150">
                                Copy
                            </button>
                            
                            <a href="<?php echo e(route('admin.customers.export', ['type' => 'excel'] + request()->all())); ?>"
                                class="inline-flex items-center px-4 py-2 bg-white text-blue-800 rounded-lg text-sm font-medium shadow-md hover:bg-gray-100 transition-colors duration-150">
                                Excel
                            </a>
                            <a href="<?php echo e(route('admin.customers.export', ['type' => 'csv'] + request()->all())); ?>"
                                class="inline-flex items-center px-4 py-2 bg-white text-blue-800 rounded-lg text-sm font-medium shadow-md hover:bg-gray-100 transition-colors duration-150">
                                CSV
                            </a>
                            <a href="<?php echo e(route('admin.customers.export', ['type' => 'pdf'] + request()->all())); ?>"
                                class="inline-flex items-center px-4 py-2 bg-white text-blue-800 rounded-lg text-sm font-medium shadow-md hover:bg-gray-100 transition-colors duration-150">
                                PDF
                            </a>
                        </div>
                        <div class="flex items-center gap-4">
                            
                             <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-gray-700">Status:</label>
                                <select name="status"
                                        onchange="document.getElementById('filterForm').submit();"
                                        class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm w-full sm:w-32">
                                    <option value="" <?php echo e(request('status') == '' ? 'selected' : ''); ?>>All</option>
                                    <option value="paid" <?php echo e(request('status') == 'paid' ? 'selected' : ''); ?>>Paid</option>
                                    <option value="lead" <?php echo e(request('status') == 'lead' ? 'selected' : ''); ?>>Lead</option>
                                </select>
                            </div>
                            <label for="searchInput" class="text-sm font-medium text-gray-700">Search:</label>
                            <input type="text" id="searchInput" name="search" value="<?php echo e(request('search')); ?>"
                                placeholder="Search customers..."
                                class="border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm w-64">
                        </div>
                    </div>
                </form>

            <div class="mt-8 overflow-x-auto">
                <div class="inline-block min-w-full align-middle max-h-[60vh] overflow-y-auto">
                    <div class="shadow-sm ring-1 ring-black ring-opacity-5">
                        <table class="min-w-full divide-y divide-gray-200 relative">
                            <thead class="bg-blue-50">
                                <tr>
                                    <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                        <?php echo sortLink('id', 'ID'); ?>

                                    </th>
                                    <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                        <?php echo sortLink('first_name', 'Name'); ?>

                                    </th>
                                    <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                        <?php echo sortLink('email', 'Email'); ?>

                                    </th>
                                    <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                        <?php echo sortLink('mobile_number', 'Mobile'); ?>

                                    </th>
                                    <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                        <?php echo sortLink('is_paid', 'Status'); ?>

                                    </th>
                                    <th scope="col" class="sticky top-0 bg-blue-50 px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                         <?php echo sortLink('created_at', 'Created At'); ?>

                                    </th>
                                </tr>
                            </thead>
                             <tbody class="bg-white">
                                <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                 <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-colors duration-150 cursor-pointer" 
                                     onclick="window.location.href='<?php echo e(route('admin.customers.show', $customer)); ?>?previous_url=<?php echo e(urlencode(request()->fullUrl())); ?>'">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?php echo e($customer->id); ?>

                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?php echo e($customer->first_name); ?> <?php echo e($customer->last_name); ?>

                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?php echo e(strtolower($customer->email)); ?>

                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?php echo e($customer->mobile_number); ?>

                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <?php if($customer->is_paid): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"> Paid </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"> Lead </span>
                                        <?php endif; ?>
                                    </td>
                                     <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                         <?php echo e($customer->created_at->format('d/m/Y H:i:s')); ?>

                                     </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                     <td colspan="6" class="px-6 py-10 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="mt-4 text-gray-500 text-sm font-medium">No customers found</p>
                                            <p class="mt-1 text-gray-400 text-xs">Try adjusting your search or filter to find what you're looking for.</p>
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
                    
                    <?php if($customers->total() > 0): ?>
                        <p class="text-sm text-gray-500">
                            Showing
                            <span class="font-medium"><?php echo e($customers->firstItem()); ?></span>
                            to
                            <span class="font-medium"><?php echo e($customers->lastItem()); ?></span>
                            of
                            <span class="font-medium"><?php echo e($customers->total()); ?></span>
                            results
                        </p>
                    <?php endif; ?>

                    
                    <?php $options = [10, 25, 50, 100]; ?>

                    
                    <?php if($customers->total() > ($options[0] ?? 10)): ?>
                        <div class="flex items-center gap-2">
                            <label for="perPageBottom" class="text-sm font-medium text-gray-700">Per Page:</label>
                            
                            <select id="perPageBottom"
                                onchange="document.getElementById('perPageInput').value = this.value; document.getElementById('filterForm').submit();"
                                class="border border-gray-300 rounded-lg text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                                <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($option); ?>"
                                        <?php echo e(request('per_page', 10) == $option ? 'selected' : ''); ?>>
                                        <?php echo e($option); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div>
                     
                    <?php if($customers->hasPages()): ?>
                        <?php echo e($customers->appends(request()->query())->links()); ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showToast(message, type = 'success') {
        let bgColorClass, textColorClass, iconSvg, borderColorClass;

        switch (type) {
            case 'success':
                bgColorClass = 'bg-emerald-500/95';
                textColorClass = 'text-white';
                borderColorClass = 'border-emerald-400';
                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>`;
                break;
            case 'error':
                bgColorClass = 'bg-red-500/95';
                textColorClass = 'text-white';
                borderColorClass = 'border-red-400';
                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>`;
                break;
            case 'warning':
                bgColorClass = 'bg-amber-500/95';
                textColorClass = 'text-white';
                borderColorClass = 'border-amber-400';
                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>`;
                break;
            default: // Info
                bgColorClass = 'bg-blue-500/95';
                textColorClass = 'text-white';
                borderColorClass = 'border-blue-400';
                iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>`;
        }

        const toastNode = document.createElement('div');
        toastNode.className = `toast-content flex items-center w-full max-w-sm p-4 mb-4 ${bgColorClass} ${textColorClass} rounded-lg shadow-2xl border ${borderColorClass} backdrop-blur`;
        toastNode.innerHTML = `
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${textColorClass}">
                ${iconSvg}
            </div>
            <div class="ml-3 text-sm font-normal">${message}</div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8 ${textColorClass} hover:${bgColorClass.replace('/95', '')} focus:outline-none">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>`;

        // Add click handler for close button
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

    function copyToClipboard() {
        const table = document.querySelector('table');
        const rows = Array.from(table.querySelectorAll('tbody tr'));

        // Check if the only row is the "No customers found" row (using colspan="7")
        if (rows.length === 1 && rows[0].querySelector('td[colspan="7"]')) {
            showToast('No data to copy.', 'error');
            return;
        }

        const header = Array.from(table.querySelectorAll('thead th'))
            .map(th => th.innerText.trim())
            .slice(0, -1) // Exclude the last 'Action' header
            .join('\t'); // Join header cells with TAB

        let text = header + '\n';

        text += rows.map(row => {
            // Skip the "No customers found" row if it exists among other rows
            if (row.querySelector('td[colspan="7"]')) {
                return '';
            }
            return Array.from(row.querySelectorAll('td'))
                .slice(0, -1) // Exclude the last 'Action' cell
                .map(cell => cell.textContent.trim().replace(/\s+/g, ' ')) // Clean whitespace
                .join('\t');
        })
        .filter(rowText => rowText !== '')
        .join('\n');

        navigator.clipboard.writeText(text).then(() => {
            showToast('Table data copied to clipboard!', 'success');
        }).catch(err => {
            console.error('Failed to copy text: ', err);
            showToast('Failed to copy data to clipboard.', 'error');
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // SweetAlert2 delete confirmation
        const deleteForms = document.querySelectorAll('.delete-customer-form'); // Updated class
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33', // Make confirm button red for delete
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
                        event.target.submit();
                    }
                });
            });
        });

        // Check for flashed session messages and show toasts
        <?php if(session('success')): ?>
            showToast("<?php echo e(session('success')); ?>", 'success');
        <?php endif; ?>

        <?php if(session('error')): ?>
            showToast("<?php echo e(session('error')); ?>", 'error');
        <?php endif; ?>
    });

    <?php
        $sortBy = request('sort_by', 'id');
        $sortDirection = request('sort_direction', 'asc');

        function sortLink($column, $label)
        {
            $sortBy = request('sort_by', 'id');
            $sortDirection = request('sort_direction', 'asc');
            $newDirection = ($sortBy == $column && $sortDirection == 'asc') ? 'desc' : 'asc';
            $route = 'admin.customers.index'; // Make sure this is correct
            $queryParams = array_merge(request()->except('page'), [
                'sort_by' => $column,
                'sort_direction' => $newDirection
            ]);

            $icon = '';
            if ($sortBy == $column) {
                $icon = $sortDirection == 'asc'
                    ? '<svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg>'
                    : '<svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>';
            }

            return '<a href="' . route($route, $queryParams) . '" class="flex items-center hover:text-blue-700">' . $label . $icon . '</a>';
        }
    ?>
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/laravel-app/releases/11/resources/views/admin/customers/index.blade.php ENDPATH**/ ?>