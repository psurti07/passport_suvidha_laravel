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
use App\Http\Controllers\Api\SiteOptionController;
// use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\MetaKeywordsController;
use App\Http\Controllers\Api\SchedualSlotController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

Route::post('/customers/create', [CustomerController::class, 'create'])->name('api.customers.create');
Route::post('/customers/login', [CustomerController::class, 'login'])->name('api.customers.login');
Route::middleware('auth:sanctum')
    ->put('/customers/{customer}', [CustomerController::class, 'update']);

Route::post('/otp/send', [OtpController::class, 'send']);
Route::post('/otp/verify', [OtpController::class, 'verify']);

Route::post('/support/tickets', [SupportTicketController::class, 'store'])->name('api.support.tickets.store'); // For guest/customer ticket creation

Route::middleware('auth:customer')->group(function () { // Add routes requiring authentication here

    Route::get('/support/tickets', [SupportTicketController::class, 'index'])->name('api.support.tickets.index'); // For authenticated customer to view their tickets
    Route::get('/support/tickets/{ticket_number}', [SupportTicketController::class, 'show'])->name('api.support.tickets.show');

    Route::post('/customer/additional-info', [CustomerController::class, 'addAdditionalInfo'])->name('api.customers.additional-info');
    Route::post('/customer/select-service', [CustomerController::class, 'selectService'])->name('api.customers.select-service');

    Route::prefix('application-review')->group(function () {
        Route::get('/summary', [FinalDetailController::class, 'getApplicationSummary'])->name('api.application-review.summary');
        Route::get('/preview', [FinalDetailController::class, 'preview'])->name('api.application-review.preview');
        Route::get('/download', [FinalDetailController::class, 'download'])->name('api.application-review.download');
        Route::post('/verify', [FinalDetailController::class, 'verifyApplication'])->name('api.application-review.verify');
    });

    Route::get('/application-progress/status', [ApplicationProgressController::class, 'getApplicationProgress']);

    Route::prefix('appointment-letters')->group(function () {
        Route::get('/', [AppointmentLetterController::class, 'listUserLetters'])->name('api.appointment-letters.list');
        Route::get('/download/{id}', [AppointmentLetterController::class, 'downloadById'])->name('api.appointment-letters.download-by-id');
        Route::get('/download', [AppointmentLetterController::class, 'download'])->name('api.appointment-letters.download');
        Route::get('/preview', [AppointmentLetterController::class, 'preview'])->name('api.appointment-letters.preview');
    });

    Route::prefix('required-documents')->group(function () {
        Route::get('/', [RequiredDocumentsController::class, 'index'])->name('api.required-documents.index');
        Route::post('/upload/{document_type_id}', [RequiredDocumentsController::class, 'upload'])->name('api.required-documents.upload');
        Route::get('/download/{document_type_id}', [RequiredDocumentsController::class, 'download'])->name('api.required-documents.download');
        Route::delete('/{document_type_id}', [RequiredDocumentsController::class, 'delete'])->name('api.required-documents.delete');
    });
});

Route::post('/check-status-by-mobile', [ApplicationProgressController::class, 'getStatusByMobile'])->name('api.application-progress.status-by-mobile');

Route::middleware('auth:sanctum')->get(
    '/application-progress',
    [ApplicationProgressController::class, 'getCustomerApplicationStatus']
);

Route::post('/public/support/tickets', [SupportTicketController::class, 'storePublic']);
Route::get('/services/passport', [ServiceController::class, 'passportServices']);

// routes/api.php
Route::middleware('auth:sanctum')->get('/application/details', [ApplicationProgressController::class, 'details']);
Route::get('/invoice/{customer_id}', [InvoiceController::class, 'generateInvoice']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/create-order', [PaymentController::class, 'createOrder']);
    Route::post('/verify-payment', [PaymentController::class, 'verifyPayment']);
    Route::post('/payment-failed', [PaymentController::class, 'paymentFailed']);
});

// offer page payment routes
Route::post('/create-payment', [OfferOrderController::class, 'createPayment']);
Route::post('/cashfree/webhook', [OfferOrderController::class, 'cashfreeWebhook'])->name('cashfree.webhook');
Route::get('/check-payment-status', [OfferOrderController::class, 'checkPaymentStatus']);
Route::post('/mark-payment-failed', [OfferOrderController::class, 'markPaymentFailed']);
Route::post('/payment-cancelled', [OfferOrderController::class, 'paymentCancelled']);
Route::get('/cardoffer-response', [OfferOrderController::class, 'paymentResponse']);

// welcome message routes
Route::get('/welcome-message', [SiteOptionController::class, 'getWelcomeMessage']);

// SEO Meta keywords routes
Route::get('/seo/{slug}', [MetaKeywordsController::class, 'show']);

// locations
// Route::get('/locations', [LocationController::class, 'getPoliceStations']);

// Schedule slot routes
Route::get('/schedule-slot', [SchedualSlotController::class, 'getScheduleDetails']);
Route::get('/schedule-success', [SchedualSlotController::class, 'scheduleSuccess']);
Route::get('/schedule-cancel', [SchedualSlotController::class, 'scheduleCancle']);
Route::post('/schedule-slot', [SchedualSlotController::class, 'scheduleSlot']);
Route::get('/encrypt', [SchedualSlotController::class, 'encryptId']);
Route::get('/decrypt', [SchedualSlotController::class, 'decryptId']);

// Fb code setup 
Route::get('/fb-pixel', [SiteOptionController::class, 'getFbPixel']);

// PhonePe routes
Route::post('/check-phonepe-status', [OfferOrderController::class, 'checkPhonepeStatus']);
Route::match(['GET', 'POST'], '/phonepe/redirect', [OfferOrderController::class, 'phonepeRedirect'])->name('phonepe.redirect');
