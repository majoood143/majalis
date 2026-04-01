<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\GalleryResource\Pages;

use App\Filament\Owner\Resources\GalleryResource;
use App\Models\Hall;
use App\Models\HallImage;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * ManageGallery Page for Owner Panel
 *
 * Visual gallery management with simple grid view.
 */
class ManageGallery extends Page
{
    /**
     * The resource this page belongs to.
     */
    protected static string $resource = GalleryResource::class;

    /**
     * The view for this page.
     */
    protected static string $view = 'filament.owner.resources.gallery-resource.pages.manage-gallery';

    /**
     * Selected hall ID.
     */
    public ?int $selectedHallId = null;

    /**
     * Mount the page.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $halls = Hall::where('owner_id', $user?->id)->get();

        // Pre-select first hall if available
        if ($halls->count() > 0) {
            $this->selectedHallId = $halls->first()->id;
        }

        // Check for hall_id in URL
        if (request()->has('hall_id')) {
            $hallId = (int) request()->get('hall_id');
            $hall = Hall::find($hallId);
            if ($hall && $hall->owner_id === Auth::id()) {
                $this->selectedHallId = $hallId;
            }
        }
    }

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.gallery.manage.title');
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return __('owner.gallery.manage.heading');
    }

    /**
     * Get the subheading.
     */
    public function getSubheading(): ?string
    {
        return __('owner.gallery.manage.subheading');
    }

    /**
     * Get header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label(__('owner.gallery.actions.back_to_gallery'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => GalleryResource::getUrl('index')),

            Actions\Action::make('bulk_upload')
                ->label(__('owner.gallery.actions.bulk_upload'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->url(fn () => GalleryResource::getUrl('upload') .
                    ($this->selectedHallId ? '?hall_id=' . $this->selectedHallId : '')),
        ];
    }

    /**
     * Get owner's halls.
     */
    public function getOwnerHalls(): Collection
    {
        $user = Auth::user();

        return Hall::where('owner_id', $user?->id)
            ->withCount('images')
            ->orderBy('name->en')
            ->get();
    }

    /**
     * Get the selected hall.
     */
    public function getSelectedHall(): ?Hall
    {
        if (!$this->selectedHallId) {
            return null;
        }

        return Hall::find($this->selectedHallId);
    }

    /**
     * Get images for the selected hall.
     */
    public function getHallImages(): Collection
    {
        if (!$this->selectedHallId) {
            return collect();
        }

        return HallImage::where('hall_id', $this->selectedHallId)
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get gallery statistics.
     */
    public function getGalleryStats(): array
    {
        if (!$this->selectedHallId) {
            return [
                'total' => 0,
                'active' => 0,
                'featured' => 0,
            ];
        }

        return [
            'total' => HallImage::where('hall_id', $this->selectedHallId)->count(),
            'active' => HallImage::where('hall_id', $this->selectedHallId)->where('is_active', true)->count(),
            'featured' => HallImage::where('hall_id', $this->selectedHallId)->where('is_featured', true)->count(),
        ];
    }

    /**
     * Set selected hall.
     */
    public function setHall(int $hallId): void
    {
        $this->selectedHallId = $hallId;
    }

    /**
     * Toggle image featured status.
     */
    public function toggleFeatured(int $imageId): void
    {
        $image = HallImage::find($imageId);

        if (!$image || $image->hall->owner_id !== Auth::id()) {
            return;
        }

        $image->update(['is_featured' => !$image->is_featured]);

        Notification::make()
            ->success()
            ->title($image->is_featured
                ? __('owner.gallery.notifications.marked_featured')
                : __('owner.gallery.notifications.unmarked_featured'))
            ->duration(2000)
            ->send();
    }

    /**
     * Toggle image active status.
     */
    public function toggleActive(int $imageId): void
    {
        $image = HallImage::find($imageId);

        if (!$image || $image->hall->owner_id !== Auth::id()) {
            return;
        }

        $image->update(['is_active' => !$image->is_active]);

        Notification::make()
            ->success()
            ->title($image->is_active
                ? __('owner.gallery.notifications.activated')
                : __('owner.gallery.notifications.deactivated'))
            ->duration(2000)
            ->send();
    }

    /**
     * Delete an image.
     */
    public function deleteImage(int $imageId): void
    {
        $image = HallImage::find($imageId);

        if (!$image || $image->hall->owner_id !== Auth::id()) {
            return;
        }

        // Delete files
        if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        if ($image->thumbnail_path && Storage::disk('public')->exists($image->thumbnail_path)) {
            Storage::disk('public')->delete($image->thumbnail_path);
        }

        $image->delete();

        Notification::make()
            ->success()
            ->title(__('owner.gallery.notifications.deleted'))
            ->duration(2000)
            ->send();
    }
}
