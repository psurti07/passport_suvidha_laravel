<?php $__env->startSection('title', 'Edit Appointment Letter'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mx-auto">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4 sm:p-6 lg:p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Edit Appointment Letter
                    </h2>
                    <a href="<?php echo e(route('admin.appointment-letters.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-all duration-200">
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
                
                <div class="bg-gray-50 rounded-lg p-6">
                    <form action="<?php echo e(route('admin.appointment-letters.update', $appointmentLetter->id)); ?>" method="POST" enctype="multipart/form-data" id="appointment-letter-form">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-5">
                                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                                    <select id="customer_id" name="customer_id" class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                        <option value="">Select a customer</option>
                                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($customer->id); ?>" <?php echo e(old('customer_id', $appointmentLetter->customer_id) == $customer->id ? 'selected' : ''); ?>>
                                                <?php echo e($customer->name); ?> (<?php echo e($customer->email); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                
                                <div class="mb-5">
                                    <label for="upload_date" class="block text-sm font-medium text-gray-700 mb-1">Upload Date</label>
                                    <input type="date" id="upload_date" name="upload_date" value="<?php echo e(old('upload_date', $appointmentLetter->upload_date->format('Y-m-d'))); ?>" class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm <?php $__errorArgs = ['upload_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <?php $__errorArgs = ['upload_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                
                                <div class="mb-5">
                                    <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-1">Appointment Date</label>
                                    <input type="date" id="appointment_date" name="appointment_date" value="<?php echo e(old('appointment_date', $appointmentLetter->appointment_date ? $appointmentLetter->appointment_date->format('Y-m-d') : '')); ?>" class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm <?php $__errorArgs = ['appointment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <?php $__errorArgs = ['appointment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                
                                <div class="mb-5">
                                    <label for="appointment_time" class="block text-sm font-medium text-gray-700 mb-1">Appointment Time</label>
                                    <input type="time" id="appointment_time" name="appointment_time" value="<?php echo e(old('appointment_time', $appointmentLetter->appointment_time ? $appointmentLetter->appointment_time->format('H:i') : '')); ?>" class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm <?php $__errorArgs = ['appointment_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <?php $__errorArgs = ['appointment_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                
                                <div class="mb-5">
                                    <label for="appointment_letter" class="block text-sm font-medium text-gray-700 mb-1">Document File (Leave empty to keep current file)</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="appointment_letter" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                                    <span>Upload a file</span>
                                                    <input id="appointment_letter" name="appointment_letter" type="file" class="sr-only" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                PDF, DOC, DOCX, JPG, JPEG, or PNG up to 10MB
                                            </p>
                                            <p class="text-sm text-gray-600 mt-2" id="current-file">Current file: <?php echo e(basename($appointmentLetter->file_path)); ?></p>
                                            <p class="text-sm text-gray-600 mt-2 hidden" id="file-name"></p>
                                        </div>
                                    </div>
                                    <?php $__errorArgs = ['appointment_letter'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            
                            <div>
                                <div class="mb-5">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                                    <textarea id="description" name="description" rows="8" class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('description', $appointmentLetter->description)); ?></textarea>
                                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                
                                <div class="mb-5">
                                    <div class="flex items-center">
                                        <input id="notify_customer" name="notify_customer" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                        <label for="notify_customer" class="ml-2 text-sm font-medium text-gray-700">Notify Customer</label>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Send an email notification to the customer about this update.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between pt-6 border-t border-gray-200 mt-6">
                            <a href="<?php echo e(route('admin.appointment-letters.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Cancel
                            </a>
                            
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium shadow-sm hover:from-blue-700 hover:to-blue-900 transition-all duration-200 transform hover:-translate-y-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Update Appointment Letter
                            </button>
                        </div>
                    </form>
                </div>
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

        // Handle file input change (keep existing logic)
        const fileInput = document.getElementById('appointment_letter');
        const fileNameDisplay = document.getElementById('file-name');
        const currentFileDisplay = document.getElementById('current-file');
        
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    fileNameDisplay.textContent = `Selected file: ${this.files[0].name}`;
                    fileNameDisplay.classList.remove('hidden');
                    if (currentFileDisplay) currentFileDisplay.classList.add('hidden');
                } else {
                    fileNameDisplay.classList.add('hidden');
                    if (currentFileDisplay) currentFileDisplay.classList.remove('hidden');
                }
            });
        }
        
        // Handle drag and drop (keep existing logic)
        const dropZone = document.querySelector('.border-dashed');
        if (dropZone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                dropZone.classList.add('border-blue-300', 'bg-blue-50');
            }
            
            function unhighlight() {
                dropZone.classList.remove('border-blue-300', 'bg-blue-50');
            }
            
            dropZone.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files && files.length && fileInput) {
                    fileInput.files = files;
                    fileNameDisplay.textContent = `Selected file: ${files[0].name}`;
                    fileNameDisplay.classList.remove('hidden');
                    if (currentFileDisplay) currentFileDisplay.classList.add('hidden');
                }
            }
        }

        // Remove old success/error message checks if they existed in DOMContentLoaded
        // const successMessage = document.getElementById('success-message');
        // ... old toastify logic ...
        // const errorMessage = document.getElementById('error-message');
        // ... old toastify logic ...
    });
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    
    
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
            // Old file input/drag-drop/toastify logic removed from here
        // });
    </script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/laravel-app/releases/11/resources/views/admin/appointment-letters/edit.blade.php ENDPATH**/ ?>