<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\AvailabilityResource\Pages;

use App\Filament\Owner\Resources\AvailabilityResource;
use App\Models\Hall;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * ListAvailabilities Page for Owner Panel
 *
 * Displays availability slots with tabs for quick filtering.
 */
class ListAvailabilities extends ListRecords
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
        return __('owner.availability_resource.title');
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return __('owner.availability_resource.heading');
    }

    /**
     * Get the page subheading.
     */
    public function getSubheading(): ?string
    {
        return __('owner.availability_resource.subheading');
    }

    /**
     * Get header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // Calendar View Button
            Actions\Action::make('calendar_view')
                ->label(__('owner.availability_resource.actions.calendar_view'))
                ->icon('heroicon-o-calendar')
                ->color('info')
                ->url(fn () => AvailabilityResource::getUrl('calendar')),

            // Bulk Generate Action
            Actions\Action::make('bulk_generate')
                ->label(__('owner.availability_resource.actions.bulk_generate'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('hall_id')
                        ->label(__('owner.availability_resource.fields.hall'))
                        ->options(function () {
                            $user = Auth::user();
                            return Hall::where('owner_id', $user->id)
                                ->get()
                                ->mapWithKeys(fn ($hall) => [
                                    $hall->id => $hall->getTranslation('name', app()->getLocale())
                                ]);
                        })
                        ->required()
                        ->native(false)
                        ->searchable(),

                    \Filament\Forms\Components\TextInput::make('days')
                        ->label(__('owner.availability_resource.fields.days_to_generate'))
                        ->numeric()
                        ->default(90)
                        ->minValue(1)
                        ->maxValue(365)
                        ->required()
                        ->suffix(__('owner.availability_resource.suffixes.days')),
                ])
                ->action(function (array $data): void {
                    $hall = Hall::findOrFail($data['hall_id']);

                    // Verify ownership
                    if ($hall->owner_id !== Auth::id()) {
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title(__('owner.errors.unauthorized'))
                            ->send();
                        return;
                    }

                    $hall->generateAvailability((int) $data['days']);

                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title(__('owner.availability_resource.notifications.generated'))
                        ->body(__('owner.availability_resource.notifications.generated_body', [
                            'days' => $data['days'],
                            'hall' => $hall->getTranslation('name', app()->getLocale()),
                        ]))
                        ->send();
                }),

            // Create Action
            Actions\CreateAction::make()
                ->label(__('owner.availability_resource.actions.create'))
                ->icon('heroicon-o-plus'),
        ];
    }

    /**
     * Get tabs for filtering.
     *
     * @return array<Tab>
     */
    public function getTabs(): array
    {
        $user = Auth::user();
        $baseQuery = fn () => \App\Models\HallAvailability::whereHas('hall', function (Builder $q) use ($user) {
            $q->where('owner_id', $user->id);
        })->where('date', '>=', now()->toDateString());

        return [
            'all' => Tab::make(__('owner.availability_resource.tabs.all'))
                ->badge($baseQuery()->count())
                ->badgeColor('primary'),

            'available' => Tab::make(__('owner.availability_resource.tabs.available'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_available', true))
                ->badge($baseQuery()->where('is_available', true)->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check-circle'),

            'blocked' => Tab::make(__('owner.availability_resource.tabs.blocked'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_available', false)->where('reason', '!=', 'booked'))
                ->badge($baseQuery()->where('is_available', false)->where('reason', '!=', 'booked')->count())
                ->badgeColor('danger')
                ->icon('heroicon-o-x-circle'),

            'booked' => Tab::make(__('owner.availability_resource.tabs.booked'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('reason', 'booked'))
                ->badge($baseQuery()->where('reason', 'booked')->count())
                ->badgeColor('info')
                ->icon('heroicon-o-calendar-days'),

            'maintenance' => Tab::make(__('owner.availability_resource.tabs.maintenance'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('reason', 'maintenance'))
                ->badge($baseQuery()->where('reason', 'maintenance')->count())
                ->badgeColor('warning')
                ->icon('heroicon-o-wrench-screwdriver'),

            'today' => Tab::make(__('owner.availability_resource.tabs.today'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('date', now()))
                ->badge($baseQuery()->whereDate('date', now())->count())
                ->badgeColor('purple'),

            'this_week' => Tab::make(__('owner.availability_resource.tabs.this_week'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('date', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString(),
                ]))
                ->badge($baseQuery()->whereBetween('date', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString(),
                ])->count())
                ->badgeColor('gray'),
        ];
    }
}
