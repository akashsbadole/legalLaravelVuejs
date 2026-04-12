<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CaseController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\DocumentController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::apiResource('cases', CaseController::class);
    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('documents', DocumentController::class);
    
    Route::get('cases/{case}/timeline', [CaseController::class, 'timeline']);
    Route::post('cases/{case}/notes', [CaseController::class, 'addNote']);
    
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf']);
    Route::post('invoices/{invoice}/payment', [InvoiceController::class, 'recordPayment']);
    
    Route::get('documents/{document}/download', [DocumentController::class, 'download']);
});
