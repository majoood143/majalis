<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use App\Services\BookingReminderService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * DiagnoseBookingEmail Command
 *
 * Diagnostic command to test and debug booking reminder email issues.
 * Runs through each step of the email sending process and reports status.
 *
 * Usage:
 *   php artisan booking:diagnose-email              # Interactive mode
 *   php artisan booking:diagnose-email --booking=1  # Test specific booking
 *   php artisan booking:diagnose-email --test-email=test@example.com  # Send test
 *
 * @package App\Console\Commands
 */
class DiagnoseBookingEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:diagnose-email
                            {--booking= : Specific booking ID to test}
                            {--test-email= : Email address to send test to}
                            {--skip-send : Skip actual email sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose booking reminder email issues step by step';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('');
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║       BOOKING EMAIL DIAGNOSTIC TOOL - Majalis             ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->info('');

        // Step 1: Check Mail Configuration
        $this->checkMailConfiguration();

        // Step 2: Check View/Template Exists
        $this->checkViewExists();

        // Step 3: Check Mail Components
        $this->checkMailComponents();

        // Step 4: Test Booking Data
        $booking = $this->getTestBooking();
        if (!$booking) {
            return Command::FAILURE;
        }

        // Step 5: Test Mailable Creation
        $mailable = $this->testMailableCreation($booking);
        if (!$mailable) {
            return Command::FAILURE;
        }

        // Step 6: Render Email (catches view errors)
        $this->testEmailRendering($mailable);

        // Step 7: Send Test Email
        if (!$this->option('skip-send')) {
            $this->sendTestEmail($booking, $mailable);
        }

        // Step 8: Test BookingReminderService
        $this->testReminderService($booking);

        $this->info('');
        $this->info('════════════════════════════════════════════════════════════');
        $this->info('Diagnosis complete. Check results above for issues.');
        $this->info('════════════════════════════════════════════════════════════');

        return Command::SUCCESS;
    }

    /**
     * Step 1: Check mail configuration
     */
    private function checkMailConfiguration(): void
    {
        $this->newLine();
        $this->info('▶ STEP 1: Checking Mail Configuration...');
        $this->line('─────────────────────────────────────────');

        $mailer = config('mail.default');
        $this->line("  Default Mailer: <comment>{$mailer}</comment>");

        // Check mailer settings
        $mailerConfig = config("mail.mailers.{$mailer}", []);

        if ($mailer === 'log') {
            $this->warn('  ⚠️  Using LOG mailer - emails saved to storage/logs/laravel.log');
            $this->line('     To send real emails, set MAIL_MAILER=smtp in .env');
        } elseif ($mailer === 'smtp') {
            $host = $mailerConfig['host'] ?? 'not set';
            $port = $mailerConfig['port'] ?? 'not set';
            $username = $mailerConfig['username'] ?? null;

            $this->line("  SMTP Host: <comment>{$host}</comment>");
            $this->line("  SMTP Port: <comment>{$port}</comment>");
            $this->line("  SMTP Username: <comment>" . ($username ? 'configured' : 'NOT SET') . "</comment>");

            if (!$username) {
                $this->error('  ❌ SMTP credentials not configured!');
            }
        }

        // Check from address
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        $this->line("  From Address: <comment>{$fromAddress}</comment>");
        $this->line("  From Name: <comment>{$fromName}</comment>");

        if ($fromAddress === 'hello@example.com') {
            $this->warn('  ⚠️  Using default from address - update MAIL_FROM_ADDRESS in .env');
        }

        $this->info('  ✅ Mail configuration loaded');
    }

    /**
     * Step 2: Check view/template exists
     */
    private function checkViewExists(): void
    {
        $this->newLine();
        $this->info('▶ STEP 2: Checking Email Templates...');
        $this->line('─────────────────────────────────────────');

        $views = [
            'emails.booking.reminder' => 'Main reminder template',
            'emails.booking.reminder-text' => 'Plain text template (optional)',
        ];

        foreach ($views as $view => $description) {
            $viewPath = resource_path('views/' . str_replace('.', '/', $view) . '.blade.php');

            if (file_exists($viewPath)) {
                $this->line("  ✅ {$description}: <comment>{$view}</comment>");

                // Check if it uses markdown components
                $content = file_get_contents($viewPath);
                if (str_contains($content, '<x-mail::message>')) {
                    $this->warn("     ⚠️  Template uses <x-mail::message> - requires 'markdown:' in Content()");
                }
                if (str_contains($content, '@extends(')) {
                    $this->line("     📄 Template extends a layout");
                }
            } else {
                if ($view === 'emails.booking.reminder-text') {
                    $this->line("  ⏭️  {$description}: <comment>not found (optional)</comment>");
                } else {
                    $this->error("  ❌ {$description}: NOT FOUND at {$viewPath}");
                }
            }
        }

        // Check vendor mail views
        $vendorMailPath = resource_path('views/vendor/mail');
        if (is_dir($vendorMailPath)) {
            $this->line("  ✅ Vendor mail views published at: <comment>resources/views/vendor/mail</comment>");
        } else {
            $this->warn("  ⚠️  Vendor mail views not published. Run: php artisan vendor:publish --tag=laravel-mail");
        }
    }

    /**
     * Step 3: Check mail components are available
     */
    private function checkMailComponents(): void
    {
        $this->newLine();
        $this->info('▶ STEP 3: Checking Mail Components...');
        $this->line('─────────────────────────────────────────');

        // Check if mail component namespace is registered
        try {
            $componentClass = 'Illuminate\\Mail\\Mailables\\Content';
            if (class_exists($componentClass)) {
                $this->line("  ✅ Laravel Mail Content class available");
            }

            // Try to resolve a mail component
            $testView = 'mail::message';
            if (view()->exists($testView)) {
                $this->line("  ✅ Mail message component (mail::message) accessible");
            } else {
                $this->warn("  ⚠️  mail::message view not found - may need to publish vendor views");
            }

            // Check x-mail components
            $xMailComponent = 'Illuminate\\Mail\\Markdown';
            if (class_exists($xMailComponent)) {
                $this->line("  ✅ Markdown mail class available");
            }

        } catch (Exception $e) {
            $this->error("  ❌ Error checking mail components: {$e->getMessage()}");
        }
    }

    /**
     * Step 4: Get a test booking
     */
    private function getTestBooking(): ?Booking
    {
        $this->newLine();
        $this->info('▶ STEP 4: Loading Test Booking...');
        $this->line('─────────────────────────────────────────');

        $bookingId = $this->option('booking');

        try {
            if ($bookingId) {
                $booking = Booking::with(['customer', 'hall', 'hall.city'])->find($bookingId);

                if (!$booking) {
                    $this->error("  ❌ Booking #{$bookingId} not found");
                    return null;
                }
            } else {
                // Get the most recent booking
                $booking = Booking::with(['customer', 'hall', 'hall.city'])
                    ->latest()
                    ->first();

                if (!$booking) {
                    $this->error('  ❌ No bookings found in database');
                    $this->line('     Create a booking first or provide --booking=ID');
                    return null;
                }
            }

            $this->line("  ✅ Loaded Booking: <comment>#{$booking->id}</comment>");
            $this->line("     Reference: <comment>{$booking->reference_number}</comment>");
            $this->line("     Customer: <comment>{$booking->customer_name}</comment>");

            // Check customer relationship
            if ($booking->customer) {
                $this->line("     Customer Email: <comment>{$booking->customer->email}</comment>");
            } else {
                $this->warn("     ⚠️  No customer relationship - using customer_email field");
                $this->line("     Customer Email: <comment>{$booking->customer_email}</comment>");
            }

            // Check hall relationship
            if ($booking->hall) {
                $hallName = is_array($booking->hall->name)
                    ? ($booking->hall->name['en'] ?? 'Hall')
                    : $booking->hall->name;
                $this->line("     Hall: <comment>{$hallName}</comment>");
            } else {
                $this->error("     ❌ No hall relationship loaded!");
            }

            // Check booking date
            if ($booking->booking_date) {
                $this->line("     Booking Date: <comment>{$booking->booking_date->format('Y-m-d')}</comment>");
            } else {
                $this->error("     ❌ No booking_date set!");
            }

            return $booking;

        } catch (Exception $e) {
            $this->error("  ❌ Error loading booking: {$e->getMessage()}");
            Log::error('DiagnoseBookingEmail: Error loading booking', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Step 5: Test mailable creation
     */
    private function testMailableCreation(Booking $booking): ?BookingReminderMail
    {
        $this->newLine();
        $this->info('▶ STEP 5: Creating BookingReminderMail Instance...');
        $this->line('─────────────────────────────────────────');

        try {
            $mailable = new BookingReminderMail($booking, 'Test custom message from diagnostic');

            $this->line("  ✅ Mailable instance created");

            // Check envelope
            $envelope = $mailable->envelope();
            $this->line("     Subject: <comment>{$envelope->subject}</comment>");

            if ($envelope->from) {
                $this->line("     From: <comment>{$envelope->from->address}</comment>");
            }

            // Check content
            $content = $mailable->content();
            $this->line("     View: <comment>" . ($content->view ?? 'not set') . "</comment>");
            $this->line("     Markdown: <comment>" . ($content->markdown ?? 'not set') . "</comment>");

            // Important check: view vs markdown mismatch
            if ($content->view && !$content->markdown) {
                $viewPath = resource_path('views/' . str_replace('.', '/', $content->view) . '.blade.php');
                if (file_exists($viewPath)) {
                    $viewContent = file_get_contents($viewPath);
                    if (str_contains($viewContent, '<x-mail::message>')) {
                        $this->error('  ❌ MISMATCH DETECTED!');
                        $this->error('     Template uses <x-mail::message> but Content uses view:');
                        $this->error('     Change "view:" to "markdown:" in BookingReminderMail.php');
                        $this->newLine();
                        $this->warn('     FIX: In BookingReminderMail.php content() method:');
                        $this->line('     Change: return new Content(view: "emails.booking.reminder", ...)');
                        $this->line('     To:     return new Content(markdown: "emails.booking.reminder", ...)');
                    }
                }
            }

            return $mailable;

        } catch (Exception $e) {
            $this->error("  ❌ Error creating mailable: {$e->getMessage()}");
            Log::error('DiagnoseBookingEmail: Error creating mailable', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Step 6: Test email rendering
     */
    private function testEmailRendering(BookingReminderMail $mailable): void
    {
        $this->newLine();
        $this->info('▶ STEP 6: Rendering Email Content...');
        $this->line('─────────────────────────────────────────');

        try {
            // Try to render the email
            $rendered = $mailable->render();

            $length = strlen($rendered);
            $this->line("  ✅ Email rendered successfully ({$length} bytes)");

            // Check for common issues in rendered content
            if (str_contains($rendered, 'No hint path defined')) {
                $this->error('  ❌ "No hint path defined" error in rendered output');
                $this->line('     This usually means markdown: is used but vendor views are not published');
                $this->line('     Run: php artisan vendor:publish --tag=laravel-mail');
            }

            // Show preview
            if ($this->confirm('     Show rendered email preview?', false)) {
                $this->line('');
                $this->line('─── EMAIL PREVIEW (first 1000 chars) ───');
                $this->line(substr($rendered, 0, 1000) . '...');
                $this->line('─── END PREVIEW ───');
            }

        } catch (Exception $e) {
            $this->error("  ❌ Error rendering email: {$e->getMessage()}");

            // Provide specific guidance based on error
            if (str_contains($e->getMessage(), 'No hint path defined for [mail]')) {
                $this->newLine();
                $this->warn('  🔧 FIX: This error occurs when using markdown: with <x-mail::*> components');
                $this->line('     Option 1: Publish mail views: php artisan vendor:publish --tag=laravel-mail');
                $this->line('     Option 2: Change Content to use "view:" instead of "markdown:"');
                $this->line('              BUT then your template must NOT use <x-mail::*> components');
            }

            if (str_contains($e->getMessage(), 'View [emails.booking.reminder] not found')) {
                $this->warn('  🔧 FIX: Create the view file at resources/views/emails/booking/reminder.blade.php');
            }

            Log::error('DiagnoseBookingEmail: Error rendering email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Step 7: Send test email
     */
    private function sendTestEmail(Booking $booking, BookingReminderMail $mailable): void
    {
        $this->newLine();
        $this->info('▶ STEP 7: Sending Test Email...');
        $this->line('─────────────────────────────────────────');

        $testEmail = $this->option('test-email');

        if (!$testEmail) {
            // Try to get email from booking
            $testEmail = $booking->customer->email ?? $booking->customer_email ?? null;

            if (!$testEmail) {
                $this->warn('  ⚠️  No test email provided and booking has no email');
                $this->line('     Use --test-email=your@email.com to test sending');
                return;
            }
        }

        $this->line("  Sending to: <comment>{$testEmail}</comment>");

        $mailer = config('mail.default');
        if ($mailer === 'log') {
            $this->warn("  ⚠️  Using LOG mailer - check storage/logs/laravel.log for output");
        }

        if (!$this->confirm('  Proceed with sending test email?', true)) {
            $this->line('  ⏭️  Skipped sending');
            return;
        }

        try {
            // Create fresh mailable for sending
            $freshMailable = new BookingReminderMail($booking, 'Test email from diagnostic command');

            // THE FIX: Use Mail::to()->send() pattern
            Mail::to($testEmail)->send($freshMailable);

            $this->info("  ✅ Email sent successfully!");

            if ($mailer === 'log') {
                $this->line('     Check: storage/logs/laravel.log');
            } else {
                $this->line("     Check inbox: {$testEmail}");
            }

        } catch (Exception $e) {
            $this->error("  ❌ Error sending email: {$e->getMessage()}");

            // Common error guidance
            if (str_contains($e->getMessage(), 'Connection could not be established')) {
                $this->warn('  🔧 FIX: SMTP connection failed - check MAIL_HOST, MAIL_PORT in .env');
            }
            if (str_contains($e->getMessage(), 'Authentication failed')) {
                $this->warn('  🔧 FIX: SMTP auth failed - check MAIL_USERNAME, MAIL_PASSWORD in .env');
            }

            Log::error('DiagnoseBookingEmail: Error sending email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Step 8: Test BookingReminderService
     */
    private function testReminderService(Booking $booking): void
    {
        $this->newLine();
        $this->info('▶ STEP 8: Testing BookingReminderService...');
        $this->line('─────────────────────────────────────────');

        try {
            $service = app(BookingReminderService::class);
            $this->line("  ✅ Service instantiated");

            // Check if service has the fixed Mail::to() pattern
            $reflection = new \ReflectionClass($service);
            $sourceFile = $reflection->getFileName();

            if ($sourceFile && file_exists($sourceFile)) {
                $sourceCode = file_get_contents($sourceFile);

                // Check for the bug
                if (str_contains($sourceCode, 'Mail::send($mailable)')) {
                    $this->error('  ❌ BUG FOUND in BookingReminderService!');
                    $this->error('     Mail::send($mailable) does not specify recipient');
                    $this->newLine();
                    $this->warn('  🔧 FIX: Change all occurrences of:');
                    $this->line('     Mail::send($mailable);');
                    $this->line('     To:');
                    $this->line('     Mail::to($booking->customer->email)->send($mailable);');
                } elseif (str_contains($sourceCode, 'Mail::to(')) {
                    $this->line("  ✅ Service uses correct Mail::to()->send() pattern");
                }
            }

            // Test actual service call (skip-send mode)
            if ($this->option('skip-send')) {
                $this->line("  ⏭️  Skipping actual service test (--skip-send)");
            } else {
                if ($this->confirm('  Test service sendReminder() method?', false)) {
                    $result = $service->sendReminder($booking, 'Diagnostic test', 'en', false);

                    if ($result) {
                        $this->info("  ✅ Service sendReminder() returned TRUE");
                    } else {
                        $this->error("  ❌ Service sendReminder() returned FALSE");
                        $this->line('     Check storage/logs/laravel.log for details');
                    }
                }
            }

        } catch (Exception $e) {
            $this->error("  ❌ Error with BookingReminderService: {$e->getMessage()}");
            Log::error('DiagnoseBookingEmail: Service error', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
