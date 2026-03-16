<?php $__env->startSection('title', 'Search Data'); ?>

<?php $__env->startSection('content'); ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="md:col-span-1 bg-white p-8 rounded-lg shadow-lg border border-gray-200 md:self-start">
            <h2 class="text-xl font-semibold mb-6 text-gray-800 border-b pb-3">Search Customer</h2>
            <form action="<?php echo e(route('admin.customer.search')); ?>" method="POST" class="mt-6 space-y-6">
                <?php echo csrf_field(); ?>

                <div>
                    <label for="mobile_no"class="block text-gray-700 text-sm font-bold mb-2">Mobile No *</label>
                    <input type="text" name="mobile_no" id="mobile_no" value="<?php echo e(old('mobile_no', $mobileNo ?? '')); ?>"
                        class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm <?php $__errorArgs = ['mobile_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        title="Mobile number must be 10 digits" placeholder="Enter 10-digit mobile number">
                    <?php $__errorArgs = ['mobile_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-sm italic mt-2"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex items-center justify-start pt-2">
                    <button type="submit" class="btn-primary px-6 py-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                    </button>
                </div>
            </form>
        </div>

        
        <?php if(isset($mobileNo) || isset($customer)): ?>
            <div class="md:col-span-2 bg-white p-8 rounded-lg shadow-lg border border-gray-200">
                <h2 class="text-xl font-semibold mb-6 text-gray-800 border-b pb-3">Search Result</h2>
                <div class="mt-6">
                    <?php if($customer): ?>
                        <dl class="divide-y divide-gray-200">
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Module</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold">CUSTOMER</dd>
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Mobile</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <?php echo e($customer->mobile_number ?? 'N/A'); ?></dd> 
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Registration Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <?php echo e($customer->created_at ? $customer->created_at->format('d M Y, H:i A') : 'N/A'); ?></dd>
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <?php echo e($customer->first_name ?? ''); ?> <?php echo e($customer->last_name ?? 'N/A'); ?></dd>
                                
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><?php echo e($customer->email ?? 'N/A'); ?>

                                </dd>
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <?php if($customer->is_paid == 1): ?>
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Registered Customer
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Lead
                                        </span>
                                    <?php endif; ?>
                                </dd>
                            </div>
                        </dl>
                        <?php if($customer->is_paid == 1): ?>
                            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end">
                                <a href="<?php echo e(route('admin.customers.show', $customer->id)); ?>?previous_url=<?php echo e(urlencode(request()->fullUrl())); ?>"
                                    class="btn-primary px-5 py-2">
                                    View Full Details &rarr;
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php elseif(isset($mobileNo)): ?>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.33-.22 3.008-1.74 3.008H4.413c-1.52 0-2.49-1.678-1.74-3.008l5.58-9.92zM10 13a1 1 0 100-2 1 1 0 000 2zm-1-4a1 1 0 011-1h.008a1 1 0 110 2H10a1 1 0 01-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        No customer found for mobile number: <strong
                                            class="font-medium text-yellow-800"><?php echo e($mobileNo); ?></strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/laravel-app/releases/11/resources/views/admin/customer/search.blade.php ENDPATH**/ ?>