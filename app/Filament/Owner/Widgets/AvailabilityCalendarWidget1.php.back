<?php

declare(strict_types=1);

namespace App\Filament\Owner\Widgets;

use App\Models\Hall;
use App\Models\HallAvailability;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Actions;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

/**
 * AvailabilityCalendarWidget for Owner Panel
 *
 * Full-featured calendar widget using saade/filament-fullcalendar plugin.
 * Displays hall availability slots with color-coded status.
 *
 * Features:
 * - Month/Week/Day views
 * - Drag-and-drop event editing
 * - Click-to-create availability
 * - Color-coded by status (available, blocked, booked, maintenance)
 * - Filter by hall
 * - Mobile responsive
 *
 * @package App\Filament\Owner\Widgets
 */
class AvailabilityCalendarWidget extends FullCalendarWidget
{
    /**
     * The model for CRUD actions.
     */
    public Model|string|null $model = HallAvailability::class;

    /**
     * Selected hall filter (null = all halls).
     */
    public ?int $selectedHallId = null;

    /**
     * Widget sort order on dashboard.
     */
    protected static ?int $sort = 1;

    /**
     * Column span for the widget.
     */
    protected int|string|array $columnSpan = 'full';

    /**
     * Get the widget heading.
     */
    public function getHeading(): ?string
    {
        return __('owner.fullcalendar.heading');
    }

    /**
     * Determine if widget should display on dashboard.
     */
    public static function canView(): bool
    {
        // Show on dashboard - you can add custom logic here
        return true;
    }

    /**
     * Configure FullCalendar options for this widget.
     *
     * @return array<string, mixed>
     */
    public function config(): array
    {
        $locale = app()->getLocale();
        $isRtl = $locale === 'ar';

        return [
            // Initial view
            'initialView' => 'dayGridMonth',

            // Header toolbar configuration
            'headerToolbar' => [
                'left' => $isRtl ? 'next,prev today' : 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,listWeek',
            ],

            // Footer toolbar for mobile
            'footerToolbar' => [
                'center' => 'dayGridMonth,timeGridWeek,listWeek',
            ],

            // Locale and direction
            'locale' => $locale,
            'direction' => $isRtl ? 'rtl' : 'ltr',

            // Week starts on Saturday (Omani weekend: Fri-Sat)
            'firstDay' => 6,

            // Time settings
            'slotMinTime' => '06:00:00',
            'slotMaxTime' => '24:00:00',
            'slotDuration' => '01:00:00',
            'slotLabelInterval' => '02:00:00',

            // Display settings
            'allDaySlot' => true,
            'nowIndicator' => true,
            'dayMaxEvents' => 4, // Show "+more" link when >4 events
            'eventDisplay' => 'block',
            'displayEventTime' => true,
            'displayEventEnd' => false,
            'navLinks' => true, // Can click day names to navigate

            // Interaction
            'selectable' => true,
            'selectMirror' => true,
            'editable' => true,
            'eventDurationEditable' => false, // Don't allow resizing

            // Event time format
            'eventTimeFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'meridiem' => 'short',
            ],

            // Responsive
            'handleWindowResize' => true,
            'expandRows' => true,
            'stickyHeaderDates' => true,

            // Height
            'height' => 'auto',
            'contentHeight' => 650,

            // Business hours (Omani business hours)
            'businessHours' => [
                [
                    'daysOfWeek' => [0, 1, 2, 3, 4], // Sun-Thu
                    'startTime' => '08:00',
                    'endTime' => '22:00',
                ],
            ],

            // Date constraints
            'validRange' => [
                'start' => now()->startOfMonth()->toDateString(),
            ],

            // Views configuration
            'views' => [
                'dayGridMonth' => [
                    'dayMaxEvents' => 3,
                ],
                'timeGridWeek' => [
                    'slotDuration' => '01:00:00',
                ],
                'listWeek' => [
                    'noEventsContent' => __('owner.fullcalendar.no_events'),
                ],
            ],
        ];
    }

    /**
     * Fetch events (availability slots) for the calendar.
     *
     * @param array{start: string, end: string, timezone: string} $fetchInfo
     * @return array<int, EventData|array>
     */
    public function fetchEvents(array $fetchInfo): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        // Build query for owner's hall availabilities
        $query = HallAvailability::query()
            ->whereHas('hall', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            })
            ->whereBetween('date', [$fetchInfo['start'], $fetchInfo['end']])
            ->with('hall');

        // Apply hall filter if selected
        if ($this->selectedHallId) {
            $query->where('hall_id', $this->selectedHallId);
        }

        return $query->get()
            ->map(function (HallAvailability $availability) {
                // Determine color based on status
                $color = $this->getEventColor($availability);

                // Build event title
                $title = $this->getEventTitle($availability);

                // Calculate start/end times based on time slot
                [$startTime, $endTime] = $this->getSlotTimes($availability->time_slot);

                $startDateTime = $availability->date->format('Y-m-d') . 'T' . $startTime;
                $endDateTime = $availability->date->format('Y-m-d') . 'T' . $endTime;

                return EventData::make()
                    ->id((string) $availability->id)
                    ->title($title)
                    ->start($startDateTime)
                    ->end($endDateTime)
                    ->backgroundColor($color)
                    ->borderColor($color)
                    ->textColor('#ffffff')
                    ->extendedProps([
                        'hall_id' => $availability->hall_id,
                        'hall_name' => $availability->hall->getTranslation('name', app()->getLocale()),
                        'time_slot' => $availability->time_slot,
                        'is_available' => $availability->is_available,
                        'reason' => $availability->reason,
                        'custom_price' => $availability->custom_price,
                    ]);
            })
            ->toArray();
    }

    /**
     * Get event color based on availability status.
     */
    protected function getEventColor(HallAvailability $availability): string
    {
        // Past dates
        if ($availability->date->isPast() && !$availability->date->isToday()) {
            return '#9ca3af'; // Gray
        }

        // Available
        if ($availability->is_available) {
            return '#22c55e'; // Green
        }

        // Blocked by reason
        return match ($availability->reason) {
            'booked' => '#3b82f6',       // Blue
            'maintenance' => '#f59e0b',   // Amber
            'holiday' => '#a855f7',       // Purple
            'renovation' => '#ec4899',    // Pink
            'private_event' => '#6366f1', // Indigo
            default => '#ef4444',         // Red (blocked)
        };
    }

    /**
     * Get event title with hall name and status.
     */
    protected function getEventTitle(HallAvailability $availability): string
    {
        $hallName = $availability->hall->getTranslation('name', app()->getLocale());
        $slotLabel = $this->getSlotLabel($availability->time_slot);

        // If only one hall is selected, don't show hall name
        if ($this->selectedHallId) {
            $statusLabel = $availability->is_available
                ? __('owner.availability.available')
                : __("owner.availability.reasons.{$availability->reason}");

            return "{$slotLabel} - {$statusLabel}";
        }

        // Show hall name for multi-hall view
        return "{$hallName} ({$slotLabel})";
    }

    /**
     * Get slot label.
     */
    protected function getSlotLabel(string $slot): string
    {
        $locale = app()->getLocale();

        $labels = [
            'morning' => ['en' => 'M', 'ar' => 'ص'],
            'afternoon' => ['en' => 'A', 'ar' => 'ظ'],
            'evening' => ['en' => 'E', 'ar' => 'م'],
            'full_day' => ['en' => 'F', 'ar' => 'ك'],
        ];

        return $labels[$slot][$locale] ?? $slot;
    }

    /**
     * Get start and end times for a time slot.
     *
     * @return array{0: string, 1: string}
     */
    protected function getSlotTimes(string $slot): array
    {
        return match ($slot) {
            'morning' => ['08:00:00', '12:00:00'],
            'afternoon' => ['13:00:00', '17:00:00'],
            'evening' => ['18:00:00', '23:00:00'],
            'full_day' => ['08:00:00', '23:00:00'],
            default => ['08:00:00', '12:00:00'],
        };
    }

    /**
     * Define form schema for viewing/editing availability.
     */
    public function getFormSchema(): array
    {
        $user = Auth::user();

        return [
            Forms\Components\Grid::make(2)
                ->schema([
                    // Hall Selection
                    Forms\Components\Select::make('hall_id')
                        ->label(__('owner.availability_resource.fields.hall'))
                        ->options(function () use ($user) {
                            return Hall::where('owner_id', $user?->id)
                                ->get()
                                ->mapWithKeys(fn ($hall) => [
                                    $hall->id => $hall->getTranslation('name', app()->getLocale())
                                ]);
                        })
                        ->required()
                        ->native(false)
                        ->searchable()
                        ->default($this->selectedHallId),

                    // Date
                    Forms\Components\DatePicker::make('date')
                        ->label(__('owner.availability_resource.fields.date'))
                        ->required()
                        ->native(false)
                        ->displayFormat('d M Y'),

                    // Time Slot
                    Forms\Components\Select::make('time_slot')
                        ->label(__('owner.availability_resource.fields.time_slot'))
                        ->options([
                            'morning' => __('owner.slots.morning'),
                            'afternoon' => __('owner.slots.afternoon'),
                            'evening' => __('owner.slots.evening'),
                            'full_day' => __('owner.slots.full_day'),
                        ])
                        ->required()
                        ->native(false),

                    // Available Toggle
                    Forms\Components\Toggle::make('is_available')
                        ->label(__('owner.availability_resource.fields.is_available'))
                        ->default(true)
                        ->live(),
                ]),

            // Block reason (visible when not available)
            Forms\Components\Select::make('reason')
                ->label(__('owner.availability_resource.fields.reason'))
                ->options([
                    'blocked' => __('owner.availability.reasons.blocked'),
                    'maintenance' => __('owner.availability.reasons.maintenance'),
                    'holiday' => __('owner.availability.reasons.holiday'),
                    'private_event' => __('owner.availability.reasons.private_event'),
                    'renovation' => __('owner.availability.reasons.renovation'),
                    'other' => __('owner.availability.reasons.other'),
                ])
                ->default('blocked')
                ->native(false)
                ->visible(fn (Forms\Get $get): bool => !$get('is_available'))
                ->requiredIf('is_available', false),

            // Custom Price
            Forms\Components\TextInput::make('custom_price')
                ->label(__('owner.availability_resource.fields.custom_price'))
                ->numeric()
                ->minValue(0)
                ->step(0.001)
                ->prefix('OMR')
                ->placeholder(__('owner.availability_resource.placeholders.use_hall_price')),

            // Notes
            Forms\Components\Textarea::make('notes')
                ->label(__('owner.availability_resource.fields.notes'))
                ->rows(2)
                ->maxLength(500),
        ];
    }

    /**
     * Define modal actions.
     *
     * @return array<Actions\CreateAction|Actions\EditAction|Actions\DeleteAction>
     */
    protected function modalActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('owner.fullcalendar.actions.create'))
                ->mountUsing(function (Forms\Form $form, array $arguments): void {
                    // Pre-fill form with selected date/time from calendar click
                    $form->fill([
                        'hall_id' => $this->selectedHallId,
                        'date' => $arguments['start'] ?? now()->toDateString(),
                        'time_slot' => $this->determineTimeSlot($arguments['start'] ?? null),
                        'is_available' => true,
                    ]);
                })
                ->mutateFormDataUsing(function (array $data): array {
                    // Ensure reason is null when available
                    if ($data['is_available'] ?? true) {
                        $data['reason'] = null;
                    }
                    return $data;
                }),

            Actions\EditAction::make()
                ->label(__('owner.fullcalendar.actions.edit'))
                ->mountUsing(function (HallAvailability $record, Forms\Form $form, array $arguments): void {
                    // Fill form with existing data, considering any drag updates
                    $form->fill([
                        'hall_id' => $record->hall_id,
                        'date' => $arguments['event']['start'] ?? $record->date->format('Y-m-d'),
                        'time_slot' => $record->time_slot,
                        'is_available' => $record->is_available,
                        'reason' => $record->reason,
                        'custom_price' => $record->custom_price,
                        'notes' => $record->notes,
                    ]);
                })
                ->mutateFormDataUsing(function (array $data): array {
                    if ($data['is_available'] ?? true) {
                        $data['reason'] = null;
                    }
                    return $data;
                }),

            Actions\DeleteAction::make()
                ->label(__('owner.fullcalendar.actions.delete')),
        ];
    }

    /**
     * Determine time slot from datetime string.
     */
    protected function determineTimeSlot(?string $datetime): string
    {
        if (!$datetime) {
            return 'morning';
        }

        $hour = (int) date('H', strtotime($datetime));

        return match (true) {
            $hour < 12 => 'morning',
            $hour < 17 => 'afternoon',
            default => 'evening',
        };
    }

    /**
     * Handle date click (create new availability).
     */
    // public function onDateSelect(string $start, ?string $end, bool $allDay, ?array $view): void
    // {
    //     // Prevent creating in the past
    //     if (strtotime($start) < strtotime('today')) {
    //         Notification::make()
    //             ->warning()
    //             ->title(__('owner.fullcalendar.notifications.cannot_select_past'))
    //             ->send();
    //         return;
    //     }

    //     // If no hall selected, prompt user
    //     if (!$this->selectedHallId) {
    //         Notification::make()
    //             ->warning()
    //             ->title(__('owner.fullcalendar.notifications.select_hall_first'))
    //             ->send();
    //         return;
    //     }

    //     // Mount create action with selected date
    //     $this->mountAction('create', [
    //         'start' => $start,
    //         'end' => $end,
    //         'allDay' => $allDay,
    //     ]);
    // }

    /**
     * Handle event drop (drag and drop).
     */
    // public function onEventDrop(array $event, array $oldEvent, array $relatedEvents, array $delta): bool
    // {
    //     $availability = HallAvailability::find($event['id']);

    //     if (!$availability) {
    //         return false;
    //     }

    //     // Verify ownership
    //     $user = Auth::user();
    //     if ($availability->hall->owner_id !== $user->id) {
    //         Notification::make()
    //             ->danger()
    //             ->title(__('owner.errors.unauthorized'))
    //             ->send();
    //         return false;
    //     }

    //     // Prevent moving to past
    //     $newDate = date('Y-m-d', strtotime($event['start']));
    //     if (strtotime($newDate) < strtotime('today')) {
    //         Notification::make()
    //             ->warning()
    //             ->title(__('owner.fullcalendar.notifications.cannot_move_to_past'))
    //             ->send();
    //         return false;
    //     }

    //     // Check if booked (cannot move booked slots)
    //     if ($availability->reason === 'booked') {
    //         Notification::make()
    //             ->warning()
    //             ->title(__('owner.fullcalendar.notifications.cannot_move_booked'))
    //             ->send();
    //         return false;
    //     }

    //     // Update the date
    //     $availability->update(['date' => $newDate]);

    //     Notification::make()
    //         ->success()
    //         ->title(__('owner.fullcalendar.notifications.moved_success'))
    //         ->send();

    //     return true;
    // }

    /**
     * Handle event click.
     */
    public function onEventClick(array $event): void
    {
        // Load the record
        $this->record = $this->resolveRecord($event['id']);

        // Mount view/edit action
        $this->mountAction('edit', [
            'type' => 'click',
            'event' => $event,
        ]);
    }

    /**
     * Resolve record from ID.
     */
    public function resolveRecord(string|int $key): Model
    {
        return HallAvailability::findOrFail($key);
    }

    /**
     * Define header actions (hall filter, etc.).
     *
     * @return array<\Filament\Actions\Action>
     */
    protected function headerActions(): array
    {
        $user = Auth::user();

        return [
            // Hall Filter Action
            \Filament\Actions\Action::make('filter_hall')
                ->label($this->selectedHallId
                    ? Hall::find($this->selectedHallId)?->getTranslation('name', app()->getLocale()) ?? __('owner.fullcalendar.all_halls')
                    : __('owner.fullcalendar.all_halls'))
                ->icon('heroicon-o-funnel')
                ->color('gray')
                ->form([
                    Forms\Components\Select::make('hall_id')
                        ->label(__('owner.fullcalendar.select_hall'))
                        ->options(function () use ($user) {
                            return Hall::where('owner_id', $user?->id)
                                ->get()
                                ->mapWithKeys(fn ($hall) => [
                                    $hall->id => $hall->getTranslation('name', app()->getLocale())
                                ])
                                ->prepend(__('owner.fullcalendar.all_halls'), '');
                        })
                        ->default($this->selectedHallId)
                        ->native(false)
                        ->searchable(),
                ])
                ->action(function (array $data): void {
                    $this->selectedHallId = $data['hall_id'] ?: null;
                    $this->refreshEvents();
                }),

            // Generate Availability Action
            \Filament\Actions\Action::make('generate')
                ->label(__('owner.fullcalendar.actions.generate'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('hall_id')
                        ->label(__('owner.availability_resource.fields.hall'))
                        ->options(function () use ($user) {
                            return Hall::where('owner_id', $user?->id)
                                ->get()
                                ->mapWithKeys(fn ($hall) => [
                                    $hall->id => $hall->getTranslation('name', app()->getLocale())
                                ]);
                        })
                        ->required()
                        ->native(false)
                        ->searchable()
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
                    $hall = Hall::find($data['hall_id']);

                    if (!$hall || $hall->owner_id !== Auth::id()) {
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
                        ->body(__('owner.availability_resource.notifications.generated_body', [
                            'days' => $data['days'],
                            'hall' => $hall->getTranslation('name', app()->getLocale()),
                        ]))
                        ->send();

                    $this->refreshEvents();
                }),

            // List View Link
            \Filament\Actions\Action::make('list_view')
                ->label(__('owner.availability_resource.actions.list_view'))
                ->icon('heroicon-o-list-bullet')
                ->color('gray')
                ->url(fn () => \App\Filament\Owner\Resources\AvailabilityResource::getUrl('index')),
        ];
    }

    /**
     * JavaScript for event tooltip on hover.
     */
    public function eventDidMount(): string
    {
        return <<<JS
        function({ event, el }) {
            // Create tooltip content
            const props = event.extendedProps;
            let tooltipContent = '';

            if (props.hall_name) {
                tooltipContent += '<strong>' + props.hall_name + '</strong><br>';
            }

            const slotLabels = {
                'morning': 'Morning (8 AM - 12 PM)',
                'afternoon': 'Afternoon (1 PM - 5 PM)',
                'evening': 'Evening (6 PM - 11 PM)',
                'full_day': 'Full Day'
            };

            if (props.time_slot) {
                tooltipContent += slotLabels[props.time_slot] || props.time_slot;
                tooltipContent += '<br>';
            }

            if (props.is_available) {
                tooltipContent += '<span style="color: #22c55e;">✓ Available</span>';
            } else {
                const reasons = {
                    'booked': 'Booked',
                    'maintenance': 'Maintenance',
                    'blocked': 'Blocked',
                    'holiday': 'Holiday',
                    'private_event': 'Private Event',
                    'renovation': 'Renovation',
                    'other': 'Other'
                };
                tooltipContent += '<span style="color: #ef4444;">✗ ' + (reasons[props.reason] || 'Unavailable') + '</span>';
            }

            if (props.custom_price) {
                tooltipContent += '<br><small>OMR ' + parseFloat(props.custom_price).toFixed(3) + '</small>';
            }

            // Apply tooltip using browser title (simple approach)
            el.setAttribute('title', tooltipContent.replace(/<[^>]*>/g, ' ').replace(/\\s+/g, ' ').trim());

            // Or use a tooltip library if available
            if (typeof tippy !== 'undefined') {
                tippy(el, {
                    content: tooltipContent,
                    allowHTML: true,
                    placement: 'top',
                    theme: 'light-border',
                });
            }
        }
        JS;
    }

    /**
     * Refresh calendar events.
     */
    public function refreshEvents(): void
    {
        $this->dispatch('filament-fullcalendar--refresh');
    }
}
