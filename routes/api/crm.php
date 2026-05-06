<?php

use App\Http\Controllers\Api\CRM\CustomerController;
use App\Http\Controllers\Api\CRM\BusinessCustomerController;
use App\Http\Controllers\Api\CRM\QuotationDocumentController;
use App\Http\Controllers\Api\CRM\PaymentController;
use App\Http\Controllers\Api\Business\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'endpoint.permission'])->group(function (): void {
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::patch('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::get('/crm/customers', [BusinessCustomerController::class, 'index'])->name('crm.customers.index');
    Route::post('/crm/customers', [BusinessCustomerController::class, 'store'])->name('crm.customers.store');
    Route::get('/quotations', [QuotationDocumentController::class, 'index'])->name('quotations.index');
    Route::post('/quotations', [QuotationDocumentController::class, 'store'])->name('quotations.store');
    Route::post('/quotations/{quotation}/accept', [QuotationDocumentController::class, 'accept'])->name('quotations.accept');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/crm/analytics', [AnalyticsController::class, 'crm'])->name('crm.analytics');
});
