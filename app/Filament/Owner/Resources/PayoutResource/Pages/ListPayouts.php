<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PayoutResource\Pages;

use App\Enums\PayoutStatus;
use App\Filament\Owner\Resources\PayoutResource;
use App\Models\OwnerPayout;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * ListPayouts Page for Owner Panel
 *
 * Displays payout list with summary statistics and tabs.
 *
 * @package App\Filament\Owner\Resources\PayoutResource\Pages
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
     * Get the page title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return __('owner.payouts.title');
    }

    /**
     * Get the page heading.
     *
     * @return string
     */
    public function getHeading(): string
    {
        return __('owner.payouts.heading');
    }

    /**
     * Get the page subheading with summary.
     *
     * @return string|null
     */
    public function getSubheading(): ?string
    {
        $user = Auth::user();

        // Total completed payouts
        $totalCompleted = (float) OwnerPayout::where('owner_id', $user->id)
            ->where('status', PayoutStatus::COMPLETED)
            ->sum('net_payout');

        // Total pending payouts
        $totalPending = (float) OwnerPayout::where('owner_id', $user->id)
            ->whereIn('status', [PayoutStatus::PENDING, PayoutStatus::PROCESSING])
            ->sum('net_payout');

        return __('owner.payouts.subheading', [
            'completed' => number_format($totalCompleted, 3),
            'pending' => number_format($totalPending, 3),
        ]);
    }

    /**
     * Get header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // Contact support for payout issues
            Actions\Action::make('contactSupport')
                ->label(__('owner.payouts.contact_support'))
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('gray')
                // ->url(route('filament.owner.resources.tickets.create', [
                //     'subject' => 'Payout Inquiry',
                // ]))
                ->openUrlInNewTab(),
        ];
    }

    /**
     * Get tabs for status filtering.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $user = Auth::user();

        return [
            'all' => Tab::make(__('owner.payouts.tab_all'))
                ->icon('heroicon-m-credit-card')
                ->badge(fn (): int => $this->getTabCount('all')),

            'pending' => Tab::make(__('owner.payouts.tab_pending'))
                ->icon('heroicon-m-clock')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->whereIn('status', [PayoutStatus::PENDING, PayoutStatus::PROCESSING]))
                ->badge(fn (): int => $this->getTabCount('pending'))
                ->badgeColor('warning'),

            'completed' => Tab::make(__('owner.payouts.tab_completed'))
                ->icon('heroicon-m-check-circle')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('status', PayoutStatus::COMPLETED))
                ->badge(fn (): int => $this->getTabCount('completed'))
                ->badgeColor('success'),

            'this_year' => Tab::make(__('owner.payouts.tab_this_year'))
                ->icon('heroicon-m-calendar')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->whereYear('created_at', now()->year))
                ->badge(fn (): int => $this->getTabCount('this_year')),
        ];
    }

    /**
     * Get header widgets.
     *
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            PayoutResource\Widgets\PayoutSummaryWidget::class,
        ];
    }

    /**
     * Get tab count for badges.
     *
     * @param string $tab Tab identifier
     * @return int Count
     */
    protected function getTabCount(string $tab): int
    {
        $user = Auth::user();

        $query = OwnerPayout::where('owner_id', $user->id);

        return match ($tab) {
            'pending' => (int) $query
                ->whereIn('status', [PayoutStatus::PENDING, PayoutStatus::PROCESSING])
                ->count(),
            'completed' => (int) $query
                ->where('status', PayoutStatus::COMPLETED)
                ->count(),
            'this_year' => (int) $query
                ->whereYear('created_at', now()->year)
                ->count(),
            default => (int) $query->count(),
        };
    }
}
