<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * TestBookingEmail Command
 *
 * Simple command to test booking reminder email sending.
 * Provides clear debugging output for troubleshooting.
 *
 * Usage:
 *   php artisan booking:test-email                    # Test with latest booking
 *   php artisan booking:test-email --booking=20      # Test with specific booking ID
 *   php artisan booking:test-email --to=test@example.com  # Override recipient
 *
 * @package App\Console\Commands
 */
class TestBookingEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:test-email
                            {--booking= : Specific booking ID to test}
                            {--to= : Override recipient email address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test booking reminder email sending with debug output';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('');
        $this->info('=== BOOKING EMAIL TEST ===');
        $this->info('');

        // Step 1: Load booking
        $bookingId = $this->option('booking');
        
        if ($bookingId) {
            $booking = Booking::with(['user', 'hall', 'hall.city'])->find($bookingId);
            if (!$booking) {
                $this->error("Booking #{$bookingId} not found!");
                return Command::FAILURE;
            }
        } else {
            $booking = Booking::with(['user', 'hall', 'hall.city'])
                ->whereNotNull('customer_email')
                ->where('customer_email', '!=', '')
                ->latest()
                ->first();
                
            if (!$booking) {
                $this->error('No bookings with customer_email found!');
                return Command::FAILURE;
            }
        }

        // Step 2: Show booking details
        $this->info("Booking Details:");
        $this->line("  ID: {$booking->id}");
        $this->line("  Booking Number: {$booking->booking_number}");
        $this->line("  customer_name: " . ($booking->customer_name ?: '<EMPTY>'));
        $this->line("  customer_email: " . ($booking->customer_email ?: '<EMPTY>'));
        $this->line("  customer_phone: " . ($booking->customer_phone ?: '<EMPTY>'));
        $this->line("  user_id: " . ($booking->user_id ?: '<NULL>'));
        
        if ($booking->user) {
            $this->line("  user->email: " . ($booking->user->email ?: '<EMPTY>'));
        } else {
            $this->line("  user: <NO USER RELATIONSHIP>");
        }
        
        if ($booking->hall) {
            $hallName = is_array($booking->hall->name) 
                ? ($booking->hall->name['en'] ?? 'Hall') 
                : $booking->hall->name;
            $this->line("  hall: {$hallName}");
        } else {
            $this->error("  hall: <NO HALL - THIS WILL FAIL>");
        }
        
        $this->line("  booking_date: " . ($booking->booking_date ? $booking->booking_date->format('Y-m-d') : '<NULL>'));
        $this->info('');

        // Step 3: Determine recipient email
        $recipientEmail = $this->option('to');
        
        if (!$recipientEmail) {
            // Try customer_email first
            if (!empty($booking->customer_email)) {
                $recipientEmail = $booking->customer_email;
                $this->line("Using customer_email: {$recipientEmail}");
            } 
            // Fallback to user email
            elseif ($booking->user && !empty($booking->user->email)) {
                $recipientEmail = $booking->user->email;
                $this->line("Using user->email (fallback): {$recipientEmail}");
            }
        } else {
            $this->line("Using override email (--to): {$recipientEmail}");
        }

        // Validate email
        if (empty($recipientEmail)) {
            $this->error('');
            $this->error('❌ NO EMAIL ADDRESS AVAILABLE!');
            $this->error('');
            $this->line('The booking has:');
            $this->line("  customer_email = '" . ($booking->customer_email ?? 'NULL') . "'");
            $this->line("  user->email = '" . ($booking->user->email ?? 'NULL') . "'");
            $this->info('');
            $this->line('Solutions:');
            $this->line('  1. Use --to=your@email.com to specify a recipient');
            $this->line('  2. Use --booking=ID to test a booking that has customer_email');
            $this->line('  3. Update the booking in the database to add customer_email');
            return Command::FAILURE;
        }

        if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error("Invalid email format: {$recipientEmail}");
            return Command::FAILURE;
        }

        $this->info('');
        $this->info("Recipient: {$recipientEmail}");
        $this->info('');

        // Step 4: Check mail configuration
        $mailer = config('mail.default');
        $this->line("Mail driver: {$mailer}");
        
        if ($mailer === 'log') {
            $this->warn('⚠️  Using LOG mailer - email will be written to storage/logs/laravel.log');
        }
        $this->info('');

        // Step 5: Create and send email
        if (!$this->confirm("Send test email to {$recipientEmail}?", true)) {
            $this->line('Cancelled.');
            return Command::SUCCESS;
        }

        try {
            $this->line('Creating mailable...');
            $mailable = new BookingReminderMail($booking, 'This is a test reminder from the diagnostic command.');
            
            $this->line('Rendering email...');
            $rendered = $mailable->render();
            $this->line("  ✅ Rendered successfully (" . strlen($rendered) . " bytes)");
            
            $this->line("Sending to {$recipientEmail}...");
            Mail::to($recipientEmail)->send($mailable);
            
            $this->info('');
            $this->info('✅ EMAIL SENT SUCCESSFULLY!');
            $this->info('');
            
            if ($mailer === 'log') {
                $this->line('Check: storage/logs/laravel.log');
                $this->line('Run: tail -100 storage/logs/laravel.log | grep -A50 "Message-ID"');
            } else {
                $this->line("Check inbox: {$recipientEmail}");
            }
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('');
            $this->error('❌ FAILED TO SEND EMAIL');
            $this->error('');
            $this->line("Error: {$e->getMessage()}");
            $this->line("File: {$e->getFile()}:{$e->getLine()}");
            $this->info('');
            
            // Provide specific guidance
            if (str_contains($e->getMessage(), 'must have a "To"')) {
                $this->warn('The email address was empty when Mail::to() was called.');
                $this->line("Recipient variable value: '" . ($recipientEmail ?? 'NULL') . "'");
            }
            
            if (str_contains($e->getMessage(), 'No hint path defined')) {
                $this->warn('Run: php artisan vendor:publish --tag=laravel-mail');
            }
            
            if (str_contains($e->getMessage(), 'View [emails.booking.reminder] not found')) {
                $this->warn('Create: resources/views/emails/booking/reminder.blade.php');
            }
            
            return Command::FAILURE;
        }
    }
}
