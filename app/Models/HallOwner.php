<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HallOwner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_name_ar',
        'commercial_registration',
        'tax_number',
        'business_phone',
        'business_email',
        'business_address',
        'business_address_ar',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'iban',
        'commercial_registration_document',
        'tax_certificate',
        'identity_document',
        'additional_documents',
        'is_verified',
        'verified_at',
        'verified_by',
        'verification_notes',
        'commission_type',
        'commission_value',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'additional_documents' => 'array',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'commission_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePendingVerification($query)
    {
        return $query->where('is_verified', false)
            ->where('is_active', true);
    }

    // Status Methods
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function hasCustomCommission(): bool
    {
        return !is_null($this->commission_type) && !is_null($this->commission_value);
    }

    // Action Methods
    public function verify(int $verifiedBy, string $notes = null): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
            'verification_notes' => $notes,
        ]);

        // Update user role
        $this->user->update(['role' => 'hall_owner']);
    }

    public function reject(string $notes = null): void
    {
        $this->update([
            'is_verified' => false,
            'verification_notes' => $notes,
        ]);
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    // Helper Methods
    public function getTotalHalls(): int
    {
        return Hall::where('owner_id', $this->user_id)->count();
    }

    public function getActiveHalls(): int
    {
        return Hall::where('owner_id', $this->user_id)
            ->where('is_active', true)
            ->count();
    }

    public function getTotalBookings(): int
    {
        return Booking::whereHas('hall', function ($query) {
            $query->where('owner_id', $this->user_id);
        })->count();
    }

    public function getTotalRevenue(): float
    {
        return Booking::whereHas('hall', function ($query) {
            $query->where('owner_id', $this->user_id);
        })
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('owner_payout');
    }

    public function getPendingPayout(): float
    {
        return Booking::whereHas('hall', function ($query) {
            $query->where('owner_id', $this->user_id);
        })
            ->where('status', 'confirmed')
            ->where('payment_status', 'paid')
            ->sum('owner_payout');
    }

    public function getBusinessNameAttribute(): string
    {
        $locale = app()->getLocale();

        if ($locale === 'ar' && !empty($this->business_name_ar)) {
            return $this->business_name_ar;
        }

        return $this->attributes['business_name'];
    }

    public function getBusinessAddressAttribute(): string
    {
        $locale = app()->getLocale();

        if ($locale === 'ar' && !empty($this->business_address_ar)) {
            return $this->business_address_ar;
        }

        return $this->attributes['business_address'];
    }

    public function hasAllDocuments(): bool
    {
        return !empty($this->commercial_registration_document)
            && !empty($this->identity_document);
    }

    public function getDocumentProgress(): int
    {
        $totalFields = 3; // CR, Tax, ID
        $completed = 0;

        if (!empty($this->commercial_registration_document)) $completed++;
        if (!empty($this->tax_certificate)) $completed++;
        if (!empty($this->identity_document)) $completed++;

        return ($completed / $totalFields) * 100;
    }
}
