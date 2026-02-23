<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\Pages;

use App\Filament\Owner\Resources\HallResource;
use App\Models\Hall;
use App\Models\HallAvailability;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * ManageHallAvailability Page for Owner Panel
 *
 * Calendar-based availability management for hall owners.
 *
 * Features:
 * - Visual calendar view
 * - Bulk block/unblock operations
 * - Custom pricing per slot
 * - Date range selection
 */
class ManageHallAvailability extends Page implements HasForms
{
    use InteractsWithForms;

    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = HallResource::class;

    /**
     * The view for this page.
     *
     * @var string
     */
    protected static string $view = 'filament.owner.resources.hall-resource.pages.manage-hall-availability';

    /**
     * The hall record.
     */
    public Hall $record;

    /**
     * Current month being viewed.
     */
    public int $currentMonth;

    /**
     * Current year being viewed.
     */
    public int $currentYear;

    /**
     * Selected dates for bulk operations.
     *
     * @var array<string>
     */
    public array $selectedDates = [];

    /**
     * Selected time slots for bulk operations.
     *
     * @var array<string>
     */
    public array $selectedSlots = [];

    /**
     * Form data for bulk operations.
     *
     * @var array<string, mixed>
     */
    public array $bulkData = [];

    /**
     * Custom price input value.
     */
    public ?float $customPriceInput = null;

    /**
     * Block reason selection.
     */
    public string $blockReason = 'blocked';

    /**
     * Mount the page.
     */
    public function mount(int|string $record): void
    {
        $this->record = Hall::findOrFail($record);

        // Verify ownership
        $user = Auth::user();
        if ($this->record->owner_id !== $user->id) {
            abort(403, __('owner.errors.unauthorized'));
        }

        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->selectedSlots = ['morning', 'afternoon', 'evening', 'full_day'];
    }

    /**
     * Get the title for the page.
     */
    public function getTitle(): string
    {
        return __('owner.availability.title', [
            'hall' => $this->record->getTranslation('name', app()->getLocale()),
        ]);
    }

    /**
     * Get breadcrumbs for the page.
     *
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            HallResource::getUrl() => __('owner.halls.plural'),
            HallResource::getUrl('edit', ['record' => $this->record]) => $this->record->getTranslation('name', app()->getLocale()),
            '' => __('owner.availability.breadcrumb'),
        ];
    }

    /**
     * Get the header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label(__('owner.availability.actions.back'))
                ->url(HallResource::getUrl('edit', ['record' => $this->record]))
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),

            Actions\Action::make('regenerate')
                ->label(__('owner.availability.actions.regenerate'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('owner.availability.modals.regenerate_heading'))
                ->modalDescription(__('owner.availability.modals.regenerate_description'))
                ->action(function () {
                    $this->record->generateAvailability();

                    Notification::make()
                        ->success()
                        ->title(__('owner.availability.notifications.regenerated'))
                        ->send();
                }),
        ];
    }

    /**
     * Get the calendar data for the current month.
     */
    #[Computed]
    public function calendarData(): Collection
    {
        $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->endOfMonth();

        // Use simple query compatible with existing model
        $availabilities = HallAvailability::where('hall_id', $this->record->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->groupBy(fn ($item) => $item->date->format('Y-m-d'));

        $calendar = collect();
        $currentDate = $startDate->copy();
        $timeSlots = ['morning', 'afternoon', 'evening', 'full_day'];

        while ($currentDate <= $endDate) {
            $dateString = $currentDate->toDateString();
            $dayAvailabilities = $availabilities->get($dateString, collect());

            $slots = [];
            foreach ($timeSlots as $slotKey) {
                $availability = $dayAvailabilities->firstWhere('time_slot', $slotKey);
                $slots[$slotKey] = [
                    'available' => $availability?->is_available ?? true,
                    'reason' => $availability?->reason,
                    'custom_price' => $availability?->custom_price,
                    'id' => $availability?->id,
                ];
            }

            $calendar->push([
                'date' => $dateString,
                'day' => $currentDate->day,
                'dayOfWeek' => $currentDate->dayOfWeek,
                'isToday' => $currentDate->isToday(),
                'isPast' => $currentDate->isPast() && !$currentDate->isToday(),
                'isWeekend' => $currentDate->isWeekend(),
                'slots' => $slots,
            ]);

            $currentDate->addDay();
        }

        return $calendar;
    }

    /**
     * Get the month name.
     */
    #[Computed]
    public function monthName(): string
    {
        return Carbon::create($this->currentYear, $this->currentMonth, 1)
            ->locale(app()->getLocale())
            ->isoFormat('MMMM YYYY');
    }

    /**
     * Navigate to previous month.
     */
    public function previousMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    /**
     * Navigate to next month.
     */
    public function nextMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    /**
     * Navigate to current month.
     */
    public function goToToday(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    /**
     * Toggle a specific slot availability.
     */
    public function toggleSlot(string $date, string $slot): void
    {
        $availability = HallAvailability::firstOrCreate(
            [
                'hall_id' => $this->record->id,
                'date' => $date,
                'time_slot' => $slot,
            ],
            [
                'is_available' => true,
            ]
        );

        $availability->update([
            'is_available' => !$availability->is_available,
            'reason' => $availability->is_available ? 'blocked' : null,
        ]);

        Notification::make()
            ->success()
            ->title($availability->is_available
                ? __('owner.availability.notifications.unblocked')
                : __('owner.availability.notifications.blocked'))
            ->send();

        unset($this->calendarData);
    }

    /**
     * Block selected dates/slots.
     */
    public function blockSelected(): void
    {
        if (empty($this->selectedDates) || empty($this->selectedSlots)) {
            Notification::make()
                ->warning()
                ->title(__('owner.availability.notifications.no_selection'))
                ->body(__('owner.availability.notifications.select_dates_slots'))
                ->send();
            return;
        }

        $count = 0;
        foreach ($this->selectedDates as $date) {
            foreach ($this->selectedSlots as $slot) {
                HallAvailability::updateOrCreate(
                    [
                        'hall_id' => $this->record->id,
                        'date' => $date,
                        'time_slot' => $slot,
                    ],
                    [
                        'is_available' => false,
                        'reason' => $this->blockReason ?? 'blocked',
                    ]
                );
                $count++;
            }
        }

        $this->selectedDates = [];

        Notification::make()
            ->success()
            ->title(__('owner.availability.notifications.slots_blocked'))
            ->body(__('owner.availability.notifications.slots_blocked_count', ['count' => $count]))
            ->send();

        unset($this->calendarData);
    }

    /**
     * Unblock selected dates/slots.
     */
    public function unblockSelected(): void
    {
        if (empty($this->selectedDates) || empty($this->selectedSlots)) {
            Notification::make()
                ->warning()
                ->title(__('owner.availability.notifications.no_selection'))
                ->body(__('owner.availability.notifications.select_dates_slots'))
                ->send();
            return;
        }

        $count = 0;
        foreach ($this->selectedDates as $date) {
            foreach ($this->selectedSlots as $slot) {
                HallAvailability::updateOrCreate(
                    [
                        'hall_id' => $this->record->id,
                        'date' => $date,
                        'time_slot' => $slot,
                    ],
                    [
                        'is_available' => true,
                        'reason' => null,
                    ]
                );
                $count++;
            }
        }

        $this->selectedDates = [];

        Notification::make()
            ->success()
            ->title(__('owner.availability.notifications.slots_unblocked'))
            ->body(__('owner.availability.notifications.slots_unblocked_count', ['count' => $count]))
            ->send();

        unset($this->calendarData);
    }

    /**
     * Set custom price for selected dates/slots.
     */
    public function setCustomPrice(): void
    {
        if (empty($this->selectedDates) || empty($this->selectedSlots)) {
            Notification::make()
                ->warning()
                ->title(__('owner.availability.notifications.no_selection'))
                ->body(__('owner.availability.notifications.select_dates_slots'))
                ->send();
            return;
        }

        if ($this->customPriceInput === null || $this->customPriceInput < 0) {
            Notification::make()
                ->warning()
                ->title(__('owner.availability.notifications.invalid_price'))
                ->send();
            return;
        }

        HallAvailability::where('hall_id', $this->record->id)
            ->whereIn('date', $this->selectedDates)
            ->whereIn('time_slot', $this->selectedSlots)
            ->update(['custom_price' => $this->customPriceInput]);

        // Create records for dates that don't exist yet
        foreach ($this->selectedDates as $date) {
            foreach ($this->selectedSlots as $slot) {
                HallAvailability::firstOrCreate(
                    [
                        'hall_id' => $this->record->id,
                        'date' => $date,
                        'time_slot' => $slot,
                    ],
                    [
                        'is_available' => true,
                        'custom_price' => $this->customPriceInput,
                    ]
                );
            }
        }

        $this->selectedDates = [];
        $this->customPriceInput = null;

        Notification::make()
            ->success()
            ->title(__('owner.availability.notifications.price_updated'))
            ->send();

        unset($this->calendarData);
    }

    /**
     * Clear custom price for selected dates/slots.
     */
    public function clearCustomPrice(): void
    {
        if (empty($this->selectedDates) || empty($this->selectedSlots)) {
            Notification::make()
                ->warning()
                ->title(__('owner.availability.notifications.no_selection'))
                ->body(__('owner.availability.notifications.select_dates_slots'))
                ->send();
            return;
        }

        HallAvailability::where('hall_id', $this->record->id)
            ->whereIn('date', $this->selectedDates)
            ->whereIn('time_slot', $this->selectedSlots)
            ->update(['custom_price' => null]);

        $this->selectedDates = [];

        Notification::make()
            ->success()
            ->title(__('owner.availability.notifications.price_cleared'))
            ->send();

        unset($this->calendarData);
    }

    /**
     * Toggle date selection.
     */
    public function toggleDateSelection(string $date): void
    {
        if (in_array($date, $this->selectedDates)) {
            $this->selectedDates = array_values(array_diff($this->selectedDates, [$date]));
        } else {
            $this->selectedDates[] = $date;
        }
    }

    /**
     * Select all dates in the current month.
     */
    public function selectAllDates(): void
    {
        $this->selectedDates = $this->calendarData
            ->where('isPast', false)
            ->pluck('date')
            ->toArray();
    }

    /**
     * Clear date selection.
     */
    public function clearSelection(): void
    {
        $this->selectedDates = [];
    }

    /**
     * Toggle slot selection.
     */
    public function toggleSlotSelection(string $slot): void
    {
        if (in_array($slot, $this->selectedSlots)) {
            $this->selectedSlots = array_values(array_diff($this->selectedSlots, [$slot]));
        } else {
            $this->selectedSlots[] = $slot;
        }
    }

    /**
     * Get the form for bulk operations.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('owner.availability.bulk_operations'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\CheckboxList::make('selectedSlots')
                            ->label(__('owner.availability.time_slots'))
                            ->options([
                                'morning' => __('owner.slots.morning'),
                                'afternoon' => __('owner.slots.afternoon'),
                                'evening' => __('owner.slots.evening'),
                                'full_day' => __('owner.slots.full_day'),
                            ])
                            ->default(['morning', 'afternoon', 'evening', 'full_day'])
                            ->columns(2),

                        Forms\Components\Select::make('blockReason')
                            ->label(__('owner.availability.block_reason'))
                            ->options([
                                'blocked' => __('owner.availability.reasons.blocked'),
                                'maintenance' => __('owner.availability.reasons.maintenance'),
                                'holiday' => __('owner.availability.reasons.holiday'),
                                'private_event' => __('owner.availability.reasons.private_event'),
                                'renovation' => __('owner.availability.reasons.renovation'),
                                'other' => __('owner.availability.reasons.other'),
                            ])
                            ->default('blocked'),

                        Forms\Components\TextInput::make('customPriceInput')
                            ->label(__('owner.availability.custom_price'))
                            ->numeric()
                            ->minValue(0)
                            ->step(0.001)
                            ->prefix('OMR'),
                    ]),
            ])
            ->statePath('bulkData');
    }
}
