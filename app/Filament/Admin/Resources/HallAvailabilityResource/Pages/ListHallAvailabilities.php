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
                ->color('primary')
                ->label(__('hall-availability.list_actions.create')),

            Actions\Action::make('bulkBlock')
                ->label(__('hall-availability.list_actions.bulk_block'))
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Select::make('hall_id')
                        ->label(__('hall-availability.hall'))
                        ->options(\App\Models\Hall::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label(__('hall-availability.bulk_block_modal.start_date'))
                        ->required()
                        ->native(false)
                        ->minDate(now()),

                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label(__('hall-availability.bulk_block_modal.end_date'))
                        ->required()
                        ->native(false)
                        ->minDate(now())
                        ->afterOrEqual('start_date'),

                    \Filament\Forms\Components\CheckboxList::make('time_slots')
                        ->label(__('hall-availability.bulk_block_modal.time_slots'))
                        ->options([
                            'morning' => __('hall-availability.bulk_block_modal.time_slot_options.morning'),
                            'afternoon' => __('hall-availability.bulk_block_modal.time_slot_options.afternoon'),
                            'evening' => __('hall-availability.bulk_block_modal.time_slot_options.evening'),
                            'full_day' => __('hall-availability.bulk_block_modal.time_slot_options.full_day'),
                        ])
                        ->required()
                        ->columns(2),

                    \Filament\Forms\Components\Select::make('reason')
                        ->label(__('hall-availability.bulk_block_modal.block_reason'))
                        ->options([
                            'maintenance' => __('hall-availability.reasons.maintenance'),
                            'blocked' => __('hall-availability.reasons.blocked'),
                            'holiday' => __('hall-availability.reasons.holiday'),
                            'custom' => __('hall-availability.reasons.custom'),
                        ])
                        ->required(),

                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label(__('hall-availability.notes'))
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->bulkBlockDates($data);
                }),

            Actions\Action::make('generateAvailability')
                ->label(__('hall-availability.list_actions.generate_availability'))
                ->icon('heroicon-o-calendar-days')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('hall_id')
                        ->label(__('hall-availability.hall'))
                        ->options(\App\Models\Hall::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label(__('hall-availability.generate_availability_modal.start_date'))
                        ->required()
                        ->native(false)
                        ->default(now()),

                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label(__('hall-availability.generate_availability_modal.end_date'))
                        ->required()
                        ->native(false)
                        ->default(now()->addMonths(3))
                        ->afterOrEqual('start_date'),

                    \Filament\Forms\Components\CheckboxList::make('time_slots')
                        ->label(__('hall-availability.generate_availability_modal.time_slots_to_generate'))
                        ->options([
                            'morning' => __('hall-availability.time_slots_short.morning'),
                            'afternoon' => __('hall-availability.time_slots_short.afternoon'),
                            'evening' => __('hall-availability.time_slots_short.evening'),
                            'full_day' => __('hall-availability.time_slots_short.full_day'),
                        ])
                        ->default(['morning', 'afternoon', 'evening', 'full_day'])
                        ->required()
                        ->columns(2),

                    \Filament\Forms\Components\Toggle::make('skip_existing')
                        ->label(__('hall-availability.generate_availability_modal.skip_existing'))
                        ->helperText(__('hall-availability.generate_availability_modal.skip_existing_helper'))
                        ->default(true),
                ])
                ->action(function (array $data) {
                    $this->generateAvailability($data);
                }),

            Actions\Action::make('exportCalendar')
                ->label(__('hall-availability.list_actions.export_calendar'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Select::make('hall_id')
                        ->label(__('hall-availability.export_calendar_modal.hall_optional'))
                        ->options(\App\Models\Hall::pluck('name', 'id'))
                        ->searchable()
                        ->preload(),

                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label(__('hall-availability.export_calendar_modal.start_date'))
                        ->required()
                        ->native(false)
                        ->default(now()),

                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label(__('hall-availability.export_calendar_modal.end_date'))
                        ->required()
                        ->native(false)
                        ->default(now()->addMonth())
                        ->afterOrEqual('start_date'),
                ])
                ->action(function (array $data) {
                    $this->exportCalendar($data);
                }),

            Actions\Action::make('cleanupPast')
                ->label(__('hall-availability.list_actions.cleanup_past'))
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('hall-availability.cleanup_modal.heading'))
                ->modalDescription(__('hall-availability.cleanup_modal.description'))
                ->action(function () {
                    $deleted = \App\Models\HallAvailability::where('date', '<', now()->toDateString())->delete();

                    Notification::make()
                        ->success()
                        ->title(__('hall-availability.notifications.cleanup_completed'))
                        ->body(__('hall-availability.notifications.deleted_records', ['count' => $deleted]))
                        ->send();

                    $this->redirect(static::getUrl());
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('hall-availability.tabs.all'))
                ->icon('heroicon-o-squares-2x2')
                ->badge(fn() => \App\Models\HallAvailability::count()),

            'available' => Tab::make(__('hall-availability.tabs.available'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_available', true))
                ->badge(fn() => \App\Models\HallAvailability::where('is_available', true)->count())
                ->badgeColor('success'),

            'blocked' => Tab::make(__('hall-availability.tabs.blocked'))
                ->icon('heroicon-o-lock-closed')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_available', false))
                ->badge(fn() => \App\Models\HallAvailability::where('is_available', false)->count())
                ->badgeColor('danger'),

            'today' => Tab::make(__('hall-availability.tabs.today'))
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('date', now()))
                ->badge(fn() => \App\Models\HallAvailability::whereDate('date', now())->count())
                ->badgeColor('info'),

            'this_week' => Tab::make(__('hall-availability.tabs.this_week'))
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

            'this_month' => Tab::make(__('hall-availability.tabs.this_month'))
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year))
                ->badge(fn() => \App\Models\HallAvailability::whereMonth('date', now()->month)
                    ->whereYear('date', now()->year)
                    ->count())
                ->badgeColor('primary'),

            'custom_pricing' => Tab::make(__('hall-availability.tabs.custom_pricing'))
                ->icon('heroicon-o-currency-dollar')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('custom_price'))
                ->badge(fn() => \App\Models\HallAvailability::whereNotNull('custom_price')->count())
                ->badgeColor('warning'),

            'maintenance' => Tab::make(__('hall-availability.tabs.maintenance'))
                ->icon('heroicon-o-wrench-screwdriver')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('reason', 'maintenance'))
                ->badge(fn() => \App\Models\HallAvailability::where('reason', 'maintenance')->count())
                ->badgeColor('orange'),

            'past' => Tab::make(__('hall-availability.tabs.past'))
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
            ->title(__('hall-availability.notifications.bulk_block_completed'))
            ->body(__('hall-availability.notifications.created_updated', [
                'created' => $createdCount,
                'updated' => $updatedCount
            ]))
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
        //Cache::tags(['availability', 'hall_' . $data['hall_id']])->flush();

        Notification::make()
            ->success()
            ->title(__('hall-availability.notifications.availability_generated'))
            ->body(__('hall-availability.notifications.created_skipped', [
                'created' => $createdCount,
                'skipped' => $skippedCount
            ]))
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
            __('hall-availability.export_headers.hall'),
            __('hall-availability.export_headers.date'),
            __('hall-availability.export_headers.day'),
            __('hall-availability.export_headers.time_slot'),
            __('hall-availability.export_headers.available'),
            __('hall-availability.export_headers.reason'),
            __('hall-availability.export_headers.custom_price'),
            __('hall-availability.export_headers.effective_price'),
            __('hall-availability.export_headers.notes'),
        ]);

        foreach ($availabilities as $availability) {
            fputcsv($file, [
                $availability->hall->name ?? __('hall-availability.not_applicable'),
                $availability->date->format('Y-m-d'),
                $availability->date->format('l'),
                ucfirst(str_replace('_', ' ', $availability->time_slot)),
                $availability->is_available ? __('hall-availability.export_values.yes') : __('hall-availability.export_values.no'),
                $availability->reason ? ucfirst(str_replace('_', ' ', $availability->reason)) : __('hall-availability.not_applicable'),
                $availability->custom_price ? number_format($availability->custom_price, 3) : __('hall-availability.export_values.default'),
                number_format($availability->getEffectivePrice(), 3),
                $availability->notes ?? '',
            ]);
        }

        fclose($file);

        Notification::make()
            ->success()
            ->title(__('hall-availability.notifications.calendar_exported'))
            ->body(__('hall-availability.notifications.calendar_exported'))
            ->persistent()
            ->actions([
                \Filament\Notifications\Actions\Action::make('download')
                    ->label(__('hall-availability.notifications.download'))
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
