<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PricingResource\Pages;

use App\Filament\Owner\Resources\PricingResource;
use App\Models\Hall;
use App\Models\HallAvailability;
use App\Models\SeasonalPricing;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * PriceCalculator Page for Owner Panel
 *
 * Interactive price calculator that shows how pricing rules
 * affect the final price for any date/slot combination.
 *
 * Features:
 * - Select hall, date, and time slot
 * - See base price, all applied rules, and final price
 * - Preview pricing for date ranges
 * - Compare pricing across slots
 */
class PriceCalculator extends Page implements HasForms
{
    use InteractsWithForms;

    /**
     * The resource this page belongs to.
     */
    protected static string $resource = PricingResource::class;

    /**
     * The view for this page.
     */
    protected static string $view = 'filament.owner.resources.pricing-resource.pages.price-calculator';

    /**
     * Selected hall ID.
     */
    public ?int $selectedHallId = null;

    /**
     * Selected date.
     */
    public ?string $selectedDate = null;

    /**
     * Selected time slot.
     */
    public ?string $selectedSlot = null;

    /**
     * Number of guests (for per-person services).
     */
    public int $numberOfGuests = 1;

    /**
     * Mount the page.
     */
    public function mount(): void
    {
        $this->selectedDate = now()->addDay()->toDateString();
        $this->selectedSlot = 'evening';

        // Pre-select first hall if only one
        $halls = $this->getOwnerHalls();
        if ($halls->count() === 1) {
            $this->selectedHallId = $halls->first()->id;
        }
    }

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.pricing.calculator.title');
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return __('owner.pricing.calculator.heading');
    }

    /**
     * Get the subheading.
     */
    public function getSubheading(): ?string
    {
        return __('owner.pricing.calculator.subheading');
    }

    /**
     * Get header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label(__('owner.pricing.actions.back_to_list'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => PricingResource::getUrl('index')),

            Actions\Action::make('create_rule')
                ->label(__('owner.pricing.actions.create'))
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(fn () => PricingResource::getUrl('create')),
        ];
    }

    /**
     * Get owner's halls.
     */
    #[Computed]
    public function getOwnerHalls(): Collection
    {
        $user = Auth::user();
        return Hall::where('owner_id', $user->id)
            ->orderBy('name->en')
            ->get();
    }

    /**
     * Get the selected hall.
     */
    #[Computed]
    public function selectedHall(): ?Hall
    {
        if (!$this->selectedHallId) {
            return null;
        }

        return Hall::find($this->selectedHallId);
    }

    /**
     * Calculate and return pricing breakdown.
     */
    #[Computed]
    public function pricingBreakdown(): ?array
    {
        if (!$this->selectedHallId || !$this->selectedDate || !$this->selectedSlot) {
            return null;
        }

        $hall = $this->selectedHall;
        if (!$hall) {
            return null;
        }

        $date = Carbon::parse($this->selectedDate);

        // 1. Get base price for slot
        $basePrice = $hall->getPriceForSlot($this->selectedSlot);

        // 2. Check for custom availability price
        $customPrice = null;
        $availability = HallAvailability::where('hall_id', $this->selectedHallId)
            ->where('date', $this->selectedDate)
            ->where('time_slot', $this->selectedSlot)
            ->first();

        if ($availability && $availability->custom_price !== null) {
            $customPrice = (float) $availability->custom_price;
        }

        // 3. Get applicable pricing rules (ordered by priority)
        $applicableRules = SeasonalPricing::where('hall_id', $this->selectedHallId)
            ->where('is_active', true)
            ->forDate($date)
            ->forSlot($this->selectedSlot)
            ->orderBy('priority', 'desc')
            ->get();

        // 4. Calculate final price
        $currentPrice = $customPrice ?? $basePrice;
        $rulesApplied = [];

        foreach ($applicableRules as $rule) {
            $previousPrice = $currentPrice;
            $currentPrice = $rule->calculatePrice($currentPrice);

            $rulesApplied[] = [
                'name' => $rule->getTranslation('name', app()->getLocale()),
                'type' => $rule->type,
                'adjustment' => $rule->adjustment_description,
                'priority' => $rule->priority,
                'previous_price' => $previousPrice,
                'new_price' => $currentPrice,
                'difference' => $currentPrice - $previousPrice,
            ];
        }

        $finalPrice = $currentPrice;

        // 5. Build breakdown
        return [
            'base_price' => $basePrice,
            'slot_override' => $hall->pricing_override[$this->selectedSlot] ?? null,
            'custom_price' => $customPrice,
            'rules_applied' => $rulesApplied,
            'final_price' => $finalPrice,
            'total_adjustment' => $finalPrice - $basePrice,
            'adjustment_percentage' => $basePrice > 0 ? (($finalPrice - $basePrice) / $basePrice) * 100 : 0,
            'date_info' => [
                'day_name' => $date->locale(app()->getLocale())->dayName,
                'formatted' => $date->format('d M Y'),
                'is_weekend' => in_array($date->dayOfWeek, [5, 6]),
                'is_past' => $date->isPast(),
            ],
        ];
    }

    /**
     * Get pricing comparison for all slots on selected date.
     */
    #[Computed]
    public function slotComparison(): ?array
    {
        if (!$this->selectedHallId || !$this->selectedDate) {
            return null;
        }

        $hall = $this->selectedHall;
        if (!$hall) {
            return null;
        }

        $slots = ['morning', 'afternoon', 'evening', 'full_day'];
        $comparison = [];

        foreach ($slots as $slot) {
            $basePrice = $hall->getPriceForSlot($slot);

            // Check custom price
            $availability = HallAvailability::where('hall_id', $this->selectedHallId)
                ->where('date', $this->selectedDate)
                ->where('time_slot', $slot)
                ->first();

            $customPrice = $availability?->custom_price;
            $currentPrice = $customPrice ?? $basePrice;

            // Apply rules
            $rules = SeasonalPricing::where('hall_id', $this->selectedHallId)
                ->where('is_active', true)
                ->forDate($this->selectedDate)
                ->forSlot($slot)
                ->orderBy('priority', 'desc')
                ->get();

            foreach ($rules as $rule) {
                $currentPrice = $rule->calculatePrice($currentPrice);
            }

            $comparison[$slot] = [
                'base_price' => $basePrice,
                'final_price' => $currentPrice,
                'has_custom' => $customPrice !== null,
                'rules_count' => $rules->count(),
                'is_available' => $availability?->is_available ?? true,
            ];
        }

        return $comparison;
    }

    /**
     * Get week preview (7 days from selected date).
     */
    #[Computed]
    public function weekPreview(): ?array
    {
        if (!$this->selectedHallId || !$this->selectedDate || !$this->selectedSlot) {
            return null;
        }

        $hall = $this->selectedHall;
        if (!$hall) {
            return null;
        }

        $startDate = Carbon::parse($this->selectedDate);
        $preview = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $basePrice = $hall->getPriceForSlot($this->selectedSlot);

            // Check custom price
            $availability = HallAvailability::where('hall_id', $this->selectedHallId)
                ->where('date', $date->toDateString())
                ->where('time_slot', $this->selectedSlot)
                ->first();

            $customPrice = $availability?->custom_price;
            $currentPrice = $customPrice ?? $basePrice;

            // Apply rules
            $rules = SeasonalPricing::where('hall_id', $this->selectedHallId)
                ->where('is_active', true)
                ->forDate($date)
                ->forSlot($this->selectedSlot)
                ->orderBy('priority', 'desc')
                ->get();

            foreach ($rules as $rule) {
                $currentPrice = $rule->calculatePrice($currentPrice);
            }

            $preview[] = [
                'date' => $date->toDateString(),
                'day_name' => $date->locale(app()->getLocale())->shortDayName,
                'day_number' => $date->day,
                'final_price' => $currentPrice,
                'is_weekend' => in_array($date->dayOfWeek, [5, 6]),
                'is_today' => $date->isToday(),
                'has_rules' => $rules->count() > 0,
            ];
        }

        return $preview;
    }

    /**
     * Update selected hall.
     */
    public function setHall(?int $hallId): void
    {
        $this->selectedHallId = $hallId;
        unset($this->pricingBreakdown);
        unset($this->slotComparison);
        unset($this->weekPreview);
    }

    /**
     * Update selected date.
     */
    public function setDate(?string $date): void
    {
        $this->selectedDate = $date;
        unset($this->pricingBreakdown);
        unset($this->slotComparison);
        unset($this->weekPreview);
    }

    /**
     * Update selected slot.
     */
    public function setSlot(?string $slot): void
    {
        $this->selectedSlot = $slot;
        unset($this->pricingBreakdown);
        unset($this->weekPreview);
    }

    /**
     * Navigate to next day.
     */
    public function nextDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addDay()->toDateString();
        unset($this->pricingBreakdown);
        unset($this->slotComparison);
        unset($this->weekPreview);
    }

    /**
     * Navigate to previous day.
     */
    public function previousDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subDay()->toDateString();
        unset($this->pricingBreakdown);
        unset($this->slotComparison);
        unset($this->weekPreview);
    }

    /**
     * Go to today.
     */
    public function goToToday(): void
    {
        $this->selectedDate = now()->toDateString();
        unset($this->pricingBreakdown);
        unset($this->slotComparison);
        unset($this->weekPreview);
    }
}
