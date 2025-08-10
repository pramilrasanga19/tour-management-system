<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EnquiryController;
use App\Http\Controllers\API\ItineraryController;
use App\Http\Controllers\API\QuotationController;
use App\Http\Controllers\API\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Public Enquiry Route
Route::post('/enquiries', [EnquiryController::class, 'store']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Enquiry Management
    Route::get('/enquiries', [EnquiryController::class, 'index']);
    Route::put('/enquiries/{enquiry}/assign', [EnquiryController::class, 'assign']);
    Route::patch('/enquiries/{enquiry}/status', [EnquiryController::class, 'updateStatus']);
    
    // Itinerary Management
    Route::get('/itineraries', [ItineraryController::class, 'index']);
    Route::post('/itineraries', [ItineraryController::class, 'store']);
    Route::get('/itineraries/{itinerary}', [ItineraryController::class, 'show']);
    Route::put('/itineraries/{itinerary}', [ItineraryController::class, 'update']);
    Route::delete('/itineraries/{itinerary}', [ItineraryController::class, 'destroy']);
    
    // Quotation Management
    Route::get('/quotations', [QuotationController::class, 'index']);
    Route::post('/quotations', [QuotationController::class, 'store']);
    Route::get('/quotations/{quotation}', [QuotationController::class, 'show']);
    
    // Payment Management
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::post('/payments', [PaymentController::class, 'store']);
});

// Public Quotation View
Route::get('/quotations/public/{uniqueId}', [QuotationController::class, 'publicShow']);