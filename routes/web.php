<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TodayStatisticsController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PincodeLocationController;
use App\Http\Controllers\Admin\ApplicationDocumentController;
use App\Http\Controllers\Admin\ApplicationProgressController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\OtpController;
use App\Http\Controllers\Admin\FinalDetailController;
use App\Http\Controllers\Admin\AppointmentLetterController;
use App\Http\Controllers\Admin\PreDefinedMessageController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ApplicationStatusController;
use App\Http\Controllers\Admin\DndController;
use App\Http\Controllers\Admin\RazorpayLogController;
use App\Http\Controllers\Admin\CashfreeLogController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ZaakpayLogController;
use App\Http\Controllers\Admin\CardOfferController;
use App\Http\Controllers\Admin\StarOfferController;
use App\Http\Controllers\Admin\SiteOptionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirect the root path to the admin dashboard
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
}); // No middleware needed here, the target route handles auth.

// Authentication Routes
Auth::routes();

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Common Routes
    Route::post('/pincode-location', [PincodeLocationController::class, 'getPincodeLocation'])->name('pincode.location');

    // Dashboard Routes
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Statistics Routes
    Route::get('/todaystatistics', [TodayStatisticsController::class, 'index'])->name('todaystatistics');

    // Customers Routes
    Route::get('/search-customer', [SearchController::class, 'showSearchForm'])->name('customer.search.form');
    Route::post('/search-customer', [SearchController::class, 'searchCustomer'])->name('customer.search');
    Route::get('/leads/today', [LeadController::class, 'today'])->name('leads.today');
    Route::get('/leads/today/data', [LeadController::class, 'todayData'])->name('leads.today.data');
    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads-data', [LeadController::class, 'data'])->name('leads.data');
    Route::get('/customers/today', [CustomerController::class, 'today'])->name('customers.today');
    Route::get('/customers/today/data', [CustomerController::class, 'todayData'])->name('customers.today.data');
    Route::resource('customers', CustomerController::class);
    Route::get('/customers-data', [CustomerController::class, 'data'])->name('customers.data');
    Route::put('/customers/{customer}/convert', [CustomerController::class, 'convertToCustomer'])->name('customers.convert');
    Route::patch('customers/{customer}/activate', [CustomerController::class, 'activate'])->name('customers.activate');
    Route::patch('customers/{customer}/deactivate', [CustomerController::class, 'deactivate'])->name('customers.deactivate');

    // Application Documents Routes
    Route::resource('application-documents', ApplicationDocumentController::class);
    Route::get('/application-documents-data', [ApplicationDocumentController::class, 'data'])->name('application-documents.data');
    Route::post('/documents/update-all', [ApplicationDocumentController::class, 'updateAll'])->name('documents.updateAll');
    Route::get('/documents/toggle/{id}', [ApplicationDocumentController::class, 'toggleVerify'])->name('documents.toggleVerify');

    // Final Details Routes
    Route::patch('final-details/{finalDetail}/approve', [FinalDetailController::class, 'approve'])->name('final-details.approve');
    Route::patch('final-details/{finalDetail}/unapprove', [FinalDetailController::class, 'unapprove'])->name('final-details.unapprove');
    Route::resource('final-details', FinalDetailController::class)->except(['create', 'store', 'destroy']);
    Route::get('/final-details-data', [FinalDetailController::class, 'data'])->name('final-details.data');

    // Appointment Letter Routes
    Route::get('appointment-letters/{appointmentLetter}/download', [AppointmentLetterController::class, 'download'])->name('appointment-letters.download');
    Route::get('appointment-letters/{appointmentLetter}/preview', [AppointmentLetterController::class, 'preview'])->name('appointment-letters.preview');
    Route::resource('appointment-letters', AppointmentLetterController::class);
    Route::get('/appointment-letters-data', [AppointmentLetterController::class, 'data'])->name('appointment-letters.data');

    // Application Status Routes
    Route::get('/application-status', [ApplicationStatusController::class, 'index'])->name('application.status');
    Route::get('/application-status/data', [ApplicationStatusController::class, 'data'])->name('application.status.data');
    Route::get('/application-status/new', [ApplicationStatusController::class, 'new'])->name('application.status.new');
    Route::get('/application-status/current', [ApplicationStatusController::class, 'current'])->name('application.status.current');
    Route::get('/application-status/completed', [ApplicationStatusController::class, 'completed'])->name('application.status.completed');

    // Otp Routes
    Route::get('otps', [OtpController::class, 'index'])->name('otps.index');
    Route::get('/otps-data', [OtpController::class, 'data'])->name('otps.data');

    // Invoice Routes
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices-data', [InvoiceController::class, 'data'])->name('invoices.data');
    Route::get('invoices/download/{invoice_id}', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::get('/gst-reports', [InvoiceController::class, 'gstIndex'])->name('gst.index');
    Route::get('/gst-reports/data', [InvoiceController::class, 'gstData'])->name('gst.data');

    // DND Routes
    Route::get('dnd', [DndController::class, 'index'])->name('dnd.index');
    Route::get('/dnd-data', [DndController::class, 'data'])->name('dnd.data');
    Route::post('dnd/upload', [DndController::class, 'upload'])->name('dnd.upload');
    Route::delete('dnd/{customer}/delete', [DndController::class, 'destroy'])->name('dnd.destroy');

    // Card Offer Routes
    Route::get('card-offers', [CardOfferController::class, 'index'])->name('card-offers.index');
    Route::get('/card-offers-data', [CardOfferController::class, 'data'])->name('card-offers.data');
    Route::patch('card-offers/{cardOffer}/customer', [CardOfferController::class, 'customer'])->name('card-offers.customer');
    Route::patch('card-offers/{cardOffer}/lead', [CardOfferController::class, 'lead'])->name('card-offers.lead');

    // Star Offers Routes
    Route::get('star-offers', [StarOfferController::class, 'index'])->name('star-offers.index');
    Route::get('/star-offers-data', [StarOfferController::class, 'data'])->name('star-offers.data');
    Route::patch('star-offers/{starOffer}/customer', [StarOfferController::class, 'customer'])->name('star-offers.customer');
    Route::patch('star-offers/{starOffer}/lead', [StarOfferController::class, 'lead'])->name('star-offers.lead');

    // Razorpay Logs Routes
    Route::get('/razorpay-logs', [RazorpayLogController::class, 'index'])->name('razorpay-logs.index');
    Route::get('/razorpay-logs/data', [RazorpayLogController::class, 'data'])->name('razorpay-logs.data');

    // Cashfree Logs Routes
    Route::get('/cashfree-logs', [CashfreeLogController::class, 'index'])->name('cashfree-logs.index');
    Route::get('/cashfree-logs/data', [CashfreeLogController::class, 'data'])->name('cashfree-logs.data');

    //Zaakpay Logs Routes
    Route::get('/zaakpay-logs', [ZaakpayLogController::class, 'index'])->name('zaakpay-logs.index');
    Route::get('/zaakpay-logs/data', [ZaakpayLogController::class, 'data'])->name('zaakpay-logs.data');

    // Support Routes
    Route::get('/support/customer', [SupportController::class, 'customerSupport'])->name('support.customer');
    Route::get('/support/customer/data', [SupportController::class, 'customerSupportData'])->name('support.customer.data');
    Route::get('/support/guest', [SupportController::class, 'guestSupport'])->name('support.guest');
    Route::get('/support/guest/data', [SupportController::class, 'guestSupportData'])->name('support.guest.data');
    Route::get('/support/tickets/{ticket}', [SupportController::class, 'show'])->name('support.tickets.show');
    Route::post('/support/tickets/{ticket}/remarks', [SupportController::class, 'storeRemark'])->name('support.tickets.remarks.store');
    Route::patch('/support/tickets/{ticket}/status', [SupportController::class, 'updateStatus'])->name('support.tickets.status.update');

    // Predefined Messages Routes
    Route::resource('predefined-messages', PreDefinedMessageController::class);
    Route::get('/predefined-messages-data', [PreDefinedMessageController::class, 'data'])->name('predefined-messages.data');

    // Document Types Routes
    Route::resource('document-types', DocumentTypeController::class);
    Route::get('/document-types-data', [DocumentTypeController::class, 'data'])->name('document-types.data');

    // Site Options Routes
    Route::get('/site-options', [SiteOptionController::class, 'index'])->name('site-options');
    Route::post('/site-options/update', [SiteOptionController::class, 'update'])->name('site-options.update');
    
    // Users Routes
    Route::resource('users', UserController::class);
    Route::get('/users-data', [UserController::class, 'data'])->name('users.data');




    


    // Application Progress Routes
    Route::get('customers/{customer}/application-progress', [ApplicationProgressController::class, 'customerHistory'])
        ->name('application-progress.customer-history');
    Route::resource('application-progress', ApplicationProgressController::class);

    // Leads Routes
    Route::match(['get', 'post'], '/leads/normal', [LeadController::class, 'normalLeads'])->name('leads.normal');
    Route::match(['get', 'post'], '/leads/tatkal', [LeadController::class, 'tatkalLeads'])->name('leads.tatkal');
    Route::get('/lead/{customer}', [LeadController::class, 'show'])->name('lead.show');

    // Report Routes
    Route::get('/reports/gst', [ReportController::class, 'gstReport'])->name('reports.gst');
});
