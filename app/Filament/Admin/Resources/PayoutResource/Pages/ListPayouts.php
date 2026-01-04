<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PayoutResource\Pages;

use App\Enums\PayoutStatus;
use App\Filament\Admin\Resources\PayoutResource;
use App\Models\Booking;
use App\Models\OwnerPayout;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;

/**
 * ListPayouts Page
 *
 * Lists all owner payouts with filtering, tabs, and bulk actions.
 * Includes summary statistics and quick generation actions.
 *
 * @package App\Filament\Admin\Resources\PayoutResource\Pages
 */
class ListPayouts extends ListRecords
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = PayoutResource::class;

    /**
     * Get the header actions for this page.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            // Generate Payouts Action
            Actions\Action::make('generate_payouts')
                ->label(__('admin.payout.actions.generate'))
                ->icon('heroicon-o-calculator')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('owner_id')
                        ->label(__('admin.payout.fields.owner'))
                        ->options(function () {
                            return User::whereHas('hallOwner')
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->placeholder(__('admin.payout.all_owners'))
                        ->helperText(__('admin.payout.generate_owner_help')),

                    Forms\Components\DatePicker::make('period_start')
                        ->label(__('admin.payout.fields.period_start'))
                        ->required()
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->maxDate(now())
                        ->default(now()->startOfMonth()),

                    Forms\Components\DatePicker::make('period_end')
                        ->label(__('admin.payout.fields.period_end'))
                        ->required()
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->maxDate(now())
                        ->default(now()->endOfMonth()),
                ])
                ->modalHeading(__('admin.payout.modal.generate_title'))
                ->modalDescription(__('admin.payout.modal.generate_desc'))
                ->modalSubmitActionLabel(__('admin.payout.modal.generate_confirm'))
                ->action(function (array $data): void {
                    $generated = $this->generatePayouts(
                        $data['owner_id'] ?? null,
                        $data['period_start'],
                        $data['period_end']
                    );

                    if ($generated > 0) {
                        Notification::make()
                            ->title(__('admin.payout.notifications.generated', [
                                'count' => $generated,
                            ]))
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title(__('admin.payout.notifications.no_payouts'))
                            ->body(__('admin.payout.notifications.no_payouts_body'))
                            ->warning()
                            ->send();
                    }
                }),

            // Export Action
            Actions\Action::make('export')
                ->label(__('admin.payout.actions.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->form([
                    Forms\Components\Select::make('status')
                        ->label(__('admin.payout.filters.status'))
                        ->options(PayoutStatus::toSelectArray())
                        ->placeholder(__('admin.payout.all_statuses')),

                    Forms\Components\DatePicker::make('from_date')
                        ->label(__('admin.payout.filters.from')),

                    Forms\Components\DatePicker::make('to_date')
                        ->label(__('admin.payout.filters.to')),

                    Forms\Components\Select::make('format')
                        ->label(__('admin.payout.export.format'))
                        ->options([
                            'csv' => 'CSV',
                            'xlsx' => 'Excel (XLSX)',
                        ])
                        ->default('csv')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    // Export logic would go here
                    Notification::make()
                        ->title(__('admin.payout.notifications.export_started'))
                        ->success()
                        ->send();
                }),

            // Create New Payout
            Actions\CreateAction::make()
                ->label(__('admin.payout.actions.create'))
                ->icon('heroicon-o-plus'),
        ];
    }

    /**
     * Generate payouts for owners.
     *
     * @param int|null $ownerId Specific owner or null for all
     * @param string $periodStart Period start date
     * @param string $periodEnd Period end date
     * @return int Number of payouts generated
     */
    protected function generatePayouts(
        ?int $ownerId,
        string $periodStart,
        string $periodEnd
    ): int {
        $generated = 0;

        // Get owners to process
        $ownerQuery = User::whereHas('hallOwner');
        
        if ($ownerId) {
            $ownerQuery->where('id', $ownerId);
        }

        $owners = $ownerQuery->get();

        foreach ($owners as $owner) {
            // Check if payout already exists for this period
            $existingPayout = OwnerPayout::where('owner_id', $owner->id)
                ->where('period_start', $periodStart)
                ->where('period_end', $periodEnd)
                ->exists();

            if ($existingPayout) {
                continue;
            }

            // Check if owner has eligible bookings
            $bookingsQuery = Booking::whereHas('hall', function ($q) use ($owner): void {
                $q->where('owner_id', $owner->id);
            })
                ->whereBetween('booking_date', [$periodStart, $periodEnd])
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid');

            if ($bookingsQuery->count() === 0) {
                continue;
            }

            // Create payout
            try {
                OwnerPayout::createForPeriod($owner->id, $periodStart, $periodEnd);
                $generated++;
            } catch (\Exception $e) {
                \Log::error('Failed to generate payout', [
                    'owner_id' => $owner->id,
                    'period' => [$periodStart, $periodEnd],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $generated;
    }

    /**
     * Get the tabs for filtering payouts.
     *
     * @return array
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('admin.payout.tabs.all'))
                ->badge(OwnerPayout::count())
                ->badgeColor('gray'),

            'pending' => Tab::make(__('admin.payout.tabs.pending'))
                ->badge(OwnerPayout::where('status', PayoutStatus::PENDING)->count())
                ->badgeColor('warning')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('status', PayoutStatus::PENDING)
                ),

            'processing' => Tab::make(__('admin.payout.tabs.processing'))
                ->badge(OwnerPayout::where('status', PayoutStatus::PROCESSING)->count())
                ->badgeColor('info')
                ->icon('heroicon-o-arrow-path')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('status', PayoutStatus::PROCESSING)
                ),

            'completed' => Tab::make(__('admin.payout.tabs.completed'))
                ->badge(OwnerPayout::where('status', PayoutStatus::COMPLETED)->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('status', PayoutStatus::COMPLETED)
                ),

            'on_hold' => Tab::make(__('admin.payout.tabs.on_hold'))
                ->badge(OwnerPayout::where('status', PayoutStatus::ON_HOLD)->count())
                ->badgeColor('warning')
                ->icon('heroicon-o-pause-circle')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('status', PayoutStatus::ON_HOLD)
                ),

            'failed' => Tab::make(__('admin.payout.tabs.failed'))
                ->badge(OwnerPayout::where('status', PayoutStatus::FAILED)->count())
                ->badgeColor('danger')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('status', PayoutStatus::FAILED)
                ),
        ];
    }

    /**
     * Get the header widgets for this page.
     *
     * @return array
     */
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\PayoutStatsWidget::class,
        ];
    }
}
