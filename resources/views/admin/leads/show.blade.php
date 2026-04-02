@extends('layouts.app')

@section('title', 'Normal Lead')

@section('content')
{{-- Using container structure similar to index pages, but maybe less padding --}}
<div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Page Header - Adapted from index --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
         {{-- Updated Title Style --}}
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
            NORMAL LEAD DETAILS
        </h1>
        <div class="flex items-center space-x-3 flex-shrink-0">
            {{-- Back Button - Style slightly adjusted for consistency --}}
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back
            </a>
        </div>
    </div>

    {{-- Display Success Messages --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    {{-- Display Validation Errors (Optional but Recommended) --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Oops! Something went wrong.</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:items-start"> 
        {{-- Left Panel: Customer Information --}}
        <div class="lg:col-span-1 bg-white rounded-xl shadow-lg border border-gray-100 p-6 space-y-6">
            <div>
                <h2 class="text-lg font-semibold text-blue-700 border-b border-gray-200 pb-3 mb-4">Customer Information</h2>
                <dl class="space-y-4">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">First Name</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->first_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Last Name</dt>
                        <dd class="text-sm text-gray-900">{{ $customer->last_name }}</dd>
                    </div>
                     <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Mobile Number</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->mobile_number  }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->email }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->address }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Pincode</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->pin_code }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">City</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->city }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">State</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->state }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Gender</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->gender }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Date Of Birth</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->date_of_birth }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Place Of Birth</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->place_of_birth }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Nationality</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->nationality }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Payment Info Id</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->payment_info_id }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Service Code</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->service_code }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Passport Type</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->passport_type }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Book Size</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $customer->book_size }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Right Panel: Convert to Customer --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-100 p-6 space-y-6">
            <div>
                <h2 class="text-lg font-semibold text-blue-700">Convert to Customer</h2>
            </div>

            <div>
                <h3 class="text-md font-medium text-gray-700 mb-3 border-t pt-4"></h3> 

                {{-- Add Convert to Customer Form --}}
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                <script>
                 $(document).ready(function() {
                     $('#convert-customer-form').on('submit', function(e) {
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

                @if(false)
                 <form id="add-remark-form" action="{{ route('admin.support.tickets.remarks.store', $ticket->ticket_number) }}" method="POST" class="mt-4"> {{-- Added margin-top --}}
                     @csrf
                     <div>
                         <label for="remark" class="sr-only">Add Remark</label>
                         <textarea id="remark" name="remark" rows="3" required minlength="5"
                                   class="block w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-150 ease-in-out @error('remark') border-red-500 ring-red-500 @enderror"
                                   placeholder="Enter remark here...">{{ old('remark') }}</textarea>
                         @error('remark')
                            {{-- This error display might be redundant if handled by AJAX error message --}}
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                         @enderror
                     </div>
                     <div class="mt-3 flex justify-end">
                         <button type="submit"
                                 class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                             Add Remark
                         </button>
                     </div>
                 </form>
                 @endif
            </div>
        </div>
    </div>
</div>
@endsection 