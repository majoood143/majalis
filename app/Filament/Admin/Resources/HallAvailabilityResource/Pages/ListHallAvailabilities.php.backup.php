<?php

namespace App\Filament\Admin\Resources\HallAvailabilityResource\Pages;

use App\Filament\Admin\Resources\HallAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ListHallAvailabilities extends ListRecords
{
    protected static string $resource = HallAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Actions\Action::make('bulkBlock')
                ->label('Bulk Block Dates')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Select::make('hall_id')
                        ->label('Hall')
                        ->options(\App\Models\Hall::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required()
                        ->native(false)
                        ->minDate(now()),

                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->native(false)
                        ->minDate(now())
                        ->afterOrEqual('start_date'),

                    \Filament\Forms\Components\CheckboxList::make('time_slots')
                        ->label('Time Slots')
                        ->options([
                            'morning' => 'Morning (8 AM - 12 PM)',
                            'afternoon' => 'Afternoon (12 PM - 5 PM)',
                            'evening' => 'Evening (5 PM - 11 PM)',
                            'full_day' => 'Full Day (8 AM - 11 PM)',
                        ])
                        ->required()
                        ->columns(2),

                    \Filament\Forms\Components\Select::make('reason')
                        ->label('Block Reason')
                        ->options([
                            'maintenance' => 'Under Maintenance',
                            'blocked' => 'Blocked by Owner',
                            'holiday' => 'Holiday',
                            'custom' => 'Custom Block',
                        ])
                        ->required(),

                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->bulkBlockDates($data);
                }),

            Actions\Action::make('generateAvailability')
                ->label('Generate Availability')
                ->icon('heroicon-o-calendar-days')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('hall_id')
                        ->label('Hall')
                        ->options(\App\Models\Hall::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required()
                        ->native(false)
                        ->default(now()),

                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->native(false)
                        ->default(now()->addMonths(3))
                        ->afterOrEqual('start_date'),

                    \Filament\Forms\Components\CheckboxList::make('time_slots')
                        ->label('Time Slots to Generate')
                        ->options([
                            'morning' => 'Morning',
                            'afternoon' => 'Afternoon',
                            'evening' => 'Evening',
                            'full_day' => 'Full Day',
                        ])
                        ->default(['morning', 'afternoon', 'evening', 'full_day'])
                        ->required()
                        ->columns(2),

                    \Filament\Forms\Components\Toggle::make('skip_existing')
                        ->label('Skip Existing Records')
                        ->helperText('Only create availability for dates that don\'t exist yet')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    $this->generateAvailability($data);
                }),

            Actions\Action::make('exportCalendar')
                ->label('Export Calendar')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Select::make('hall_id')
                        ->label('Hall (Optional)')
                        ->options(\App\Models\Hall::pluck('name', 'id'))
                        ->searchable()
                        ->preload(),

                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required()
                        ->native(false)
                        ->default(now()),

                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->native(false)
                        ->default(now()->addMonth())
                        ->afterOrEqual('start_date'),
                ])
                ->action(function (array $data) {
                    $this->exportCalendar($data);
                }),

            Actions\Action::make('cleanupPast')
                ->label('Cleanup Past Dates')
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Delete Past Availability Records')
                ->modalDescription('This will permanently delete all availability records before today.')
                ->action(function () {
                    $deleted = \App\Models\HallAvailability::where('date', '<', now()->toDateString())->delete();

                    Notification::make()
                        ->success()
                        ->title('Cleanup Completed')
                        ->body("{$deleted} past availability record(s) deleted.")
                        ->send();

                    $this->redirect(static::getUrl());
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->icon('heroicon-o-squares-2x2')
                ->badge(fn() => \App\Models\HallAvailability::count()),

            'available' => Tab::make('Available')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_available', true))
                ->badge(fn() => \App\Models\HallAvailability::where('is_available', true)->count())
                ->badgeColor('success'),

            'blocked' => Tab::make('Blocked')
                ->icon('heroicon-o-lock-closed')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_available', false))
                ->badge(fn() => \App\Models\HallAvailability::where('is_available', false)->count())
                ->badgeColor('danger'),

            'today' => Tab::make('Today')
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('date', now()))
                ->badge(fn() => \App\Models\HallAvailability::whereDate('date', now())->count())
                ->badgeColor('info'),

            'this_week' => Tab::make('This Week')
                ->icon('heroicon-o-calendar-days')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween('date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]))
                ->badge(fn() => \App\Models\HallAvailability::whereBetween('date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count())
                ->badgeColor('info'),

            'this_month' => Tab::make('This Month')
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year))
                ->badge(fn() => \App\Models\HallAvailability::whereMonth('date', now()->month)
                    ->whereYear('date', now()->year)
                    ->count())
                ->badgeColor('primary'),

            'custom_pricing' => Tab::make('Custom Pricing')
                ->icon('heroicon-o-currency-dollar')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('custom_price'))
                ->badge(fn() => \App\Models\HallAvailability::whereNotNull('custom_price')->count())
                ->badgeColor('warning'),

            'maintenance' => Tab::make('Maintenance')
                ->icon('heroicon-o-wrench-screwdriver')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('reason', 'maintenance'))
                ->badge(fn() => \App\Models\HallAvailability::where('reason', 'maintenance')->count())
                ->badgeColor('orange'),

            'past' => Tab::make('Past')
                ->icon('heroicon-o-archive-box')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('date', '<', now()))
                ->badge(fn() => \App\Models\HallAvailability::where('date', '<', now())->count())
                ->badgeColor('gray'),
        ];
    }

    protected function bulkBlockDates(array $data): void
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $createdCount = 0;
        $updatedCount = 0;

        while ($startDate->lte($endDate)) {
            foreach ($data['time_slots'] as $timeSlot) {
                $availability = \App\Models\HallAvailability::updateOrCreate(
                    [
                        'hall_id' => $data['hall_id'],
                        'date' => $startDate->toDateString(),
                        'time_slot' => $timeSlot,
                    ],
                    [
                        'is_available' => false,
                        'reason' => $data['reason'],
                        'notes' => $data['notes'] ?? null,
                    ]
                );

                if ($availability->wasRecentlyCreated) {
                    $createdCount++;
                } else {
                    $updatedCount++;
                }
            }

            $startDate->addDay();
        }

        // Clear cache
        Cache::tags(['availability', 'hall_' . $data['hall_id']])->flush();

        Notification::make()
            ->success()
            ->title('Bulk Block Completed')
            ->body("Created: {$createdCount}, Updated: {$updatedCount} availability records.")
            ->send();

        $this->redirect(static::getUrl());
    }

    protected function generateAvailability(array $data): void
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $createdCount = 0;
        $skippedCount = 0;

        while ($startDate->lte($endDate)) {
            foreach ($data['time_slots'] as $timeSlot) {
                $exists = \App\Models\HallAvailability::where('hall_id', $data['hall_id'])
                    ->where('date', $startDate->toDateString())
                    ->where('time_slot', $timeSlot)
                    ->exists();

                if ($data['skip_existing'] && $exists) {
                    $skippedCount++;
                    continue;
                }

                \App\Models\HallAvailability::updateOrCreate(
                    [
                        'hall_id' => $data['hall_id'],
                        'date' => $startDate->toDateString(),
                        'time_slot' => $timeSlot,
                    ],
                    [
                        'is_available' => true,
                    ]
                );

                $createdCount++;
            }

            $startDate->addDay();
        }

        // Clear cache
        Cache::tags(['availability', 'hall_' . $data['hall_id']])->flush();

        Notification::make()
            ->success()
            ->title('Availability Generated')
            ->body("Created/Updated: {$createdCount}, Skipped: {$skippedCount} records.")
            ->send();

        $this->redirect(static::getUrl());
    }

    protected function exportCalendar(array $data): void
    {
        $query = \App\Models\HallAvailability::with('hall')
            ->whereBetween('date', [$data['start_date'], $data['end_date']]);

        if (isset($data['hall_id'])) {
            $query->where('hall_id', $data['hall_id']);
        }

        $availabilities = $query->orderBy('date')->orderBy('time_slot')->get();

        $filename = 'hall_availability_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        fputcsv($file, [
            'Hall',
            'Date',
            'Day',
            'Time Slot',
            'Available',
            'Reason',
            'Custom Price',
            'Effective Price',
            'Notes',
        ]);

        foreach ($availabilities as $availability) {
            fputcsv($file, [
                $availability->hall->name ?? 'N/A',
                $availability->date->format('Y-m-d'),
                $availability->date->format('l'),
                ucfirst(str_replace('_', ' ', $availability->time_slot)),
                $availability->is_available ? 'Yes' : 'No',
                $availability->reason ? ucfirst(str_replace('_', ' ', $availability->reason)) : 'N/A',
                $availability->custom_price ? number_format($availability->custom_price, 3) : 'Default',
                number_format($availability->getEffectivePrice(), 3),
                $availability->notes ?? '',
            ]);
        }

        fclose($file);

        Notification::make()
            ->success()
            ->title('Calendar Exported')
            ->body('Availability calendar exported successfully.')
            ->persistent()
            ->actions([
                \Filament\Notifications\Actions\Action::make('download')
                    ->label('Download File')
                    ->url(asset('storage/exports/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add calendar widget here if needed
        ];
    }
}
