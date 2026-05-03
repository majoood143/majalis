<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\GalleryResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
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
        return __('owner.gallery.title') ?? 'Gallery Management';
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return __('owner.gallery.heading') ?? 'Hall Gallery';
    }

    /**
     * Get the page subheading.
     */
    public function getSubheading(): ?string
    {
        return __('owner.gallery.subheading') ?? 'Manage images for your halls';
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
            Action::make('manage')
                ->label(__('owner.gallery.actions.visual_manager') ?? 'Visual Manager')
                ->icon('heroicon-o-squares-2x2')
                ->color('info')
                ->url(fn () => GalleryResource::getUrl('manage')),

            // Bulk Upload
            Action::make('bulk_upload')
                ->label(__('owner.gallery.actions.bulk_upload') ?? 'Bulk Upload')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->url(fn () => GalleryResource::getUrl('upload')),

            // Single Upload
            CreateAction::make()
                ->label(__('owner.gallery.actions.upload') ?? 'Upload Image')
                ->icon('heroicon-o-plus'),
        ];
    }

    /**
     * Get tabs for filtering.
     *
     * @return array<\Filament\Schemas\Components\Tabs\Tab>
     */
    public function getTabs(): array
    {
        $user = Auth::user();
        $ownerHallIds = Hall::where('owner_id', $user?->id)->pluck('id')->toArray();

        $baseQuery = fn () => HallImage::whereIn('hall_id', $ownerHallIds);

        return [
            'all' => \Filament\Schemas\Components\Tabs\Tab::make(__('owner.gallery.tabs.all') ?? 'All Images')
                ->badge($baseQuery()->count())
                ->badgeColor('primary'),

            'active' => \Filament\Schemas\Components\Tabs\Tab::make(__('owner.gallery.tabs.active') ?? 'Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
                ->badge($baseQuery()->where('is_active', true)->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check-circle'),

            'featured' => \Filament\Schemas\Components\Tabs\Tab::make(__('owner.gallery.tabs.featured') ?? 'Featured')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_featured', true))
                ->badge($baseQuery()->where('is_featured', true)->count())
                ->badgeColor('warning')
                ->icon('heroicon-o-star'),

            'gallery' => \Filament\Schemas\Components\Tabs\Tab::make(__('owner.gallery.tabs.gallery') ?? 'Gallery')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'gallery'))
                ->badge($baseQuery()->where('type', 'gallery')->count())
                ->badgeColor('info'),

            'inactive' => \Filament\Schemas\Components\Tabs\Tab::make(__('owner.gallery.tabs.inactive') ?? 'Inactive')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false))
                ->badge($baseQuery()->where('is_active', false)->count())
                ->badgeColor('gray')
                ->icon('heroicon-o-x-circle'),
        ];
    }
}
