<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PricingResource\Pages;

use App\Filament\Owner\Resources\PricingResource;
use App\Models\Hall;
use App\Models\SeasonalPricing;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * ListPricing Page for Owner Panel
 *
 * Lists all pricing rules with filtering tabs.
 */
class ListPricing extends ListRecords
{
    /**
     * The resource this page belongs to.
     */
    protected static string $resource = PricingResource::class;

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.pricing.title');
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return __('owner.pricing.heading');
    }

    /**
     * Get the page subheading.
     */
    public function getSubheading(): ?string
    {
        return __('owner.pricing.subheading');
    }

    /**
     * Get header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // Price Calculator
            Actions\Action::make('calculator')
                ->label(__('owner.pricing.actions.calculator'))
                ->icon('heroicon-o-calculator')
                ->color('info')
                ->url(fn () => PricingResource::getUrl('calculator')),

            // Quick Weekend Pricing
            Actions\Action::make('quick_weekend')
                ->label(__('owner.pricing.actions.quick_weekend'))
                ->icon('heroicon-o-calendar')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('hall_id')
                        ->label(__('owner.pricing.fields.hall'))
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

                    \Filament\Forms\Components\TextInput::make('percentage')
                        ->label(__('owner.pricing.fields.weekend_increase'))
                        ->numeric()
                        ->default(20)
                        ->suffix('%')
                        ->required(),
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

                    SeasonalPricing::create([
                        'hall_id' => $data['hall_id'],
                        'name' => [
                            'en' => 'Weekend Pricing',
                            'ar' => 'أسعار نهاية الأسبوع',
                        ],
                        'type' => 'weekend',
                        'start_date' => now(),
                        'end_date' => now()->addYear(),
                        'is_recurring' => true,
                        'recurrence_type' => 'weekly',
                        'days_of_week' => [5, 6], // Fri-Sat (Omani weekend)
                        'adjustment_type' => 'percentage',
                        'adjustment_value' => $data['percentage'],
                        'is_active' => true,
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title(__('owner.pricing.notifications.weekend_created'))
                        ->send();
                }),

            // Create Action
            Actions\CreateAction::make()
                ->label(__('owner.pricing.actions.create'))
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
        $baseQuery = fn () => SeasonalPricing::whereHas('hall', function (Builder $q) use ($user) {
            $q->where('owner_id', $user->id);
        });

        return [
            'all' => Tab::make(__('owner.pricing.tabs.all'))
                ->badge($baseQuery()->count())
                ->badgeColor('primary'),

            'active' => Tab::make(__('owner.pricing.tabs.active'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true)->where('end_date', '>=', now()))
                ->badge($baseQuery()->where('is_active', true)->where('end_date', '>=', now())->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check-circle'),

            'seasonal' => Tab::make(__('owner.pricing.tabs.seasonal'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'seasonal'))
                ->badge($baseQuery()->where('type', 'seasonal')->count())
                ->badgeColor('info'),

            'weekend' => Tab::make(__('owner.pricing.tabs.weekend'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'weekend'))
                ->badge($baseQuery()->where('type', 'weekend')->count())
                ->badgeColor('warning'),

            'holiday' => Tab::make(__('owner.pricing.tabs.holiday'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'holiday'))
                ->badge($baseQuery()->where('type', 'holiday')->count())
                ->badgeColor('danger'),

            'expired' => Tab::make(__('owner.pricing.tabs.expired'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('end_date', '<', now())->where('is_recurring', false))
                ->badge($baseQuery()->where('end_date', '<', now())->where('is_recurring', false)->count())
                ->badgeColor('gray'),
        ];
    }
}
