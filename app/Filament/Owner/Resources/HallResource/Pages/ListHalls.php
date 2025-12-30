<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\Pages;

use App\Filament\Owner\Resources\HallResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * ListHalls Page for Owner Panel
 *
 * Displays the owner's halls with filtering tabs and statistics.
 */
class ListHalls extends ListRecords
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = HallResource::class;

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.halls.title');
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return __('owner.halls.heading');
    }

    /**
     * Get the page subheading.
     */
    public function getSubheading(): ?string
    {
        $user = Auth::user();
        $count = \App\Models\Hall::where('owner_id', $user->id)->count();
        
        return __('owner.halls.subheading', ['count' => $count]);
    }

    /**
     * Get the header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('owner.halls.actions.create'))
                ->icon('heroicon-o-plus'),
        ];
    }

    /**
     * Get the tabs for filtering halls.
     *
     * @return array<Tab>
     */
    public function getTabs(): array
    {
        $user = Auth::user();
        $baseQuery = fn () => \App\Models\Hall::where('owner_id', $user->id);

        return [
            'all' => Tab::make(__('owner.halls.tabs.all'))
                ->badge($baseQuery()->count())
                ->badgeColor('primary'),

            'active' => Tab::make(__('owner.halls.tabs.active'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
                ->badge($baseQuery()->where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make(__('owner.halls.tabs.inactive'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false))
                ->badge($baseQuery()->where('is_active', false)->count())
                ->badgeColor('danger'),

            'featured' => Tab::make(__('owner.halls.tabs.featured'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_featured', true))
                ->badge($baseQuery()->where('is_featured', true)->count())
                ->badgeColor('warning')
                ->icon('heroicon-o-star'),
        ];
    }

    /**
     * Get the header widgets.
     *
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            // Add hall statistics widget here if needed
        ];
    }
}
