<?php $__env->startSection('title', 'Customer Details - ' . ($customer->first_name ?? 'N/A')); ?>

<?php
    // REMOVE PHP Helper function - Replaced by Alpine
    // function isActiveTab($tabName, $currentTab) { ... }
    // $currentTab = request()->query('tab', 'info');
?>

<?php $__env->startSection('content'); ?>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mt-2">CUSTOMER DETAILS</h1>
            <p class="text-xl text-gray-700 font-medium mt-1"><?php echo e(strtoupper($customer->first_name ?? 'N/A')); ?> -
                <?php echo e($customer->mobile_number ?? 'N/A'); ?></p>
        </div>
        <div>
            
            <a href="<?php echo e(request()->query('previous_url', route('admin.customers.index'))); ?>"
                class="btn-secondary inline-flex items-center border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>
    </div>

    
    <div class="flex flex-col lg:flex-row gap-6" x-data="{
        activeTab: window.location.hash ? window.location.hash.substring(1) : 'info',
        setActiveTab(tab) {
            this.activeTab = tab;
            window.location.hash = tab;
        }
    }" x-init="() => {
        // Listen for hash changes in the URL
        window.addEventListener('hashchange', () => {
            activeTab = window.location.hash ? window.location.hash.substring(1) : 'info';
        });
    
        // Set initial hash if not already present
        if (!window.location.hash) {
            window.location.hash = activeTab;
        }
    }">
        
        <div class="w-full lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <nav class="divide-y divide-gray-200">
                    
                    <a href="#" @click.prevent="setActiveTab('info')"
                        :class="{ 'bg-blue-600 text-white': activeTab === 'info', 'text-gray-700 hover:bg-blue-50 hover:text-blue-700 focus:bg-blue-100 focus:text-blue-800': activeTab !== 'info' }"
                        class="flex items-center px-6 py-4 text-sm font-medium transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Customer Info
                    </a>
                    <a href="#" @click.prevent="setActiveTab('documents')"
                        :class="{ 'bg-blue-600 text-white': activeTab === 'documents', 'text-gray-700 hover:bg-blue-50 hover:text-blue-700 focus:bg-blue-100 focus:text-blue-800': activeTab !== 'documents' }"
                        class="flex items-center px-6 py-4 text-sm font-medium transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Uploaded Documents
                    </a>
                    <a href="#" @click.prevent="setActiveTab('application-process')"
                        :class="{ 'bg-blue-600 text-white': activeTab === 'application-process', 'text-gray-700 hover:bg-blue-50 hover:text-blue-700 focus:bg-blue-100 focus:text-blue-800': activeTab !== 'application-process' }"
                        class="flex items-center px-6 py-4 text-sm font-medium transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Application Process
                    </a>
                    <a href="#" @click.prevent="setActiveTab('actions')"
                        :class="{ 'bg-blue-600 text-white': activeTab === 'actions', 'text-gray-700 hover:bg-blue-50 hover:text-blue-700 focus:bg-blue-100 focus:text-blue-800': activeTab !== 'actions' }"
                        class="flex items-center px-6 py-4 text-sm font-medium transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Actions
                    </a>
                </nav>
            </div>
        </div>

        
        <div class="w-full lg:w-3/4">

            
            <div x-show="activeTab === 'info'" x-cloak>
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b border-gray-200 gap-y-2">
                        <p class="text-sm text-gray-600">
                            Registration on: <strong
                                class="text-gray-800 font-semibold"><?php echo e($customer->created_at ? $customer->created_at->format('d M Y, H:i A') : 'N/A'); ?></strong>
                        </p>
                    </div>

                    
                    <form action="<?php echo e(route('admin.customers.update', $customer->id)); ?>" method="POST" class="space-y-5">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" id="first_name"
                                    value="<?php echo e(old('first_name', $customer->first_name)); ?>" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="last_name" id="last_name"
                                    value="<?php echo e(old('last_name', $customer->last_name ?? '')); ?>" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                            </div>

                            <div>
                                <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-1">Mobile No
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="mobile_number" id="mobile_number"
                                    value="<?php echo e(old('mobile_number', $customer->mobile_number)); ?>" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Id <span
                                        class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email"
                                    value="<?php echo e(old('email', $customer->email)); ?>" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                            </div>
                            <div>
                                <label for="pincode" class="block text-sm font-medium text-gray-700 mb-1">Pincode</label>
                                <input type="text" name="pincode" id="pincode"
                                    value="<?php echo e(old('pincode', $customer->pin_code)); ?>" placeholder="Enter pincode"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                            </div>
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="city" id="city"
                                    value="<?php echo e(old('city', $customer->city)); ?>" required placeholder="Enter city"
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State <span
                                        class="text-red-500">*</span></label>
                                <select name="state" id="state" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm">
                                    <option value="" disabled
                                        <?php echo e(old('state', $customer->state) ? '' : 'selected'); ?>>Select State</option>
                                    <option value="Gujarat"
                                        <?php echo e(old('state', $customer->state) == 'Gujarat' ? 'selected' : ''); ?>>Gujarat
                                    </option>
                                    
                                </select>
                            </div>
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                        class="text-red-500">*</span></label>
                                <textarea name="address" id="address" rows="2" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm"><?php echo e(old('address', $customer->address ?? '')); ?></textarea>
                            </div>
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of
                                    Birth <span class="text-red-500">*</span></label>
                                <input type="date" name="date_of_birth" id="date_of_birth"
                                    value="<?php echo e(old('date_of_birth', $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '')); ?>"
                                    required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                            </div>
                            <div>
                                <label for="place_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Place of
                                    Birth <span class="text-red-500">*</span></label>
                                <input type="text" name="place_of_birth" id="place_of_birth"
                                    value="<?php echo e(old('place_of_birth', $customer->place_of_birth ?? '')); ?>" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                            </div>
                        </div>
                        
                        <div class="pt-6 mt-4 border-t border-gray-200 flex justify-end">
                            <button type="submit" class="btn-primary px-8 py-2.5">SAVE</button>
                        </div>
                    </form>
                </div>
            </div>

            
            <div x-show="activeTab === 'documents'" x-cloak>
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 space-y-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Uploaded Documents</h2>

                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 mb-4">Current Documents</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php $__empty_1 = true; $__currentLoopData = $customer->applicationDocuments ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div
                                    class="border border-gray-200 rounded-lg p-4 bg-gray-50 flex flex-col justify-between">
                                    <div class="flex items-center mb-3">
                                        <?php if(pathinfo($document->file_path, PATHINFO_EXTENSION) == 'pdf'): ?>
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-8 w-8 mr-3 text-gray-500 flex-shrink-0" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        <?php else: ?>
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-8 w-8 mr-3 text-gray-500 flex-shrink-0" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        <?php endif; ?>
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm break-all">
                                                <?php echo e($document->documentType->name); ?></p>
                                            <p class="text-xs text-gray-500 mt-0.5">Uploaded:
                                                <?php echo e($document->created_at->format('Y-m-d')); ?></p>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                                        <a href="<?php echo e(Storage::url($document->file_path)); ?>" target="_blank"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            View
                                        </a>
                                        
                                        <form id="delete-document-<?php echo e($document->id); ?>"
                                            action="<?php echo e(route('admin.application-documents.destroy', $document->id)); ?>"
                                            method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="button"
                                                onclick="confirmDelete('<?php echo e($document->documentType->name); ?>', this.form)"
                                                class="text-red-500 hover:text-red-700 text-sm font-medium inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <p class="text-gray-500 text-sm sm:col-span-2 lg:col-span-3">No documents have been
                                    uploaded yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 mb-4">Required Documents Checklist</h3>
                        <?php
                            $documentTypes = \App\Models\DocumentType::all();
                            $uploadedDocumentIds = $customer->applicationDocuments
                                ? $customer->applicationDocuments->pluck('document_type_id')->toArray()
                                : [];
                        ?>

                        <ul class="space-y-2 text-sm">
                            <?php $__empty_1 = true; $__currentLoopData = $documentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $docType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $isUploaded = in_array($docType->id, $uploadedDocumentIds);

                                    if ($isUploaded) {
                                        $textColorClass = 'text-green-600';
                                        $iconPath =
                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                                        $statusText = 'Uploaded';
                                    } elseif ($docType->is_mandatory) {
                                        $textColorClass = 'text-red-600';
                                        $iconPath =
                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                                        $statusText = 'Pending';
                                    } else {
                                        $textColorClass = 'text-amber-600';
                                        $iconPath =
                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                                        $statusText = 'Optional';
                                    }
                                ?>
                                <li class="flex items-center <?php echo e($textColorClass); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <?php echo $iconPath; ?>

                                    </svg>
                                    <span class="<?php echo e(!$isUploaded && $docType->is_mandatory ? 'font-medium' : ''); ?>">
                                        <?php echo e($docType->name); ?> (<?php echo e($statusText); ?>)
                                    </span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <li class="text-gray-500">No document types defined in the system.</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 mb-4">Upload New Document (Staff)</h3>
                        <form action="<?php echo e(route('admin.application-documents.store')); ?>" method="POST"
                            enctype="multipart/form-data"
                            class="border border-gray-200 rounded-lg p-5 bg-gray-50 space-y-4">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="customer_id" value="<?php echo e($customer->id); ?>">
                            <input type="hidden" name="redirect"
                                value="<?php echo e(route('admin.customers.show', $customer->id)); ?>">

                            <div>
                                <label for="document_type_id"
                                    class="block text-sm font-medium text-gray-700 mb-1">Document
                                    Type <span class="text-red-500">*</span></label>
                                <select name="document_type_id" id="document_type_id" required
                                    class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm">
                                    <option value="" disabled selected>Select document type</option>
                                    <?php $__currentLoopData = $documentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $docType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($docType->id); ?>"
                                            <?php echo e(in_array($docType->id, $uploadedDocumentIds) ? 'disabled' : ''); ?>>
                                            <?php echo e($docType->name); ?>

                                            <?php echo e($docType->is_mandatory ? '(Required)' : '(Optional)'); ?>

                                            <?php echo e(in_array($docType->id, $uploadedDocumentIds) ? '- Already Uploaded' : ''); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div>
                                <label for="document_file" class="block text-sm font-medium text-gray-700 mb-1">Select
                                    File <span class="text-red-500">*</span></label>
                                <input type="file" name="document_file" id="document_file" required
                                    class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG. Max size: 5MB.</p>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="btn-primary inline-flex items-center px-5 py-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Upload Document
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>           

            
            <div 
                x-show="activeTab === 'application-process'" 
                x-cloak 
                x-data="applicationProcessComponent()"
            >
            <script>
                function applicationProcessComponent() {
                    return {
                        predefinedMessages: <?php echo json_encode(\App\Models\PreDefinedMessage::all(), 15, 512) ?>,
                        showRemarkForm: false,
                        selectedMessage: '',
                        selectedStatus: '',
                        showFileUpload: false,
                        showAppointmentFields: false,
                        remarks: '',
                        updateRemarks() {
                            if (this.selectedStatus) {
                                // Find the predefined message where message_name matches selectedStatus (case-insensitive)
                                const matched = this.predefinedMessages.find(
                                    msg => msg.message_name && msg.message_name.toLowerCase() === this.selectedStatus.toLowerCase()
                                );
                                if (matched) {
                                    this.remarks = matched.message_remarks;
                                } else {
                                    this.remarks = "";
                                }
                            }
                        },
                        updateFileUpload() {
                            this.showFileUpload = [
                                'details_verification', 
                                'appointment_scheduled', 
                                'appointment_rescheduled1', 
                                'appointment_rescheduled2', 
                                'appointment_rescheduled3'
                            ].includes(this.selectedStatus);
                            this.showAppointmentFields = [
                                'appointment_scheduled', 
                                'appointment_rescheduled1', 
                                'appointment_rescheduled2', 
                                'appointment_rescheduled3'
                            ].includes(this.selectedStatus);
                        }
                    }
                }
            </script>

                
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Application Process</h2>
                        
                        <button type="button" @click="showRemarkForm = !showRemarkForm"
                            class="btn-primary inline-flex items-center px-4 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <span x-text="showRemarkForm ? 'Cancel' : 'Send Remark'">Send Remark</span>
                        </button>
                    </div>

                    
                    <div x-show="showRemarkForm" x-cloak x-transition
                        class="border border-gray-200 rounded-lg p-5 mb-6 bg-gray-50">
                        <form action="<?php echo e(route('admin.application-progress.store')); ?>" method="POST"
                            enctype="multipart/form-data" class="space-y-5">
                            <?php echo csrf_field(); ?>
                            
                            <input type="hidden" name="customer_id" value="<?php echo e($customer->id); ?>">
                            <input type="hidden" name="redirect"
                                value="<?php echo e(route('admin.customers.show', $customer->id)); ?>#application-process">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                                <div>
                                    <label for="application_status"
                                        class="block text-sm font-medium text-gray-700 mb-1">Application Status <span
                                            class="text-red-500">*</span></label>
                                    <select name="application_status" id="application_status" required
                                        x-model="selectedStatus" @change="updateFileUpload(); updateRemarks()"
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 pr-10 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 sm:text-sm">
                                        <option value="" disabled selected>Select Application Status</option>
                                        <option value="in_process">In Process</option>
                                        <option value="documents_submitted">Documents Submitted</option>
                                        <option value="details_verification">Details Verification</option>
                                        <option value="appointment_scheduled">Appointment Scheduled</option>
                                        <option value="pov_success">POV Success</option>
                                        <option value="pov_failed">POV Failed</option>
                                        <option value="pov_insufficient_documents">POV Insufficient Documents</option>
                                        <option value="appointment_rescheduled1">Appointment Rescheduled1</option>
                                        <option value="appointment_rescheduled2">Appointment Rescheduled2</option>
                                        <option value="appointment_rescheduled3">Appointment Rescheduled3</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="status_date" class="block text-sm font-medium text-gray-700 mb-1">Status
                                        Date <span class="text-red-500">*</span></label>
                                    <input type="date" name="status_date" id="status_date" required
                                        value="<?php echo e(date('Y-m-d')); ?>"
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                </div>

                                
                                <div x-show="showFileUpload || showAppointmentFields" class="md:col-span-2">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload File <span
                                                    x-show="showFileUpload" class="text-red-500">*</span></label>
                                            <input type="file" name="file" id="file"
                                                :required="showFileUpload"
                                                class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                            <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG. Max size: 5MB.</p>
                                        </div>

                                        <div x-show="showAppointmentFields">
                                            <label for="appointment_date"
                                                class="block text-sm font-medium text-gray-700 mb-1">Appointment Date <span
                                                    class="text-red-500">*</span></label>
                                            <input type="date" name="appointment_date" id="appointment_date"
                                                :required="showAppointmentFields"
                                                class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                        </div>

                                        <div x-show="showAppointmentFields">
                                            <label for="appointment_time"
                                                class="block text-sm font-medium text-gray-700 mb-1">Appointment Time <span
                                                    class="text-red-500">*</span></label>
                                            <input type="time" name="appointment_time" id="appointment_time"
                                                :required="showAppointmentFields"
                                                class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm">
                                        </div>
                                    </div>
                                </div>

                                

                                <div class="md:col-span-2">
                                    <label for="remark" class="block text-sm font-medium text-gray-700 mb-1">Remarks
                                        <span class="text-red-500">*</span></label>
                                    <textarea name="remark" id="remark" rows="4" required placeholder="Enter remarks..." x-model="remarks"
                                        class="block w-full rounded-lg border-2 border-gray-200 bg-white shadow-sm py-2 px-3 hover:border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 placeholder-gray-400 sm:text-sm"></textarea>
                                </div>
                            </div>

                            
                            <div class="pt-5 mt-4 border-t border-gray-200 flex justify-end gap-3">
                                <button type="button" @click="showRemarkForm = false; selectedMessage = ''; remarks = ''"
                                    class="btn-secondary px-6 py-2">CANCEL</button>
                                <button type="submit" class="btn-primary px-8 py-2">ADD</button>
                            </div>
                        </form>
                    </div>

                    
                    <div class="overflow-x-auto">
                        <table
                            class="w-full text-sm text-left text-gray-600 border border-gray-200 divide-y divide-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Date</th>
                                    <th scope="col" class="px-6 py-3">Remark</th>
                                    <th scope="col" class="px-6 py-3">File</th>
                                    <th scope="col" class="px-6 py-3">Staff Name</th>
                                    <th scope="col" class="px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $customer->applicationProgress()->orderBy('created_at', 'desc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $progress): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?php if(in_array($progress->application_status, ['pov_success', 'documents_submitted'])): ?> bg-green-100 text-green-800
                                            <?php elseif(in_array($progress->application_status, ['pov_failed', 'rejected'])): ?>
                                                bg-red-100 text-red-800
                                            <?php elseif(in_array($progress->application_status, ['in_process', 'details_verification', 'appointment_scheduled'])): ?>
                                                bg-blue-100 text-blue-800
                                            <?php elseif($progress->application_status == 'pov_insufficient_documents' || $progress->application_status == 'rescheduled'): ?>
                                                bg-orange-100 text-orange-800
                                            <?php else: ?>
                                                bg-gray-100 text-gray-800 <?php endif; ?>
                                        ">
                                                <?php echo e(str_replace('_', ' ', ucfirst($progress->application_status))); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php echo e($progress->status_date ? $progress->status_date->format('Y-m-d') : 'N/A'); ?>

                                        </td>
                                        <td class="px-6 py-4"><?php echo e($progress->remark); ?></td>
                                        <td class="px-6 py-4">
                                            <?php if($progress->relatedFile): ?>
                                                <a href="<?php echo e(Storage::url($progress->relatedFile->file_path)); ?>"
                                                    target="_blank"
                                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                    View File
                                                </a>
                                            <?php else: ?>
                                                <span class="text-gray-500 text-sm">No file</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php echo e($progress->remarkedByUser ? $progress->remarkedByUser->name . ' (' . ucfirst($progress->remarkedByUser->role) . ')' : 'System'); ?>

                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <?php if(auth()->user()->role === 'admin' || auth()->user()->id === $progress->remarked_by): ?>
                                                <form
                                                    action="<?php echo e(route('admin.application-progress.destroy', $progress->id)); ?>"
                                                    method="POST" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <input type="hidden" name="redirect"
                                                        value="<?php echo e(route('admin.customers.show', $customer->id)); ?>#application-process">
                                                    <button type="button"
                                                        onclick="confirmDelete('<?php echo e(str_replace('_', ' ', ucfirst($progress->application_status))); ?> remark', this.form)"
                                                        class="text-red-600 hover:text-red-900 font-medium">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No remarks found
                                            for this customer.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div x-show="activeTab === 'actions'" x-cloak>
                
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 space-y-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-0">Account Actions</h2>

                    
                    <div class="pt-6 border-t border-gray-200">
                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                            <div class="flex flex-col md:flex-row justify-between md:items-center">
                                <div class="mb-3 md:mb-0">
                                    <h3 class="text-lg font-medium text-red-800 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Deactivate Account
                                    </h3>
                                    <p class="text-sm text-red-700 mt-1 max-w-xl pl-7">
                                        Prevent the user from logging in. This action requires confirmation and should be
                                        used with caution.
                                    </p>
                                </div>
                                <form id="deactivateForm" action="#" method="POST"
                                    class="flex-shrink-0 mt-3 md:mt-0 md:ml-4">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="button" onclick="confirmDeactivate()"
                                        class="inline-flex items-center text-sm px-4 py-2 w-full md:w-auto 
                                        bg-red-600
                                               font-medium rounded-lg border border-white text-white
                                               hover:bg-red-500
                                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 
                                               transition ease-in-out duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        Deactivate Account
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Event handlers can be added here if needed
        });

        // Function to confirm document deletion using SweetAlert2
        function confirmDelete(documentName, form) {
            Swal.fire({
                title: 'Delete Document?',
                text: `You are about to delete "${documentName}". This action cannot be undone.`,
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
                    form.submit();
                }
            });
        }

        // Function to confirm account deactivation using SweetAlert2
        function confirmDeactivate() {
            Swal.fire({
                title: 'Deactivate Account?',
                text: 'You are about to deactivate this customer account. This action will prevent the user from logging in and cannot be easily undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, deactivate it!',
                customClass: {
                    popup: 'rounded-lg shadow-lg',
                    title: 'text-lg font-semibold text-gray-800',
                    confirmButton: 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 mx-1',
                    cancelButton: 'px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 mx-1'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deactivateForm').submit();
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/laravel-app/releases/11/resources/views/admin/customers/show.blade.php ENDPATH**/ ?>