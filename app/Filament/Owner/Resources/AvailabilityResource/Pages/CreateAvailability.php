<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\AvailabilityResource\Pages;

use App\Filament\Owner\Resources\AvailabilityResource;
use App\Models\Hall;
use App\Models\HallAvailability;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;

/**
 * CreateAvailability Page for Owner Panel
 *
 * Allows owners to create single or multiple availability slots.
 */
class CreateAvailability extends CreateRecord
{
    /**
     * The resource this page belongs to.
     */
    protected static string $resource = AvailabilityResource::class;

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.availability_resource.create.title');
    }

    /**
     * Get the form with bulk creation support.
     */
    public function form(Form $form): Form
    {
        $user = Auth::user();

        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    // Step 1: Select Hall
                    Forms\Components\Wizard\Step::make(__('owner.availability_resource.wizard.select_hall'))
                        ->icon('heroicon-o-building-office-2')
                        ->schema([
                            Forms\Components\Select::make('hall_id')
                                ->label(__('owner.availability_resource.fields.hall'))
                                ->options(function () use ($user) {
                                    return Hall::where('owner_id', $user->id)
                                        ->get()
                                        ->mapWithKeys(fn ($hall) => [
                                            $hall->id => $hall->getTranslation('name', app()->getLocale())
                                        ]);
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->native(false)
                                ->columnSpanFull(),
                        ]),

                    // Step 2: Select Dates
                    Forms\Components\Wizard\Step::make(__('owner.availability_resource.wizard.select_dates'))
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Forms\Components\Radio::make('date_mode')
                                ->label(__('owner.availability_resource.fields.date_mode'))
                                ->options([
                                    'single' => __('owner.availability_resource.date_modes.single'),
                                    'range' => __('owner.availability_resource.date_modes.range'),
                                ])
                                ->default('single')
                                ->live()
                                ->columnSpanFull(),

                            // Single Date
                            Forms\Components\DatePicker::make('date')
                                ->label(__('owner.availability_resource.fields.date'))
                                ->required()
                                ->native(false)
                                ->minDate(now())
                                ->displayFormat('d M Y')
                                ->closeOnDateSelection()
                                ->visible(fn (Forms\Get $get): bool => $get('date_mode') === 'single'),

                            // Date Range
                            Forms\Components\Grid::make(2)
                                ->visible(fn (Forms\Get $get): bool => $get('date_mode') === 'range')
                                ->schema([
                                    Forms\Components\DatePicker::make('start_date')
                                        ->label(__('owner.availability_resource.fields.start_date'))
                                        ->required()
                                        ->native(false)
                                        ->minDate(now())
                                        ->displayFormat('d M Y')
                                        ->closeOnDateSelection(),

                                    Forms\Components\DatePicker::make('end_date')
                                        ->label(__('owner.availability_resource.fields.end_date'))
                                        ->required()
                                        ->native(false)
                                        ->minDate(now())
                                        ->displayFormat('d M Y')
                                        ->closeOnDateSelection()
                                        ->afterOrEqual('start_date'),
                                ]),
                        ]),

                    // Step 3: Select Time Slots
                    Forms\Components\Wizard\Step::make(__('owner.availability_resource.wizard.select_slots'))
                        ->icon('heroicon-o-clock')
                        ->schema([
                            Forms\Components\CheckboxList::make('time_slots')
                                ->label(__('owner.availability_resource.fields.time_slots'))
                                ->options([
                                    'morning' => __('owner.slots.morning'),
                                    'afternoon' => __('owner.slots.afternoon'),
                                    'evening' => __('owner.slots.evening'),
                                    'full_day' => __('owner.slots.full_day'),
                                ])
                                ->default(['morning', 'afternoon', 'evening', 'full_day'])
                                ->columns(2)
                                ->required(),
                        ]),

                    // Step 4: Configure Status
                    Forms\Components\Wizard\Step::make(__('owner.availability_resource.wizard.configure'))
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Forms\Components\Toggle::make('is_available')
                                ->label(__('owner.availability_resource.fields.is_available'))
                                ->default(true)
                                ->live(),

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
                                ->visible(fn (Forms\Get $get): bool => !$get('is_available')),

                            Forms\Components\TextInput::make('custom_price')
                                ->label(__('owner.availability_resource.fields.custom_price'))
                                ->numeric()
                                ->minValue(0)
                                ->step(0.001)
                                ->prefix('OMR')
                                ->placeholder(__('owner.availability_resource.placeholders.use_hall_price')),

                            Forms\Components\Textarea::make('notes')
                                ->label(__('owner.availability_resource.fields.notes'))
                                ->rows(2)
                                ->maxLength(500),
                        ]),
                ])
                ->columnSpanFull()
                // ->submitAction(new \Filament\Support\Components\ViewComponent('filament-panels::components.wizard.submit-action')),
            ]);
    }

    /**
     * Handle custom record creation for bulk dates.
     */
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $user = Auth::user();
        $hall = Hall::findOrFail($data['hall_id']);

        // Verify ownership
        if ($hall->owner_id !== $user->id) {
            Notification::make()
                ->danger()
                ->title(__('owner.errors.unauthorized'))
                ->send();

            $this->halt();
        }

        $dates = [];
        $dateMode = $data['date_mode'] ?? 'single';

        if ($dateMode === 'single') {
            $dates = [$data['date']];
        } else {
            // Generate date range
            $startDate = \Carbon\Carbon::parse($data['start_date']);
            $endDate = \Carbon\Carbon::parse($data['end_date']);

            while ($startDate <= $endDate) {
                $dates[] = $startDate->toDateString();
                $startDate->addDay();
            }
        }

        $timeSlots = $data['time_slots'] ?? ['morning', 'afternoon', 'evening', 'full_day'];
        $createdCount = 0;
        $lastRecord = null;

        foreach ($dates as $date) {
            foreach ($timeSlots as $slot) {
                $lastRecord = HallAvailability::updateOrCreate(
                    [
                        'hall_id' => $data['hall_id'],
                        'date' => $date,
                        'time_slot' => $slot,
                    ],
                    [
                        'is_available' => $data['is_available'] ?? true,
                        'reason' => ($data['is_available'] ?? true) ? null : ($data['reason'] ?? 'blocked'),
                        'custom_price' => $data['custom_price'] ?? null,
                        'notes' => $data['notes'] ?? null,
                    ]
                );
                $createdCount++;
            }
        }

        Notification::make()
            ->success()
            ->title(__('owner.availability_resource.notifications.bulk_created'))
            ->body(__('owner.availability_resource.notifications.bulk_created_body', ['count' => $createdCount]))
            ->send();

        return $lastRecord;
    }

    /**
     * Get the redirect URL after creation.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
