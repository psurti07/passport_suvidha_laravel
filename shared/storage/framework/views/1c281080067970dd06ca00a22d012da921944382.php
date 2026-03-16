<?php $__env->startSection('title', 'Edit Final Detail'); ?>

<?php $__env->startSection('content'); ?>
<div class="mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                    Edit Final Detail
                </h2>
                <a href="<?php echo e(route('admin.final-details.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to List
                </a>
            </div>

            
            <?php if($errors->any()): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <strong class="font-bold">Whoops! Something went wrong.</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('admin.final-details.update', $finalDetail)); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="space-y-6">
                    
                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer <span class="text-red-500">*</span></label>
                        <select id="customer_id" name="customer_id" required
                            class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Select Customer</option>
                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($customer->id); ?>" <?php echo e((old('customer_id', $finalDetail->customer_id) == $customer->id) ? 'selected' : ''); ?>>
                                    <?php echo e($customer->full_name); ?> (<?php echo e($customer->mobile_number); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Document (PDF, JPG, JPEG, PNG)</label>
                        
                        <?php if($finalDetail->file_path): ?>
                            <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-100">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-sm text-gray-700">Current file:</span>
                                    <a href="<?php echo e(Storage::url($finalDetail->file_path)); ?>" target="_blank" class="text-blue-600 hover:underline ml-2">
                                        <span class="text-sm"><?php echo e(basename($finalDetail->file_path)); ?></span>
                                    </a>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Uploaded on: <?php echo e($finalDetail->upload_date ? $finalDetail->upload_date->format('d M Y, h:i A') : 'N/A'); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-2">
                            <input type="file" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png"
                                class="block w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <p class="mt-1 text-xs text-gray-500">Maximum file size: 10MB. Leave empty to keep current file.</p>
                        </div>
                        
                        <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div>
                        <div class="flex items-center">
                            <input id="is_approved" name="is_approved" type="checkbox" value="1" <?php echo e($finalDetail->is_approved ? 'checked' : ''); ?>

                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_approved" class="ml-2 block text-sm font-medium text-gray-700">
                                Approved
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Approving will automatically set the approval date and approver to the current user.</p>
                        <?php if($finalDetail->is_approved): ?>
                            <div class="mt-2 text-xs text-gray-600">
                                <p>Currently approved by: <?php echo e($finalDetail->approverName); ?></p>
                                <p>Approved date: <?php echo e($finalDetail->approved_date ? $finalDetail->approved_date->format('d M Y, h:i A') : 'N/A'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <h3 class="text-sm font-medium text-gray-700 mb-1">Upload Information</h3>
                        <p class="text-xs text-gray-600">
                            Uploaded by: <?php echo e($finalDetail->uploader->name ?? 'Unknown'); ?><br>
                            Upload date: <?php echo e($finalDetail->upload_date ? $finalDetail->upload_date->format('d M Y, h:i A') : 'N/A'); ?>

                        </p>
                        <p class="mt-2 text-xs text-gray-500">Note: Uploading a new file will update this information to the current user and time.</p>
                    </div>

                    
                    <div class="flex justify-end pt-4">
                        <a href="<?php echo e(route('admin.final-details.index')); ?>"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-all duration-200 mr-2">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l-4-4m4 4H7" />
                            </svg>
                            Update Final Detail
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
    // Toast notification function
    function showToast(message, type = 'success') {
        let bgColor = type === 'success' ? 'linear-gradient(to right, #00b09b, #96c93d)' : 
                      type === 'error' ? 'linear-gradient(to right, #ff5f6d, #ffc371)' : 
                      'linear-gradient(to right, #00b09b, #96c93d)';
        
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: bgColor,
            stopOnFocus: true,
        }).showToast();
    }

    // Show session messages
    document.addEventListener('DOMContentLoaded', function() {
        <?php if(session('success')): ?>
            showToast("<?php echo e(session('success')); ?>", 'success');
        <?php endif; ?>
        <?php if(session('error')): ?>
            showToast("<?php echo e(session('error')); ?>", 'error');
        <?php endif; ?>
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/laravel-app/releases/11/resources/views/admin/final-details/edit.blade.php ENDPATH**/ ?>