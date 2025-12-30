<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\AvailabilityResource\Pages;

use App\Filament\Owner\Resources\AvailabilityResource;
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
use Livewire\Attributes\Url;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * AvailabilityCalendar Page for Owner Panel
 *
 * Visual calendar-based availability management across all halls.
 * This is a basic implementation - will be enhanced with FullCalendar plugin.
 *
 * Features:
 * - View all halls availability in calendar format
 * - Filter by hall
 * - Month navigation
 * - Quick block/unblock
 * - Mobile responsive
 */
class AvailabilityCalendar extends Page implements HasForms
{
    use InteractsWithForms;

    /**
     * The resource this page belongs to.
     */
    protected static string $resource = AvailabilityResource::class;

    /**
     * The view for this page.
     */
    protected static string $view = 'filament.owner.resources.availability-resource.pages.availability-calendar';

    /**
     * Current month being viewed.
     */
    #[Url]
    public int $currentMonth;

    /**
     * Current year being viewed.
     */
    #[Url]
    public int $currentYear;

    /**
     * Selected hall filter (null = all halls).
     */
    #[Url]
    public ?int $selectedHallId = null;

    /**
     * Selected dates for bulk operations.
     *
     * @var array<string>
     */
    public array $selectedDates = [];

    /**
     * Selected time slots for operations.
     *
     * @var array<string>
     */
    public array $selectedSlots = [];

    /**
     * Block reason for bulk operations.
     */
    public string $blockReason = 'blocked';

    /**
     * Custom price input.
     */
    public ?float $customPriceInput = null;

    /**
     * View mode (month/week).
     */
    public string $viewMode = 'month';

    /**
     * Mount the page.
     */
    public function mount(): void
    {
        $this->currentMonth = $this->currentMonth ?? now()->month;
        $this->currentYear = $this->currentYear ?? now()->year;
        $this->selectedSlots = ['morning', 'afternoon', 'evening', 'full_day'];
    }

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.availability_resource.calendar.title');
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return __('owner.availability_resource.calendar.heading');
    }

    /**
     * Get the subheading.
     */
    public function getSubheading(): ?string
    {
        return __('owner.availability_resource.calendar.subheading');
    }

    /**
     * Get header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // List View
            Actions\Action::make('list_view')
                ->label(__('owner.availability_resource.actions.list_view'))
                ->icon('heroicon-o-list-bullet')
                ->color('gray')
                ->url(fn () => AvailabilityResource::getUrl('index')),

            // Bulk Generate
            Actions\Action::make('bulk_generate')
                ->label(__('owner.availability_resource.actions.bulk_generate'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('hall_id')
                        ->label(__('owner.availability_resource.fields.hall'))
                        ->options($this->getOwnerHalls()->mapWithKeys(fn ($hall) => [
                            $hall->id => $hall->getTranslation('name', app()->getLocale())
                        ]))
                        ->required()
                        ->native(false)
                        ->default($this->selectedHallId),

                    Forms\Components\TextInput::make('days')
                        ->label(__('owner.availability_resource.fields.days_to_generate'))
                        ->numeric()
                        ->default(90)
                        ->minValue(1)
                        ->maxValue(365)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $hall = Hall::findOrFail($data['hall_id']);

                    if ($hall->owner_id !== Auth::id()) {
                        Notification::make()
                            ->danger()
                            ->title(__('owner.errors.unauthorized'))
                            ->send();
                        return;
                    }

                    $hall->generateAvailability((int) $data['days']);

                    Notification::make()
                        ->success()
                        ->title(__('owner.availability_resource.notifications.generated'))
                        ->send();

                    unset($this->calendarData);
                }),
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
     * Get the calendar data for the current month.
     */
    #[Computed]
    public function calendarData(): Collection
    {
        $user = Auth::user();
        $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->endOfMonth();

        // Get availabilities for all owner's halls or filtered hall
        $query = HallAvailability::whereHas('hall', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            })
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with('hall');

        if ($this->selectedHallId) {
            $query->where('hall_id', $this->selectedHallId);
        }

        $availabilities = $query->get()
            ->groupBy(fn ($item) => $item->date->format('Y-m-d'));

        $calendar = collect();
        $currentDate = $startDate->copy();
        $timeSlots = ['morning', 'afternoon', 'evening', 'full_day'];

        while ($currentDate <= $endDate) {
            $dateString = $currentDate->toDateString();
            $dayAvailabilities = $availabilities->get($dateString, collect());

            // Group by hall for multi-hall view
            $hallsData = [];
            if ($this->selectedHallId) {
                // Single hall view
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
                $hallsData = ['slots' => $slots];
            } else {
                // Multi-hall summary
                $totalSlots = count($timeSlots) * $this->getOwnerHalls->count();
                $availableSlots = $dayAvailabilities->where('is_available', true)->count();
                $bookedSlots = $dayAvailabilities->where('reason', 'booked')->count();
                $blockedSlots = $dayAvailabilities->where('is_available', false)->where('reason', '!=', 'booked')->count();

                $hallsData = [
                    'summary' => true,
                    'total' => $totalSlots,
                    'available' => $availableSlots,
                    'booked' => $bookedSlots,
                    'blocked' => $blockedSlots,
                ];
            }

            $calendar->push([
                'date' => $dateString,
                'day' => $currentDate->day,
                'dayOfWeek' => $currentDate->dayOfWeek,
                'dayName' => $currentDate->locale(app()->getLocale())->shortDayName,
                'isToday' => $currentDate->isToday(),
                'isPast' => $currentDate->isPast() && !$currentDate->isToday(),
                'isWeekend' => $currentDate->isWeekend(),
                'data' => $hallsData,
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
     * Get day names for the calendar header.
     */
    #[Computed]
    public function dayNames(): array
    {
        $locale = app()->getLocale();
        return [
            ['short' => $locale === 'ar' ? 'أحد' : 'Sun', 'full' => $locale === 'ar' ? 'الأحد' : 'Sunday'],
            ['short' => $locale === 'ar' ? 'اثن' : 'Mon', 'full' => $locale === 'ar' ? 'الاثنين' : 'Monday'],
            ['short' => $locale === 'ar' ? 'ثلا' : 'Tue', 'full' => $locale === 'ar' ? 'الثلاثاء' : 'Tuesday'],
            ['short' => $locale === 'ar' ? 'أرب' : 'Wed', 'full' => $locale === 'ar' ? 'الأربعاء' : 'Wednesday'],
            ['short' => $locale === 'ar' ? 'خمي' : 'Thu', 'full' => $locale === 'ar' ? 'الخميس' : 'Thursday'],
            ['short' => $locale === 'ar' ? 'جمع' : 'Fri', 'full' => $locale === 'ar' ? 'الجمعة' : 'Friday'],
            ['short' => $locale === 'ar' ? 'سبت' : 'Sat', 'full' => $locale === 'ar' ? 'السبت' : 'Saturday'],
        ];
    }

    /**
     * Navigate to previous month.
     */
    public function previousMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->clearSelection();
        unset($this->calendarData);
    }

    /**
     * Navigate to next month.
     */
    public function nextMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->clearSelection();
        unset($this->calendarData);
    }

    /**
     * Navigate to current month.
     */
    public function goToToday(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->clearSelection();
        unset($this->calendarData);
    }

    /**
     * Set the selected hall filter.
     */
    public function setHallFilter(?int $hallId): void
    {
        $this->selectedHallId = $hallId;
        $this->clearSelection();
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
     * Select all future dates in month.
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
     * Toggle single slot availability.
     */
    public function toggleSlot(string $date, string $slot): void
    {
        if (!$this->selectedHallId) {
            Notification::make()
                ->warning()
                ->title(__('owner.availability_resource.notifications.select_hall_first'))
                ->send();
            return;
        }

        $availability = HallAvailability::firstOrCreate(
            [
                'hall_id' => $this->selectedHallId,
                'date' => $date,
                'time_slot' => $slot,
            ],
            [
                'is_available' => true,
            ]
        );

        if ($availability->is_available) {
            $availability->block($this->blockReason);
        } else {
            $availability->unblock();
        }

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
        if (!$this->selectedHallId) {
            Notification::make()
                ->warning()
                ->title(__('owner.availability_resource.notifications.select_hall_first'))
                ->send();
            return;
        }

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
                        'hall_id' => $this->selectedHallId,
                        'date' => $date,
                        'time_slot' => $slot,
                    ],
                    [
                        'is_available' => false,
                        'reason' => $this->blockReason,
                    ]
                );
                $count++;
            }
        }

        $this->clearSelection();

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
        if (!$this->selectedHallId) {
            Notification::make()
                ->warning()
                ->title(__('owner.availability_resource.notifications.select_hall_first'))
                ->send();
            return;
        }

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
                $availability = HallAvailability::where([
                    'hall_id' => $this->selectedHallId,
                    'date' => $date,
                    'time_slot' => $slot,
                ])->first();

                if ($availability && !$availability->is_available && $availability->reason !== 'booked') {
                    $availability->unblock();
                    $count++;
                }
            }
        }

        $this->clearSelection();

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
        if (!$this->selectedHallId) {
            Notification::make()
                ->warning()
                ->title(__('owner.availability_resource.notifications.select_hall_first'))
                ->send();
            return;
        }

        if (empty($this->selectedDates) || empty($this->selectedSlots)) {
            Notification::make()
                ->warning()
                ->title(__('owner.availability.notifications.no_selection'))
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

        foreach ($this->selectedDates as $date) {
            foreach ($this->selectedSlots as $slot) {
                HallAvailability::updateOrCreate(
                    [
                        'hall_id' => $this->selectedHallId,
                        'date' => $date,
                        'time_slot' => $slot,
                    ],
                    [
                        'is_available' => true,
                    ]
                )->setCustomPrice($this->customPriceInput);
            }
        }

        $this->clearSelection();
        $this->customPriceInput = null;

        Notification::make()
            ->success()
            ->title(__('owner.availability.notifications.price_updated'))
            ->send();

        unset($this->calendarData);
    }
}
