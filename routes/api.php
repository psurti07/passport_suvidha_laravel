<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\SupportTicketController;
use App\Http\Controllers\Api\AppointmentLetterController;
use App\Http\Controllers\Api\ApplicationProgressController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\FinalDetailController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\OfferOrderController;
use App\Http\Controllers\Api\RequiredDocumentsController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ZaakpayController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Health check route
Route::get('/health', [HealthController::class, 'check'])->name('api.health');

Route::middleware('auth:sanctum')->post('/logout', [CustomerController::class, 'logout']);

Route::middleware('auth:sanctum')->get('/profile', function (Request $request) {
    return $request->user();
});

// STEP-1 : Customer routes 
Route::post('/customers/create', [CustomerController::class, 'create'])->name('api.customers.create');

// Customer login route
Route::post('/customers/login', [CustomerController::class, 'login'])->name('api.customers.login');
Route::middleware('auth:sanctum')
     ->put('/customers/{customer}', [CustomerController::class, 'update']);

// OTP 
// STEP-1 : OTP Send
Route::post('/otp/send', [OtpController::class, 'send']);
// STEP-2 : OTP verification
Route::post('/otp/verify', [OtpController::class, 'verify']);

// Support Ticket Routes for guest/customer
Route::post('/support/tickets', [SupportTicketController::class, 'store'])->name('api.support.tickets.store'); // For guest/customer ticket creation

Route::middleware('auth:customer')->group(function () { // Add routes requiring authentication here

    // -- Support Ticket Routes for authenticated customer --
    // Support Ticket Routes for authenticated customer
    Route::get('/support/tickets', [SupportTicketController::class, 'index'])->name('api.support.tickets.index'); // For authenticated customer to view their tickets
    // Support Ticket Routes for authenticated customer to view a specific ticket
    Route::get('/support/tickets/{ticket_number}', [SupportTicketController::class, 'show'])->name('api.support.tickets.show');

    // -- Customer registration flow --
    // STEP-3 additional-info
    Route::post('/customer/additional-info', [CustomerController::class, 'addAdditionalInfo'])->name('api.customers.additional-info');
    // STEP-4 Select Passport type
    Route::post('/customer/select-service', [CustomerController::class, 'selectService'])->name('api.customers.select-service');
    // STEP-5 Payment Integration - pending
    // Route::post('/customer/payment', [CustomerController::class, 'payment'])->name('api.customers.payment');    


    // Application Progress Route - only the customer status endpoint
    // Route::get('/application-progress', [ApplicationProgressController::class, 'getCustomerApplicationStatus'])->name('api.application-progress.status');

    Route::middleware('auth:sanctum')->get(
    '/application-progress',
    [ApplicationProgressController::class, 'getCustomerApplicationStatus']
);

    // Application Review Routes
    Route::get('/application-review/summary', [FinalDetailController::class, 'getApplicationSummary'])->name('api.application-review.summary');
    Route::get('/application-review/preview', [FinalDetailController::class, 'preview'])->name('api.application-review.preview');
    Route::get('/application-review/download', [FinalDetailController::class, 'download'])->name('api.application-review.download');
    Route::post('/application-review/verify', [FinalDetailController::class, 'verifyApplication'])->name('api.application-review.verify');

    // Appointment Letter Routes     
    Route::prefix('appointment-letters')->group(function () {
        Route::get('/', [AppointmentLetterController::class, 'listUserLetters'])->name('api.appointment-letters.list');
        Route::get('/download/{id}', [AppointmentLetterController::class, 'downloadById'])->name('api.appointment-letters.download-by-id');
        Route::get('/download', [AppointmentLetterController::class, 'download'])->name('api.appointment-letters.download');
    });

    // Required Documents Routes
    Route::prefix('required-documents')->group(function () {
        Route::get('/', [RequiredDocumentsController::class, 'index'])->name('api.required-documents.index');
        Route::post('/upload/{document_type_id}', [RequiredDocumentsController::class, 'upload'])->name('api.required-documents.upload');
        Route::get('/download/{document_type_id}', [RequiredDocumentsController::class, 'download'])->name('api.required-documents.download');
        Route::delete('/{document_type_id}', [RequiredDocumentsController::class, 'delete'])->name('api.required-documents.delete');
    });
});

// support ticket route for public (no auth) - separate from authenticated routes to avoid confusion
Route::post('/public/support/tickets', [SupportTicketController::class, 'storePublic']);

Route::get('/services/passport', [ServiceController::class, 'passportServices']);

// routes/api.php
Route::middleware('auth:sanctum')->get('/application/details', [ApplicationProgressController::class, 'details']);
Route::get('/invoice/{customer_id}', [InvoiceController::class, 'generateInvoice']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/create-order', [PaymentController::class, 'createOrder']);
    Route::post('/verify-payment', [PaymentController::class, 'verifyPayment']);
});

// Route::post('/createOffer-order', [OfferOrderController::class, 'createOrder']);
// Route::get('/payment-success', [OfferOrderController::class, 'paymentSuccess']);
// Route::get('/check-payment-status', [OfferOrderController::class, 'checkPaymentStatus']);

Route::post('/create-payment', [OfferOrderController::class, 'createPayment']);
Route::post('/cashfree/webhook', [OfferOrderController::class, 'cashfreeWebhook'])->name('cashfree.webhook');
Route::post('/zaakpay/callback', [OfferOrderController::class, 'zaakpayCallback'])->name('zaakpay.callback');
Route::get('/check-payment-status', [OfferOrderController::class, 'checkPaymentStatus']);
Route::post('/mark-payment-failed', [OfferOrderController::class, 'markPaymentFailed']);
