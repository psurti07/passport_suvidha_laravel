<?php $__env->startSection('title', 'Ticket Details: ' . $ticket->ticket_number); ?>

<?php $__env->startSection('content'); ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
         
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
             SUPPORT REQUEST TICKET DETAILS
        </h1>
        <div class="flex items-center space-x-3 flex-shrink-0">
            
            <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                'bg-green-100 text-green-800' => $ticket->status === 'open',
                'bg-yellow-100 text-yellow-800' => $ticket->status === 'in_progress',
                'bg-red-100 text-red-800' => $ticket->status === 'closed',
                'bg-gray-100 text-gray-800' => !in_array($ticket->status, ['open', 'in_progress', 'closed']),
            ]) ?>">
                Status: <?php echo e(ucfirst(str_replace('_', ' ', $ticket->status))); ?>

            </span>

            
            <a href="<?php echo e(url()->previous()); ?>"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back
            </a>
        </div>
    </div>

    
    <?php if(session('success')): ?>
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if($errors->any()): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Oops! Something went wrong.</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:items-start"> 
        
        <div class="lg:col-span-1 bg-white rounded-xl shadow-lg border border-gray-100 p-6 space-y-6">
            
            <div>
                <h2 class="text-lg font-semibold text-blue-700 border-b border-gray-200 pb-3 mb-4">Ticket Information</h2>
                <dl class="space-y-4">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Ticket No.</dt>
                        <dd class="text-sm text-gray-900 font-semibold"><?php echo e($ticket->ticket_number); ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Request Date</dt>
                        <dd class="text-sm text-gray-900"><?php echo e($ticket->created_at->format('d/m/Y H:i')); ?></dd>
                    </div>
                     <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">User Type</dt>
                        <dd class="text-sm text-gray-900 font-semibold"><?php echo e($userType); ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Full name</dt>
                        <dd class="text-sm text-gray-900 font-semibold"><?php echo e($ticket->name); ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Mobile</dt>
                        
                        <dd class="text-sm text-gray-900 font-semibold"><?php echo e(optional($ticket->customer)->mobile ?? 'N/A'); ?></dd> 
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Email Id</dt>
                        <dd class="text-sm text-gray-900 font-semibold"><?php echo e($ticket->email); ?></dd>
                    </div>
                     <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Card No.</dt>
                        
                        <dd class="text-sm text-gray-900 font-semibold"><?php echo e(optional($ticket->customer)->card_no ?? 'N/A'); ?></dd> 
                    </div>
                </dl>
            </div>

            
            <div>
                <h2 class="text-lg font-semibold text-blue-700 border-b border-gray-200 pb-3 mb-4">Update Status</h2>
                <form action="<?php echo e(route('admin.support.tickets.status.update', $ticket->ticket_number)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <div class="flex items-center gap-3">
                        <label for="status" class="sr-only">Status</label>
                        <select id="status" name="status" class="block w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                            <option value="open" <?php echo e($ticket->status == 'open' ? 'selected' : ''); ?>>Open</option>
                            <option value="in_progress" <?php echo e($ticket->status == 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                            <option value="closed" <?php echo e($ticket->status == 'closed' ? 'selected' : ''); ?>>Closed</option>
                        </select>
                        <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 whitespace-nowrap">
                            Update
                        </button>
                    </div>
                </form>
            </div>

        </div>

        
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-100 p-6 space-y-6">
            
            <div>
                
                <h2 class="text-lg font-semibold text-blue-700 mb-2">Subject: <?php echo e($ticket->subject); ?></h2>
            </div>

            
            <div class="border rounded-lg p-4 bg-gray-50"> 
                <h3 class="text-md font-medium text-gray-700 mb-2">Customer Message:</h3>
                <p class="text-sm text-gray-800 break-words whitespace-pre-wrap"><?php echo e($ticket->message); ?></p>
            </div>

            
            <div>
                 <h3 class="text-md font-medium text-gray-700 mb-3 border-t pt-4">Staff Remarks</h3> 

                 
                 <div class="mb-4 space-y-3 max-h-72 overflow-y-auto pr-2 border rounded-md p-3 bg-gray-50"> 
                     <?php $__empty_1 = true; $__currentLoopData = $ticket->ticketRemarks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $remark): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                         <div class="bg-blue-50 p-3 rounded-md border border-blue-100 shadow-sm">
                             <p class="text-sm text-gray-800 whitespace-pre-wrap"><?php echo e($remark->comment); ?></p>
                             <p class="text-xs text-gray-500 mt-1.5">
                                 By: <span class="font-medium"><?php echo e($remark->user->name ?? 'Staff'); ?></span>
                                 on <span class="font-medium"><?php echo e($remark->created_at->format('d/m/Y H:i')); ?></span>
                             </p>
                         </div>
                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                         <p class="text-sm text-gray-500 italic text-center py-4">No remarks added yet.</p>
                     <?php endif; ?>
                 </div>

                 
                 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                 <script>
                 $(document).ready(function() {
                     $('#add-remark-form').on('submit', function(e) {
                         e.preventDefault(); // Prevent the default form submission
                         var form = $(this);
                         var submitButton = form.find('button[type="submit"]');
                         var remarksContainer = $('.max-h-60'); // Cache the remarks container
                         var noRemarksMessage = remarksContainer.find('.text-gray-500.italic'); // Cache the 'no remarks' message

                         submitButton.prop('disabled', true).addClass('opacity-50 cursor-not-allowed'); // Disable button and add visual cue

                         // Clear previous messages
                         $('.alert-success, .alert-error').remove();

                         $.ajax({
                             url: form.attr('action'),
                             method: 'POST',
                             data: form.serialize(),
                             headers: {
                                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                             },
                             success: function(response) {
                                 // Create new remark element
                                 var remarkHtml = `
                                     <div class="bg-blue-50 p-3 rounded-md border border-blue-100 shadow-sm new-remark" style="display: none;">
                                         <p class="text-sm text-gray-800 whitespace-pre-wrap">${response.remark.comment}</p>
                                         <p class="text-xs text-gray-500 mt-1.5">
                                             By: <span class="font-medium">${response.user.name}</span>
                                             on <span class="font-medium">${response.remark.created_at}</span>
                                         </p>
                                     </div>
                                 `;

                                 // Remove 'no remarks' message if it exists
                                 if (noRemarksMessage.length) {
                                     noRemarksMessage.remove();
                                 }

                                 // Add the new remark to the top and fade it in
                                 remarksContainer.prepend(remarkHtml);
                                 remarksContainer.find('.new-remark').first().slideDown('fast').removeClass('new-remark');

                                 // Clear the form
                                 form.find('textarea').val('');

                                 // Show success message
                                 var successHtml = `
                                     <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative alert-success" style="display: none;">
                                         <span class="block sm:inline">${response.message || 'Remark added successfully!'}</span>
                                     </div>
                                 `;
                                 form.before(successHtml);
                                 $('.alert-success').slideDown('fast');

                                 // Remove success message after 3 seconds
                                 setTimeout(function() {
                                     $('.alert-success').slideUp('slow', function() {
                                         $(this).remove();
                                     });
                                 }, 3000);
                             },
                             error: function(xhr) {
                                 // Show error message
                                 var errorMessage = 'An error occurred while adding the remark.';
                                 var validationErrors = '';
                                 if (xhr.responseJSON) {
                                     if (xhr.responseJSON.message) {
                                         errorMessage = xhr.responseJSON.message;
                                     }
                                     // Handle validation errors (like remark being too short)
                                     if (xhr.responseJSON.errors && xhr.responseJSON.errors.remark) {
                                         validationErrors = `<ul class="mt-2 list-disc list-inside text-sm"><li>${xhr.responseJSON.errors.remark.join('</li><li>')}</li></ul>`;
                                         errorMessage = 'Please correct the following error:'; // More specific message
                                     }
                                 }

                                 var errorHtml = `
                                     <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative alert-error" style="display: none;">
                                         <strong class="font-bold">${errorMessage}</strong>
                                         ${validationErrors}
                                     </div>
                                 `;
                                 form.before(errorHtml);
                                 $('.alert-error').slideDown('fast');

                                 // Optional: Remove error message after some time, or let user dismiss it
                                 // setTimeout(function() {
                                 //     $('.alert-error').slideUp('slow', function() { $(this).remove(); });
                                 // }, 5000);
                             },
                             complete: function() {
                                 submitButton.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed'); // Re-enable submit button
                             }
                         });
                     });
                 });
                 </script>

                 <form id="add-remark-form" action="<?php echo e(route('admin.support.tickets.remarks.store', $ticket->ticket_number)); ?>" method="POST" class="mt-4"> 
                     <?php echo csrf_field(); ?>
                     <div>
                         <label for="remark" class="sr-only">Add Remark</label>
                         <textarea id="remark" name="remark" rows="3" required minlength="5"
                                   class="block w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-150 ease-in-out <?php $__errorArgs = ['remark'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Enter remark here..."><?php echo e(old('remark')); ?></textarea>
                         <?php $__errorArgs = ['remark'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            
                            <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                         <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                     </div>
                     <div class="mt-3 flex justify-end">
                         <button type="submit"
                                 class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                             Add Remark
                         </button>
                     </div>
                 </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/laravel-app/releases/11/resources/views/admin/support/show.blade.php ENDPATH**/ ?>