<?php

declare(strict_types=1);

/**
 * Expense Model
 * 
 * Represents expenses recorded by hall owners in the Majalis platform.
 * Supports booking-linked expenses, recurring expenses, and detailed tracking.
 * 
 * @package App\Models
 * @author  Majalis Development Team
 * @version 1.0.0
 */

namespace App\Models;

use App\Enums\ExpensePaymentMethod;
use App\Enums\ExpensePaymentStatus;
use App\Enums\ExpenseStatus;
use App\Enums\ExpenseType;
use App\Enums\RecurringFrequency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Carbon\Carbon;

/**
 * Expense Model
 * 
 * @property int $id
 * @property string $expense_number
 * @property int $owner_id
 * @property int|null $hall_id
 * @property int|null $booking_id
 * @property int|null $category_id
 * @property string $expense_type
 * @property array $title
 * @property array|null $description
 * @property float $amount
 * @property string $currency
 * @property float $tax_amount
 * @property float $total_amount
 * @property string $payment_method
 * @property string $payment_status
 * @property string|null $payment_reference
 * @property \Carbon\Carbon $expense_date
 * @property \Carbon\Carbon|null $due_date
 * @property \Carbon\Carbon|null $paid_at
 * @property string|null $vendor_name
 * @property string|null $vendor_phone
 * @property string|null $vendor_email
 * @property array|null $attachments
 * @property bool $is_recurring
 * @property string|null $recurring_frequency
 * @property \Carbon\Carbon|null $recurring_start_date
 * @property \Carbon\Carbon|null $recurring_end_date
 * @property int $recurring_count
 * @property int|null $parent_expense_id
 * @property string $status
 * @property string|null $notes
 * @property string|null $rejection_reason
 * @property int|null $created_by
 * @property int|null $approved_by
 * @property \Carbon\Carbon|null $approved_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * 
 * @property-read User $owner
 * @property-read Hall|null $hall
 * @property-read Booking|null $booking
 * @property-read ExpenseCategory|null $category
 * @property-read Expense|null $parentExpense
 * @property-read User|null $creator
 * @property-read User|null $approver
 * @property-read \Illuminate\Database\Eloquent\Collection|Expense[] $childExpenses
 */
class Expense extends Model
{
    use HasFactory;
    use HasTranslations;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'expenses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'expense_number',
        'owner_id',
        'hall_id',
        'booking_id',
        'category_id',
        'expense_type',
        'title',
        'description',
        'amount',
        'currency',
        'tax_amount',
        'payment_method',
        'payment_status',
        'payment_reference',
        'expense_date',
        'due_date',
        'paid_at',
        'vendor_name',
        'vendor_phone',
        'vendor_email',
        'attachments',
        'is_recurring',
        'recurring_frequency',
        'recurring_start_date',
        'recurring_end_date',
        'recurring_count',
        'parent_expense_id',
        'status',
        'notes',
        'rejection_reason',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:3',
        'tax_amount' => 'decimal:3',
        'total_amount' => 'decimal:3',
        'expense_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'recurring_start_date' => 'date',
        'recurring_end_date' => 'date',
        'recurring_count' => 'integer',
        'is_recurring' => 'boolean',
        'attachments' => 'array',
        'approved_at' => 'datetime',
        'expense_type' => ExpenseType::class,
        'payment_method' => ExpensePaymentMethod::class,
        'payment_status' => ExpensePaymentStatus::class,
        'status' => ExpenseStatus::class,
        'recurring_frequency' => RecurringFrequency::class,
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatable = [
        'title',
        'description',
    ];

    /**
     * Default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'currency' => 'OMR',
        'tax_amount' => 0.000,
        'payment_status' => 'paid',
        'status' => 'approved',
        'is_recurring' => false,
        'recurring_count' => 0,
    ];

    /*
    |--------------------------------------------------------------------------
    | BOOT
    |--------------------------------------------------------------------------
    */

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate expense number on creation
        static::creating(function (Expense $expense): void {
            if (empty($expense->expense_number)) {
                $expense->expense_number = self::generateExpenseNumber();
            }

            // Set created_by if authenticated
            if (auth()->check() && empty($expense->created_by)) {
                $expense->created_by = auth()->id();
            }
        });

        // Auto-approve and set paid_at for paid expenses
        static::saving(function (Expense $expense): void {
            // If payment status changed to paid, set paid_at
            if ($expense->isDirty('payment_status') && 
                $expense->payment_status === ExpensePaymentStatus::Paid &&
                empty($expense->paid_at)) {
                $expense->paid_at = now();
            }

            // If status changed to approved, set approved_at
            if ($expense->isDirty('status') && 
                $expense->status === ExpenseStatus::Approved &&
                empty($expense->approved_at)) {
                $expense->approved_at = now();
                if (auth()->check()) {
                    $expense->approved_by = auth()->id();
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the owner (user) who recorded this expense
     *
     * @return BelongsTo<User, Expense>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the hall this expense is associated with
     *
     * @return BelongsTo<Hall, Expense>
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class, 'hall_id');
    }

    /**
     * Get the booking this expense is linked to
     *
     * @return BelongsTo<Booking, Expense>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Get the expense category
     *
     * @return BelongsTo<ExpenseCategory, Expense>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    /**
     * Get the parent expense (for recurring expenses)
     *
     * @return BelongsTo<Expense, Expense>
     */
    public function parentExpense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'parent_expense_id');
    }

    /**
     * Get child expenses (for recurring expense series)
     *
     * @return HasMany<Expense>
     */
    public function childExpenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'parent_expense_id');
    }

    /**
     * Get the user who created this expense
     *
     * @return BelongsTo<User, Expense>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this expense
     *
     * @return BelongsTo<User, Expense>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to expenses for a specific owner
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $ownerId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForOwner($query, int $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    /**
     * Scope to expenses for a specific hall
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $hallId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForHall($query, int $hallId)
    {
        return $query->where('hall_id', $hallId);
    }

    /**
     * Scope to expenses for a specific booking
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $bookingId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForBooking($query, int $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    /**
     * Scope to approved expenses only
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', ExpenseStatus::Approved);
    }

    /**
     * Scope to paid expenses only
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', ExpensePaymentStatus::Paid);
    }

    /**
     * Scope to pending payment expenses
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePendingPayment($query)
    {
        return $query->whereIn('payment_status', [
            ExpensePaymentStatus::Pending,
            ExpensePaymentStatus::Partial,
        ]);
    }

    /**
     * Scope to filter by expense type
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param ExpenseType $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, ExpenseType $type)
    {
        return $query->where('expense_type', $type);
    }

    /**
     * Scope to booking-linked expenses
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBookingExpenses($query)
    {
        return $query->whereNotNull('booking_id');
    }

    /**
     * Scope to general expenses (not linked to booking)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGeneralExpenses($query)
    {
        return $query->whereNull('booking_id');
    }

    /**
     * Scope to filter by date range
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|Carbon $startDate
     * @param string|Carbon|null $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInDateRange($query, $startDate, $endDate = null)
    {
        $query->where('expense_date', '>=', $startDate);
        
        if ($endDate) {
            $query->where('expense_date', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Scope to filter by category
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to recurring expenses
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    /**
     * Scope to order by expense date descending
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('expense_date', 'desc')
                     ->orderBy('created_at', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the localized title
     *
     * @return string
     */
    public function getLocalizedTitleAttribute(): string
    {
        return $this->getTranslation('title', app()->getLocale()) 
            ?? $this->getTranslation('title', 'en') 
            ?? '';
    }

    /**
     * Get formatted amount with currency
     *
     * @return string
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 3) . ' ' . $this->currency;
    }

    /**
     * Get formatted total amount with currency
     *
     * @return string
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format((float) $this->total_amount, 3) . ' ' . $this->currency;
    }

    /**
     * Check if expense is editable
     *
     * @return bool
     */
    public function getIsEditableAttribute(): bool
    {
        return $this->status->isEditable();
    }

    /**
     * Check if expense is overdue
     *
     * @return bool
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->payment_status === ExpensePaymentStatus::Paid) {
            return false;
        }

        if (!$this->due_date) {
            return false;
        }

        return $this->due_date->isPast();
    }

    /**
     * Get days until due (negative if overdue)
     *
     * @return int|null
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->due_date) {
            return null;
        }

        return (int) now()->diffInDays($this->due_date, false);
    }

    /*
    |--------------------------------------------------------------------------
    | METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Generate unique expense number
     *
     * @return string
     */
    public static function generateExpenseNumber(): string
    {
        $year = date('Y');
        $prefix = 'EXP';
        
        // Get the last expense number for this year
        $lastExpense = self::withTrashed()
            ->where('expense_number', 'like', "{$prefix}-{$year}-%")
            ->orderBy('expense_number', 'desc')
            ->first();

        if ($lastExpense) {
            // Extract the number and increment
            $lastNumber = (int) substr($lastExpense->expense_number, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $year, $newNumber);
    }

    /**
     * Mark expense as paid
     *
     * @param string|null $reference Payment reference
     * @param ExpensePaymentMethod|null $method Payment method
     * @return bool
     */
    public function markAsPaid(?string $reference = null, ?ExpensePaymentMethod $method = null): bool
    {
        $this->payment_status = ExpensePaymentStatus::Paid;
        $this->paid_at = now();
        
        if ($reference) {
            $this->payment_reference = $reference;
        }
        
        if ($method) {
            $this->payment_method = $method;
        }

        return $this->save();
    }

    /**
     * Approve the expense
     *
     * @param int|null $approvedBy User ID who approved
     * @return bool
     */
    public function approve(?int $approvedBy = null): bool
    {
        if (!$this->status->canTransitionTo(ExpenseStatus::Approved)) {
            return false;
        }

        $this->status = ExpenseStatus::Approved;
        $this->approved_at = now();
        $this->approved_by = $approvedBy ?? auth()->id();

        return $this->save();
    }

    /**
     * Reject the expense
     *
     * @param string $reason Rejection reason
     * @return bool
     */
    public function reject(string $reason): bool
    {
        if (!$this->status->canTransitionTo(ExpenseStatus::Rejected)) {
            return false;
        }

        $this->status = ExpenseStatus::Rejected;
        $this->rejection_reason = $reason;

        return $this->save();
    }

    /**
     * Archive the expense
     *
     * @return bool
     */
    public function archive(): bool
    {
        if (!$this->status->canTransitionTo(ExpenseStatus::Archived)) {
            return false;
        }

        $this->status = ExpenseStatus::Archived;

        return $this->save();
    }

    /**
     * Get attachments as collection
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAttachmentsCollection(): \Illuminate\Support\Collection
    {
        return collect($this->attachments ?? []);
    }

    /**
     * Add attachment
     *
     * @param string $path File path
     * @return void
     */
    public function addAttachment(string $path): void
    {
        $attachments = $this->attachments ?? [];
        $attachments[] = $path;
        $this->attachments = $attachments;
    }

    /**
     * Remove attachment
     *
     * @param string $path File path
     * @return void
     */
    public function removeAttachment(string $path): void
    {
        $attachments = $this->attachments ?? [];
        $this->attachments = array_values(array_filter($attachments, fn($a) => $a !== $path));
    }

    /**
     * Calculate booking profitability
     * 
     * @return array{revenue: float, expenses: float, profit: float, margin: float}|null
     */
    public function getBookingProfitability(): ?array
    {
        if (!$this->booking_id || !$this->booking) {
            return null;
        }

        $booking = $this->booking;
        $revenue = (float) $booking->owner_payout;
        
        $expenses = self::where('booking_id', $this->booking_id)
            ->approved()
            ->sum('total_amount');
        
        $expenses = (float) $expenses;
        $profit = $revenue - $expenses;
        $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $profit,
            'margin' => round($margin, 2),
        ];
    }
}
