<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\GalleryResource\Pages;

use App\Filament\Owner\Resources\GalleryResource;
use App\Models\Hall;
use App\Models\HallImage;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * ListGallery Page for Owner Panel
 *
 * Lists all gallery images with filtering tabs.
 */
class ListGallery extends ListRecords
{
    /**
     * The resource this page belongs to.
     */
    protected static string $resource = GalleryResource::class;

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.gallery.title');
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return __('owner.gallery.heading');
    }

    /**
     * Get the page subheading.
     */
    public function getSubheading(): ?string
    {
        return __('owner.gallery.subheading');
    }

    /**
     * Get header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // Visual Gallery Manager
            Actions\Action::make('manage')
                ->label(__('owner.gallery.actions.visual_manager'))
                ->icon('heroicon-o-squares-2x2')
                ->color('info')
                ->url(fn () => GalleryResource::getUrl('manage')),

            // Bulk Upload
            Actions\Action::make('bulk_upload')
                ->label(__('owner.gallery.actions.bulk_upload'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->url(fn () => GalleryResource::getUrl('upload')),

            // Single Upload
            Actions\CreateAction::make()
                ->label(__('owner.gallery.actions.upload'))
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
        $ownerHallIds = Hall::where('owner_id', $user?->id)->pluck('id')->toArray();

        $baseQuery = fn () => HallImage::whereIn('hall_id', $ownerHallIds);

        return [
            'all' => Tab::make(__('owner.gallery.tabs.all'))
                ->badge($baseQuery()->count())
                ->badgeColor('primary'),

            'active' => Tab::make(__('owner.gallery.tabs.active'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
                ->badge($baseQuery()->where('is_active', true)->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check-circle'),

            'featured' => Tab::make(__('owner.gallery.tabs.featured'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_featured', true))
                ->badge($baseQuery()->where('is_featured', true)->count())
                ->badgeColor('warning')
                ->icon('heroicon-o-star'),

            'gallery' => Tab::make(__('owner.gallery.tabs.gallery'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'gallery'))
                ->badge($baseQuery()->where('type', 'gallery')->count())
                ->badgeColor('info'),

            'exterior' => Tab::make(__('owner.gallery.tabs.exterior'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'exterior'))
                ->badge($baseQuery()->where('type', 'exterior')->count())
                ->badgeColor('success'),

            'interior' => Tab::make(__('owner.gallery.tabs.interior'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'interior'))
                ->badge($baseQuery()->where('type', 'interior')->count())
                ->badgeColor('purple'),

            'inactive' => Tab::make(__('owner.gallery.tabs.inactive'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false))
                ->badge($baseQuery()->where('is_active', false)->count())
                ->badgeColor('gray')
                ->icon('heroicon-o-x-circle'),
        ];
    }
}
