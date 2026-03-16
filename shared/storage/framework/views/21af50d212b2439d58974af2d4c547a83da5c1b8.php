<?php $__env->startSection('title', 'Appointment Letter Details'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $fileSize = 'Unknown';
        $filePath = storage_path('app/public/' . $appointmentLetter->file_path);
        if (file_exists($filePath)) {
            $bytes = filesize($filePath);
            if ($bytes >= 1073741824) {
                $fileSize = number_format($bytes / 1073741824, 2) . ' GB';
            } elseif ($bytes >= 1048576) {
                $fileSize = number_format($bytes / 1048576, 2) . ' MB';
            } elseif ($bytes >= 1024) {
                $fileSize = number_format($bytes / 1024, 2) . ' KB';
            } else {
                $fileSize = $bytes . ' bytes';
            }
        }
    ?>
    
    <div class="mx-auto">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-4 sm:p-6 lg:p-8">
                <div class="flex flex-wrap justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                            Appointment Letter Details
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Viewing details for appointment letter #<?php echo e($appointmentLetter->id); ?></p>
                    </div>
                    
                    <div class="flex space-x-2 mt-4 sm:mt-0">
                        <a href="<?php echo e(route('admin.appointment-letters.edit', $appointmentLetter->id)); ?>" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white rounded-lg text-sm font-medium shadow-sm hover:bg-gray-50 transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                        
                        <a href="<?php echo e(route('admin.appointment-letters.download', $appointmentLetter->id)); ?>" 
                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium shadow-sm hover:from-blue-700 hover:to-blue-900 transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </a>
                        
                        <form action="<?php echo e(route('admin.appointment-letters.destroy', $appointmentLetter->id)); ?>" 
                              method="POST" 
                              class="inline delete-form"
                              data-name="This appointment letter">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-red-300 bg-white text-red-600 rounded-lg text-sm font-medium shadow-sm hover:bg-red-50 transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
                
                <?php if(session('success')): ?>
                    <div class="hidden" id="success-message" data-message="<?php echo e(session('success')); ?>"></div>
                <?php endif; ?>
                
                <div class="bg-gray-50 rounded-lg p-6 mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-md font-semibold text-gray-800 mb-4">Customer Information</h3>
                            <div class="bg-white shadow-sm rounded-lg p-4 border border-gray-100">
                                <div class="flex items-start">
                                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-blue-700 font-semibold text-lg">
                                            <?php echo e(substr($appointmentLetter->customer->name, 0, 1)); ?>

                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-medium text-gray-900"><?php echo e($appointmentLetter->customer->name); ?></h4>
                                        <p class="text-gray-500"><?php echo e($appointmentLetter->customer->email); ?></p>
                                        <?php if($appointmentLetter->customer->phone): ?>
                                            <p class="text-gray-500"><?php echo e($appointmentLetter->customer->phone); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-gray-500">Customer ID</p>
                                            <p class="text-sm font-medium"><?php echo e($appointmentLetter->customer->id); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Joined</p>
                                            <p class="text-sm font-medium"><?php echo e($appointmentLetter->customer->created_at->format('M d, Y')); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-md font-semibold text-gray-800 mb-4">Document Information</h3>
                            <div class="bg-white shadow-sm rounded-lg p-4 border border-gray-100">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-700">Appointment Letter</p>
                                        <p class="text-xs text-gray-500"><?php echo e(strtoupper(pathinfo($appointmentLetter->file_path, PATHINFO_EXTENSION))); ?> File</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500">Upload Date</p>
                                        <p class="text-sm font-medium"><?php echo e($appointmentLetter->upload_date->format('M d, Y')); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Appointment Date</p>
                                        <p class="text-sm font-medium"><?php echo e($appointmentLetter->appointment_date ? $appointmentLetter->appointment_date->format('M d, Y') : 'Not specified'); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Appointment Time</p>
                                        <p class="text-sm font-medium"><?php echo e($appointmentLetter->appointment_time ? $appointmentLetter->appointment_time->format('h:i A') : 'Not specified'); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">File Size</p>
                                        <p class="text-sm font-medium"><?php echo e($fileSize); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Uploaded By</p>
                                        <p class="text-sm font-medium"><?php echo e($appointmentLetter->uploader->name); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Last Updated</p>
                                        <p class="text-sm font-medium"><?php echo e($appointmentLetter->updated_at->format('M d, Y h:i A')); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if($appointmentLetter->description): ?>
                        <div class="mt-6">
                            <h3 class="text-md font-semibold text-gray-800 mb-4">Description</h3>
                            <div class="bg-white shadow-sm rounded-lg p-4 border border-gray-100">
                                <p class="text-gray-700 whitespace-pre-line"><?php echo e($appointmentLetter->description); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="flex justify-between mt-6">
                    <a href="<?php echo e(route('admin.appointment-letters.index')); ?>" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white rounded-lg text-sm font-medium shadow-sm hover:bg-gray-50 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle success messages
            const successMessage = document.getElementById('success-message');
            if (successMessage && successMessage.dataset.message) {
                Toastify({
                    text: successMessage.dataset.message,
                    duration: 3000,
                    className: "toast-success",
                    close: true,
                    gravity: "top",
                    position: "right",
                }).showToast();
            }
            
            // Handle delete confirmation
            const deleteForm = document.querySelector('.delete-form');
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const itemName = this.dataset.name || 'this item';
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: `You are about to delete ${itemName}. This action cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#EF4444',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }
        });
    </script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/laravel-app/releases/11/resources/views/admin/appointment-letters/show.blade.php ENDPATH**/ ?>