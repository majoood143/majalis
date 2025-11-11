<?php

/**
 * Customer Ticket Routes
 * 
 * Add these routes to your routes/web.php file within the customer middleware group.
 * 
 * @package Routes
 * @version 1.0.0
 */

use App\Http\Controllers\Customer\TicketController;

// Customer Ticket Routes (require authentication)
Route::middleware(['auth', 'verified'])->prefix('customer')->name('customer.')->group(function () {
    
    // Tickets Resource Routes
    Route::prefix('tickets')->name('tickets.')->group(function () {
        
        // List all customer's tickets
        Route::get('/', [TicketController::class, 'index'])
            ->name('index');
        
        // Show create ticket form
        Route::get('/create', [TicketController::class, 'create'])
            ->name('create');
        
        // Store new ticket
        Route::post('/', [TicketController::class, 'store'])
            ->name('store');
        
        // Show specific ticket details
        Route::get('/{ticket}', [TicketController::class, 'show'])
            ->name('show');
        
        // Add reply to ticket
        Route::post('/{ticket}/reply', [TicketController::class, 'reply'])
            ->name('reply');
        
        // Download attachment
        Route::get('/{ticket}/message/{message}/attachment/{index}', [TicketController::class, 'downloadAttachment'])
            ->name('download-attachment');
        
        // Close ticket
        Route::post('/{ticket}/close', [TicketController::class, 'close'])
            ->name('close');
        
        // Rate closed ticket
        Route::post('/{ticket}/rate', [TicketController::class, 'rate'])
            ->name('rate');
        
        // Reopen closed ticket
        Route::post('/{ticket}/reopen', [TicketController::class, 'reopen'])
            ->name('reopen');
    });
});

/**
 * Example integration in routes/web.php:
 * 
 * // Customer Dashboard Routes
 * Route::middleware(['auth', 'verified'])->prefix('customer')->name('customer.')->group(function () {
 *     
 *     // Dashboard
 *     Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
 *     
 *     // Bookings
 *     Route::resource('bookings', BookingController::class);
 *     
 *     // Support Tickets (add this)
 *     require __DIR__ . '/customer-tickets.php';
 *     
 *     // Other customer routes...
 * });
 */
