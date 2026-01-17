<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * GuestSession Model
 *
 * Manages guest booking sessions including email verification via OTP.
 * This model handles the complete lifecycle of a guest booking from
 * initial form submission through payment completion.
 *
 * Session Lifecycle:
 * 1. pending → Guest initiates booking, OTP sent
 * 2. verified → OTP confirmed, can proceed
 * 3. booking → Form submitted, creating booking
 * 4. payment → Redirected to payment gateway
 * 5. completed → Payment successful, booking confirmed
 *
 * Security Features:
 * - 6-digit OTP with 10-minute expiration
 * - Maximum 3 OTP attempts before lockout
 * - 24-hour session expiration
 * - Rate limiting per email address
 *
 * @package App\Models
 * @version 1.0.0
 *
 * @property int $id
 * @property string $session_token
 * @property string $email
 * @property string|null $phone
 * @property string $name
 * @property string|null $otp_code
 * @property Carbon|null $otp_expires_at
 * @property int $otp_attempts
 * @property bool $is_verified
 * @property Carbon|null $verified_at
 * @property string $status
 * @property array|null $booking_data
 * @property int|null $hall_id
 * @property int|null $booking_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property Carbon $expires_at
 * @property array|null $metadata
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Hall|null $hall
 * @property-read Booking|null $booking
 */
class GuestSession extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'guest_sessions';

    /**
     * Maximum OTP verification attempts allowed.
     *
     * @var int
     */
    public const MAX_OTP_ATTEMPTS = 3;

    /**
     * OTP expiration time in minutes.
     *
     * @var int
     */
    public const OTP_EXPIRY_MINUTES = 10;

    /**
     * Session expiration time in hours.
     *
     * @var int
     */
    public const SESSION_EXPIRY_HOURS = 24;

    /**
     * Maximum pending bookings allowed per email.
     *
     * @var int
     */
    public const MAX_PENDING_PER_EMAIL = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'session_token',
        'email',
        'phone',
        'name',
        'otp_code',
        'otp_expires_at',
        'otp_attempts',
        'is_verified',
        'verified_at',
        'status',
        'booking_data',
        'hall_id',
        'booking_id',
        'ip_address',
        'user_agent',
        'expires_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'otp_expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
        'otp_attempts' => 'integer',
        'booking_data' => 'array',
        'metadata' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'otp_code',
        'ip_address',
        'user_agent',
    ];

    // =========================================================================
    // BOOT METHODS
    // =========================================================================

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate session token and set expiration on creation
        static::creating(function (GuestSession $session): void {
            if (empty($session->session_token)) {
                $session->session_token = (string) Str::uuid();
            }

            if (empty($session->expires_at)) {
                $session->expires_at = now()->addHours(self::SESSION_EXPIRY_HOURS);
            }
        });
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get the hall associated with this session.
     *
     * @return BelongsTo<Hall, GuestSession>
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    /**
     * Get the booking created from this session.
     *
     * @return BelongsTo<Booking, GuestSession>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope to filter active (non-expired) sessions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now())
            ->whereNotIn('status', ['expired', 'cancelled', 'completed']);
    }

    /**
     * Scope to filter verified sessions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to filter sessions by email.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', strtolower($email));
    }

    /**
     * Scope to filter pending sessions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'verified', 'booking', 'payment']);
    }

    /**
     * Scope to filter expired sessions for cleanup.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now())
            ->where('status', '!=', 'completed');
    }

    // =========================================================================
    // OTP METHODS
    // =========================================================================

    /**
     * Generate a new OTP code for email verification.
     *
     * @return string The generated 6-digit OTP
     */
    public function generateOtp(): string
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'otp_attempts' => 0,
        ]);

        return $otp;
    }

    /**
     * Verify the provided OTP code.
     *
     * @param string $code The OTP code to verify
     * @return bool True if verification successful
     */
    public function verifyOtp(string $code): bool
    {
        // Check if already verified
        if ($this->is_verified) {
            return true;
        }

        // Check if max attempts exceeded
        if ($this->otp_attempts >= self::MAX_OTP_ATTEMPTS) {
            return false;
        }

        // Check if OTP expired
        if ($this->isOtpExpired()) {
            return false;
        }

        // Increment attempts
        $this->increment('otp_attempts');

        // Verify code
        if ($this->otp_code === $code) {
            $this->update([
                'is_verified' => true,
                'verified_at' => now(),
                'status' => 'verified',
                'otp_code' => null, // Clear OTP after successful verification
            ]);

            return true;
        }

        return false;
    }

    /**
     * Check if OTP has expired.
     *
     * @return bool
     */
    public function isOtpExpired(): bool
    {
        return $this->otp_expires_at === null || $this->otp_expires_at->isPast();
    }

    /**
     * Check if OTP attempts are exhausted.
     *
     * @return bool
     */
    public function isOtpLocked(): bool
    {
        return $this->otp_attempts >= self::MAX_OTP_ATTEMPTS;
    }

    /**
     * Get remaining OTP attempts.
     *
     * @return int
     */
    public function getRemainingOtpAttemptsAttribute(): int
    {
        return max(0, self::MAX_OTP_ATTEMPTS - $this->otp_attempts);
    }

    // =========================================================================
    // STATUS METHODS
    // =========================================================================

    /**
     * Check if session is active (not expired or cancelled).
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->expires_at->isFuture()
            && !in_array($this->status, ['expired', 'cancelled', 'completed']);
    }

    /**
     * Check if session can proceed to booking.
     *
     * @return bool
     */
    public function canProceedToBooking(): bool
    {
        return $this->is_verified
            && $this->isActive()
            && in_array($this->status, ['verified', 'booking']);
    }

    /**
     * Check if session can proceed to payment.
     *
     * @return bool
     */
    public function canProceedToPayment(): bool
    {
        return $this->is_verified
            && $this->isActive()
            && in_array($this->status, ['booking', 'payment']);
    }

    /**
     * Update session status.
     *
     * @param string $status New status
     * @return bool
     */
    public function updateStatus(string $status): bool
    {
        $validStatuses = ['pending', 'verified', 'booking', 'payment', 'completed', 'expired', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            return false;
        }

        return $this->update(['status' => $status]);
    }

    /**
     * Mark session as completed with booking reference.
     *
     * @param Booking $booking The created booking
     * @return bool
     */
    public function complete(Booking $booking): bool
    {
        return $this->update([
            'status' => 'completed',
            'booking_id' => $booking->id,
        ]);
    }

    /**
     * Mark session as expired.
     *
     * @return bool
     */
    public function expire(): bool
    {
        return $this->update(['status' => 'expired']);
    }

    /**
     * Mark session as cancelled.
     *
     * @return bool
     */
    public function cancel(): bool
    {
        return $this->update(['status' => 'cancelled']);
    }

    // =========================================================================
    // BOOKING DATA METHODS
    // =========================================================================

    /**
     * Store booking form data in session.
     *
     * @param array $data Booking form data
     * @return bool
     */
    public function storeBookingData(array $data): bool
    {
        return $this->update([
            'booking_data' => array_merge($this->booking_data ?? [], $data),
        ]);
    }

    /**
     * Get specific booking data value.
     *
     * @param string $key Data key
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public function getBookingDataValue(string $key, mixed $default = null): mixed
    {
        return $this->booking_data[$key] ?? $default;
    }

    // =========================================================================
    // STATIC HELPER METHODS
    // =========================================================================

    /**
     * Find active session by token.
     *
     * @param string $token Session token
     * @return GuestSession|null
     */
    public static function findByToken(string $token): ?self
    {
        return self::where('session_token', $token)
            ->active()
            ->first();
    }

    /**
     * Count pending sessions for an email.
     *
     * @param string $email Email address
     * @return int
     */
    public static function countPendingByEmail(string $email): int
    {
        return self::byEmail($email)
            ->pending()
            ->active()
            ->count();
    }

    /**
     * Check if email has exceeded pending booking limit.
     *
     * @param string $email Email address
     * @return bool
     */
    public static function hasExceededPendingLimit(string $email): bool
    {
        return self::countPendingByEmail($email) >= self::MAX_PENDING_PER_EMAIL;
    }

    /**
     * Check if email belongs to an existing user.
     *
     * @param string $email Email address
     * @return bool
     */
    public static function emailBelongsToUser(string $email): bool
    {
        return User::where('email', strtolower($email))->exists();
    }

    /**
     * Create a new guest session.
     *
     * @param array $data Session data
     * @return self
     */
    public static function createSession(array $data): self
    {
        return self::create([
            'email' => strtolower($data['email']),
            'phone' => $data['phone'] ?? null,
            'name' => $data['name'],
            'hall_id' => $data['hall_id'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'status' => 'pending',
        ]);
    }

    /**
     * Cleanup expired sessions (for scheduled task).
     *
     * @return int Number of sessions marked as expired
     */
    public static function cleanupExpired(): int
    {
        return self::expired()
            ->update(['status' => 'expired']);
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Get masked email for display.
     *
     * @return string
     */
    public function getMaskedEmailAttribute(): string
    {
        $parts = explode('@', $this->email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';

        $maskedName = substr($name, 0, 2) . str_repeat('*', max(0, strlen($name) - 2));

        return $maskedName . '@' . $domain;
    }

    /**
     * Get time until session expires.
     *
     * @return string
     */
    public function getExpiresInAttribute(): string
    {
        if ($this->expires_at->isPast()) {
            return __('Expired');
        }

        return $this->expires_at->diffForHumans();
    }

    /**
     * Get human-readable status.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => __('Awaiting Verification'),
            'verified' => __('Verified'),
            'booking' => __('Creating Booking'),
            'payment' => __('Processing Payment'),
            'completed' => __('Completed'),
            'expired' => __('Expired'),
            'cancelled' => __('Cancelled'),
            default => $this->status,
        };
    }
}
