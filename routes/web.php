<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TodayStatisticsController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CommonController;
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

    // Dashboard Routes
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/tatkal', [AdminController::class, 'tatkalDashboard'])->name('dashboard.tatkal');

    // Statistics Routes
    Route::get('/todaystatistics', [TodayStatisticsController::class, 'index'])->name('todaystatistics');

    // Customers Routes
    Route::get('/search-customer', [SearchController::class, 'showSearchForm'])->name('customer.search.form');
    Route::post('/search-customer', [SearchController::class, 'searchCustomer'])->name('customer.search');
    Route::get('/customers/today', [CustomerController::class, 'today'])->name('customers.today');
    Route::get('/customers/today/data', [CustomerController::class, 'todayData'])->name('customers.today.data');
    Route::resource('customers', CustomerController::class);
    Route::get('/customers-data', [CustomerController::class,'data'])->name('customers.data');
    Route::put('/customers/{customer}/convert', [CustomerController::class, 'convertToCustomer'])->name('customers.convert');
    Route::post('/pincode-location', [CommonController::class, 'getPincodeLocation'])->name('pincode.location');

    // Application Documents Routes
    Route::resource('application-documents', ApplicationDocumentController::class)->only(['store', 'destroy']);
    Route::post('/documents/update-all', [ApplicationDocumentController::class, 'updateAll'])->name('documents.updateAll');
    Route::get('/documents/toggle/{id}', [ApplicationDocumentController::class, 'toggleVerify'])->name('documents.toggleVerify');

    // Application Progress Routes
    Route::get('customers/{customer}/application-progress', [ApplicationProgressController::class, 'customerHistory'])
        ->name('application-progress.customer-history');
    Route::resource('application-progress', ApplicationProgressController::class);

    // Leads Routes
    Route::match(['get','post'],'/leads/normal',[LeadController::class,'normalLeads'])->name('leads.normal');
    Route::match(['get','post'],'/leads/tatkal',[LeadController::class,'tatkalLeads'])->name('leads.tatkal');
    Route::get('/lead/{customer}',[LeadController::class,'show'])->name('lead.show');

    // Report Routes
    Route::get('/reports/gst', [ReportController::class, 'gstReport'])->name('reports.gst');

    // Support Routes
    Route::get('/support/customer', [SupportController::class, 'customerSupport'])->name('support.customer');
    Route::get('/support/guest', [SupportController::class, 'guestSupport'])->name('support.guest');
    Route::get('/support/tickets/{ticket}', [SupportController::class, 'show'])->name('support.tickets.show');
    // Route to store a new remark for a specific ticket
    Route::post('/support/tickets/{ticket}/remarks', [SupportController::class, 'storeRemark'])->name('support.tickets.remarks.store');
    // Route to update the status of a specific ticket
    Route::patch('/support/tickets/{ticket}/status', [SupportController::class, 'updateStatus'])->name('support.tickets.status.update');

    // Otp Routes
    Route::get('otps', [OtpController::class, 'index'])->name('otps.index');
    Route::get('/otps-data', [OtpController::class, 'data'])->name('otps.data'); 

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

    // Predefined Messages Routes
    Route::resource('predefined-messages', PreDefinedMessageController::class);
    Route::get('/predefined-messages-data', [PreDefinedMessageController::class, 'data'])->name('predefined-messages.data'); 

    // Document Types Routes
    Route::resource('document-types', DocumentTypeController::class);
    Route::get('/document-types-data', [DocumentTypeController::class, 'data'])->name('document-types.data'); 
    
    // Users Routes
    Route::resource('users', UserController::class);  
    Route::get('/users-data', [UserController::class, 'data'])->name('users.data');  

});
